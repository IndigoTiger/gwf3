<?php

final class PM_Trashcan extends GWF_Method
{
	public function isLoginRequired() { return true; }
	
	public function execute(GWF_Module $module)
	{
		if (false !== ($error = $this->sanitize($module))) {
			return $error;
		}
		
		if (false !== Common::getPost('empty')) {
			return $this->onEmpty($module).$this->trashcan($module);
		}
		
		if (false !== (Common::getPost('restore'))) {
			return $this->onRestore($module, Common::getPost('pm')).$this->trashcan($module);
		}
		if (false !== ($pmid = Common::getGet('undelete'))) {
			return $this->onRestore($module, array($pmid=>'on')).$this->trashcan($module);
		}
		
		return $this->trashcan($module);
	}
	
	private function sanitize(Module_PM $module)
	{
		$pms = GDO::table('GWF_PM');
		$uid = GWF_Session::getUserID();
		$del = GWF_PM::OWNER_DELETED;
		$conditions = "(pm_owner=$uid AND pm_options&$del)";
		$nItems = $pms->countRows($conditions);
		$ipp = $module->cfgPMPerPage();
		$nPages = GWF_PageMenu::getPagecount($ipp, $nItems);
		$page = Common::clamp(intval(Common::getGet('page', 1)), 1, $nPages);
		
		$by = Common::getGet('by', '');
		$dir = Common::getGet('dir', '');
		$from = GWF_PageMenu::getFrom($page, $ipp);
		$orderby = $pms->getMultiOrderby($by, $dir);
		$this->trash = $pms->selectObjects('*', $conditions, $orderby, $ipp, $from);

		$href = $this->getMethodHref(sprintf('&by=%s&dir=%s&page=%%PAGE%%', urlencode($by), urlencode($dir)));
		$this->pagemenu = GWF_PageMenu::display($page, $nPages, $href);
		
		$this->sortURL = $this->getMethodHref(sprintf('&by=%%BY%%&dir=%%DIR%%&page=1'));
		
		return false;
	}
	
	private function trashcan(Module_PM $module)
	{
		if ($module->cfgAllowDelete()) {
			$form_empty = $this->formEmpty($module)->templateX($module->lang('ft_empty'), false);
		} else {
			$form_empty = '';
		}
		
		$tVars = array(
			'pagemenu' => $this->pagemenu,
			'pms' => $this->trash,
			'form_action' => $this->getMethodHref(),
			'sort_url' => $this->sortURL,
			'form_empty' => $form_empty,
		);
		return $module->templatePHP('trashcan.php', $tVars);
	}
	
	private function onRestore(Module_PM $module, $ids)
	{
		if (!(is_array($ids))) {
			return ''; #$module->error('err_delete');
		}
		
		$user = GWF_Session::getUser();
		$count = 0;
		foreach ($ids as $id => $stub)
		{
			if (false === ($pm = GWF_PM::getByID($id))) {
				continue;
			}
			if (false === ($pm->canRead($user))) {
				continue;
			}
			if (false === $pm->markDeleted($user, false)) {
				continue;
			}
			$count++;
		}
		
		$this->sanitize($module);
		
		return $module->message('msg_restored', array($count));
		
	}
	
	#############
	### Empty ###
	#############
	private function formEmpty(Module_PM $module)
	{
		$data = array(
			'empty' => array(GWF_Form::SUBMIT, $module->lang('btn_delete')),
		);
		return new GWF_Form($this, $data);
	}

	private function onEmpty(Module_PM $module)
	{
		if (!$module->cfgAllowDelete()) {
			return GWF_HTML::err('ERR_NO_PERMISSION');
		}

		$user = GWF_Session::getUser();
		$uid = $user->getVar('user_id');
		
		$form = $this->formEmpty($module);
		if (false !== ($error = $form->validate($module))) {
			return $error;
		}
		
		$pms = GDO::table('GWF_PM');
		$del = GWF_PM::OWNER_DELETED;
		
		if (false === ($result = $pms->deleteWhere("pm_owner=$uid AND pm_options&$del"))) {
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
//		$total = $pms->countRows("(pm_options&$todel AND pm_to=$uid) OR (pm_options&$fromdel AND pm_from=$uid)");
//		if (false === ($pms->deleteWhere("(pm_options&$bothdel=$bothdel) AND (pm_to=$uid OR pm_from=$uid)"))) {
//			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
//		}
		$deleted = $pms->affectedRows($result);
		return $module->message('msg_empty', array($deleted, $deleted, $deleted-$deleted));
	}
}

?>