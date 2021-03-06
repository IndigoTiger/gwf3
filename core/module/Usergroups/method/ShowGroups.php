<?php
final class Usergroups_ShowGroups extends GWF_Method
{
	public function isLoginRequired() { return false; }
	
	public function getHTAccess()
	{
		return 'RewriteRule ^my_groups$ index.php?mo=Usergroups&me=ShowGroups'.PHP_EOL;
	}

	public function execute()
	{
		GWF_Module::loadModuleDB('Forum',true);
		
		if (false !== ($array = Common::getPostArray('part')))
		{
			return $this->onPart($array).$this->templateGroups();
		}
		
		if (false !== ($array = Common::getPostArray('join')))
		{
			return $this->onJoin($array).$this->templateGroups();
		}
		return $this->templateGroups();
	}
	
	private function templateGroups()
	{
		$ipp = 20;
		$table = GDO::table('GWF_Group');
		$by = Common::getGet('by', '');
		$dir = Common::getGet('dir', '');
		$ug = GWF_TABLE_PREFIX.'usergroup';
		$userid = GWF_Session::getUserID();
		$visible = GWF_Group::VISIBLE_GROUP;
		$conditions = "group_founder > 0 AND ( (group_options&$visible) OR (SELECT 1 FROM $ug WHERE ug_userid=$userid AND ug_groupid=group_id) )";
		$orderby = $table->getMultiOrderby($by, $dir);
		$nItems = $table->countRows($conditions);
		$nPages = GWF_PageMenu::getPagecount($ipp, $nItems);
		$page = Common::clamp(intval(Common::getGet('page', 1)), 1, $nPages);
		$from = GWF_PageMenu::getFrom($page, $ipp);
		$href_pagemenu = GWF_WEB_ROOT.'index.php?mo=Usergroups&me=ShowGroups&by='.urlencode($by).'&dir='.urlencode($dir).'&page=%PAGE%';
		$tVars = array(
			'groups' => $table->selectObjects('*', $conditions, $orderby, $ipp, $from),
			'page_menu' => GWF_PageMenu::display($page, $nPages, $href_pagemenu),
			'sort_url' => GWF_WEB_ROOT.'index.php?mo=Usergroups&me=ShowGroups&amp;by=%BY%&dir=%DIR%&page=1',
			'form_action' => GWF_WEB_ROOT.'index.php?mo=Usergroups&me=ShowGroups&by='.urlencode($by).'&dir='.urlencode($dir).'&page='.$page,
			'href_add_group' => $this->module->getMethodURL('Create'),
			'href_edit_group' => $this->module->getMethodURL('Edit'),
		);
		return $this->module->templatePHP('groups.php', $tVars);
	}

	private function onPart($array)
	{
		if (false !== ($error = GWF_Form::validateCSRF_WeakS()))
		{
			return GWF_HTML::error('Part Group', $error);
		}
		
		$gid = key($array);
		
		if (false === ($group = GWF_Group::getByID($gid)))
		{
			return $this->module->error('err_unk_group');
		}
		
		if ($group->getFounder()->getID() === GWF_Session::getUserID())
		{
			return $this->module->error('err_kick_leader');
		}
		
		$gid = $group->getID();
		$uid = GWF_Session::getUserID();
		if (false === GDO::table('GWF_UserGroup')->deleteWhere("ug_userid={$uid} AND ug_groupid={$gid}"))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		return '';
	}
	
	private function onJoin($array)
	{
		if (false !== ($error = GWF_Form::validateCSRF_WeakS()))
		{
			return GWF_HTML::error('Join Group', $error);
		}
		if (false === ($group = GWF_Group::getByID(key($array))))
		{
			return $this->module->error('err_unk_group');
		}
		return $this->module->getMethod('Join')->onQuickJoin($group, GWF_User::getStaticOrGuest());
	}
}
?>