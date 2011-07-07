<?php

final class Admin_Users extends GWF_Method
{
	private $nPages = 1;
	private $page = 1;
	private $nUsers = 0;
	private $upp = 1;
	private $orderby = ''; #'user_regdate DESC';
	
	public function getUserGroups() { return GWF_Group::ADMIN; }
	
	public function execute(GWF_Module $module)
	{
		if (false !== ($error = $this->sanitize($module))) {
			return $error;
		}
		
		return $module->templateNav().$this->templateUserTable($module);
	}
	
	private function sanitize(Module_Admin $module)
	{
		$users = GDO::table('GWF_User');
		$this->upp = $module->cfgUsersPerPage();
		$this->nUsers = $users->countRows();
//		$this->by = $users->getWhitelistedBy(Common::getGet('by'), 'user_regdate');
//		$this->dir = $users->getWhitelistedDir(Common::getGet('dir'), 'DESC');
		$this->orderby = $users->getMultiOrderby(Common::getGet('by'), Common::getGet('dir'));
		$this->nPages = GWF_PageMenu::getPagecount($this->upp, $this->nUsers);
		$this->page = Common::clamp((int)Common::getGet('page', 1), 1, $this->nPages);
		
		return false;
	}
	
	private function templateUserTable(Module_Admin $module)
	{
//		$href = sprintf('%s/users/by/%s/%s/page-%%PAGE%%', Module_Admin::ADMIN_URL_NAME, $this->by, $this->dir);
		$href = GWF_WEB_ROOT.sprintf('index.php?mo=Admin&me=Users&by=%s&dir=%s&page=%%PAGE%%', urlencode(Common::getGet('by')), urlencode(Common::getGet('dir')));
//		$href = '';
		$tVars = array(
			'users' => $this->getUsers($module),
			'pagemenu' => GWF_PageMenu::display($this->page, $this->nPages, $href),
//			'by' => $this->by,
//			'dir' => $this->dir,
			'sort_url' => $this->getTableSortURL(),
			'search_form' => $this->getSearchForm($module)->templateX($module->lang('ft_search'), GWF_WEB_ROOT.'index.php?mo=Admin&me=UserSearch'),
		);
		return $module->templatePHP('users.php', $tVars);
	}

	private function getUsers(Module_Admin $module)
	{
		$users = GDO::table('GWF_User');
//		$by = $this->by.' '.$this->dir;
		return $users->selectObjects("*", "", $this->orderby, $this->upp, GWF_PageMenu::getFrom($this->page, $this->upp));
	}
	
	private function getTableSortURL()
	{
		return GWF_WEB_ROOT.'index.php?mo=Admin&me=Users&by=%BY%&dir=%DIR%&page=1';
	}
	
	private function getSearchForm(Module_Admin $module)
	{
		require 'UserSearch.php';
		$data = array(
			'term' => array(GWF_Form::STRING, Common::getRequest('term', ''), GWF_HTML::lang('term')),
			'search' => array(GWF_Form::SUBMIT, GWF_HTML::lang('search'), ''),
		);
		return new GWF_Form('Admin_UserSearch', $data);
	}
}

?>