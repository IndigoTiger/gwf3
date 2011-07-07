<?php

/**
 * @author gizmore
 * Main PM Functionality / Navigation
 */
final class PM_Overview extends GWF_Method
{
	##################
	### GWF_Method ###
	##################
	public function getHTAccess(GWF_Module $module)
	{
		return
			'RewriteRule ^pm$ index.php?mo=PM&me=Overview&by=pm_date&dir=DESC&page=1'.PHP_EOL.
			'RewriteRule ^pm/folder/(\d+)/[^/]+/by/page-([0-9]+)$ index.php?mo=PM&me=Overview&folder=$1&page=$2'.PHP_EOL.
			'RewriteRule ^pm/folder/(\d+)/[^/]+/by/([^/]+)/([ADESC,]{0,})/page-([0-9]+)$ index.php?mo=PM&me=Overview&folder=$1&by=$2&dir=$3&page=$4'.PHP_EOL.
			'RewriteRule ^pm/folders/(\d+)/[^/]+/by/([^/]+)/([ADESC,]{0,})/page-([0-9]+)$ index.php?mo=PM&me=Overview&folder=$1&fby=$2&fdir=$3&fpage=$4'.PHP_EOL;
	}
	
	public function execute(GWF_Module $module)
	{
		if (false === ($user = GWF_Session::getUser())) {
			return $this->templateGuests($module);
		}
		
		if (false !== ($error = $this->sanitize($module))) {
			return $error;
		}
		
		if (false !== (Common::getPost('newfolder'))) {
			return $this->onCreateFolder($module).$this->templateOverview($module);
		}
		
		if (false !== (Common::getPost('delete'))) {
			return $this->onDelete($module).$this->templateOverview($module);
		}
		if (false !== (Common::getPost('move'))) {
			return $this->onMove($module).$this->templateOverview($module);
		}
		
		return $this->templateOverview($module);
	}
	
	/**
	 * @var GWF_PMFolder
	 */
	private $folder;
	
	private function sanitize(Module_PM $module)
	{
		if (false === ($this->folder = GWF_PMFolder::getByID(Common::getGet('folder', GWF_PM::INBOX)))) {
			if (false === ($this->folder = GWF_PMFolder::getInBox())) {
				return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
			}
		}
		
		$this->fid = $fid = $this->folder->getID();
		$uid = GWF_Session::getUserID();
		$del = GWF_PM::OWNER_DELETED;
		$conditions = "pm_owner=$uid AND pm_folder=$fid AND pm_options&$del=0";
		$pmTable = GDO::table('GWF_PM');
		$this->ipp = $module->cfgPMPerPage();
		$this->nItems = $pmTable->countRows($conditions);
		$this->nPages = GWF_PageMenu::getPagecount($this->ipp, $this->nItems);
		$this->page = Common::clamp(intval(Common::getGet('page')), 1, $this->nPages);
		$this->orderby = $pmTable->getMultiOrderby(Common::getGet('by'), Common::getGet('dir'));
		$this->pms = $pmTable->selectObjects('*', $conditions, $this->orderby, $this->ipp, GWF_PageMenu::getFrom($this->page, $this->ipp));
		return false;
	}
	
	##############
	### Guests ###
	##############
	private function templateGuests(Module_PM $module)
	{
		GWF_Website::setPageTitle($module->lang('pt_guest'));
		
		$tVars = array(
			'new_pm' => $this->getNewPMFormGuest($module),
		);
		return $module->templatePHP('overview_guests.php', $tVars);
	}
	
	################
	### Sanitize ###
	################
	private function templateOverview(Module_PM $module)
	{
		GWF_Website::setPageTitle($module->lang('pt_pm'));
		
		$fname = $this->folder->urlencodeSEO('pmf_name');
		$hrefPage = GWF_WEB_ROOT.sprintf('pm/folder/%s/%s/by/%s/%s/page-%%PAGE%%', $this->fid, $fname, urlencode(Common::getGet('by')), urlencode(Common::getGet('dir')));
		$hrefSort = GWF_WEB_ROOT.'pm/folder/'.$this->fid.'/'.$fname.'/by/%BY%/%DIR%/page-1';
		
		$tVars = array(
			'folder' => $this->folder,
			'folders' => $this->folderTable($module),
			'form_new_folder' => $this->getFormNewFolder($module)->templateX($module->lang('ft_new_folder')),
		
//			'pms' => $this->pmTable($module),
			'pms' => $this->pms,
			'pagemenu' => GWF_PageMenu::display($this->page, $this->nPages, $hrefPage),
			'sort_url' => $hrefSort,
			'new_pm' => $this->getNewPMForm($module),
			'href_options' => $module->getOptionsHREF(),
			'href_search' => $module->getSearchHREF(),
			'folder_select' => GWF_PMFolder::getSelectS($module, Common::getRequest('folders', '0')),
			'form_action' => isset($_SERVER['REQUEST_URI']) ? GWF_HTML::display($_SERVER['REQUEST_URI']) : GWF_WEB_ROOT.'pm',
			'href_trashcan' => $module->getMethodURL('Trashcan&by=pm_date&dir=DESC'),
		);
		return $module->templatePHP('overview.php', $tVars);
	}

	public function folderTable(Module_PM $module)
	{
		$folders = GWF_PMFolder::getFolders(GWF_Session::getUserID());
		
		$uid = GWF_Session::getUserID();
		$conditions = "pmf_uid=$uid";
		$orderby = GDO::table('GWF_PMFolder')->getMultiOrderby(Common::getGet('fby', 'pmf_name'), Common::getGet('fdir'));
		$folders = array_merge(
			GWF_PMFolder::getDefaultFolders(),
			GDO::table('GWF_PMFolder')->selectObjects('*', $conditions, $orderby)
		);
		
		$tVars = array(
			'orderby' => $orderby,
			'folders' => $folders,
			'sort_url' => '',
			'folder_action' => $module->getMethodURL('FolderAction'),
		);
		return $module->templatePHP('folders.php', $tVars);
	}
	
	private function getFormNewFolder(Module_PM $module)
	{
		$data = array(
			'foldername' => array(GWF_Form::STRING, '', $module->lang('th_pmf_name')),
			'newfolder' => array(GWF_Form::SUBMIT, $module->lang('btn_new_folder')),
		);
		return new GWF_Form($this, $data);
	}
	
	private function pmTable(Module_PM $module)
	{
		$uid = GWF_Session::getUserID();
		$fid = $this->folder->getID();
		$conditions = "(pm_from=$uid AND pm_from_folder=$fid) OR (pm_to=$uid AND pm_to_folder=$fid)";
		$sortURL = GWF_WEB_ROOT.'pm/folder/'.$fid.'/'.$this->folder->urlencodeSEO('pmf_name').'/by/%BY%/%DIR%/page-1';
		return GWF_Table::displayGDO2($module, GDO::table('GWF_PM'), GWF_Session::getUser(), $sortURL, $conditions, $module->cfgPMPerPage());
	}
	
	private function getNewPMForm(Module_PM $module)
	{
		$data = array(
			#'username_sel' => array(GWF_Form::SELECT, $this->getUsernameSelect($module, $this->getCorrespondence(), 'username_sel')),
			'username' => array(GWF_Form::STRING, ''),
			'create' => array(GWF_Form::SUBMIT, $module->lang('btn_create')),
		);
		$form = new GWF_Form($this, $data);
		return $form->templateX($module->lang('ft_new_pm'), GWF_PM::getNewPMHref());
	}

	private function getNewPMFormGuest(Module_PM $module)
	{
		$data = array(
			'username_sel' => array(GWF_Form::SELECT, $this->getUsernameSelect($module, $this->getUsernamesPPM(), 'username_sel')),
			'create' => array(GWF_Form::SUBMIT, $module->lang('btn_create')),
			'username' => array(GWF_Form::STRING),
		);
		$form = new GWF_Form($this, $data);
		return $form->templateX($module->lang('ft_new_pm'), GWF_PM::getNewPMHref());
	}

	private function getCorrespondence()
	{
		if (false === ($user = GWF_Session::getUser())) {
			return array();
		}
		$uid = $user->getID();
		$back = array_merge(
			GDO::table('GWF_PM')->selectColumn('T_A.user_name', "pm_to=$uid OR pm_from=$uid", 'T_A.user_name ASC'),
			GDO::table('GWF_PM')->selectColumn('T_B.user_name', "pm_to=$uid OR pm_from=$uid", 'T_B.user_name ASC')
		);
		$uname = $user->getVar('user_name');
		if (false !== ($i = array_search($uname, $back))) {
			unset($back[$i]);
		}
		return $back;
	}
	
	private function getUsernamesPPM()
	{
		$ppm = GWF_PMOptions::ALLOW_GUEST_PM;
		return GDO::table('GWF_PMOptions')->selectColumn('user_name', "pmo_options&$ppm", "user_name DESC", array('pmo_user'));
	}

	private function getUsernameSelect(Module_PM $module, array $usernames, $name='username')
	{
		$back = sprintf('<select name="%s">', $name);
		$back .= sprintf('<option value="0">%s</option>', $module->lang('sel_username'));
		foreach ($usernames as $username)
		{
			$username = GWF_HTML::display($username);
			$back .= sprintf('<option value="%s">%s</option>', $username, $username);
		}
		$back .= '</select>';
		return $back;
	}
	
	##############
	### Delete ###
	##############
	private function onDelete(Module_PM $module)
	{
		$ids = Common::getPost('pm');
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
			if (false === $pm->markDeleted($user)) {
				continue;
			}
			$count++;
		}
		
		$this->sanitize($module);
		
		return $module->message('msg_deleted', array($count));
	}
	
	##################
	### New Folder ###
	##################
	public function validate_foldername(Module_PM $module, $arg) { return $module->validate_foldername($arg); }
	public function onCreateFolder(Module_PM $module)
	{
		$form = $this->getFormNewFolder($module);
		if (false !== ($error = $form->validate($module))) {
			return $error;
		}
		
		$userid = GWF_Session::getUserID();
		
		$folders = GWF_PMFolder::getFolders($userid);
		if (count($folders) >= $module->cfgMaxFolders()) {
			return $module->error('err_max_folders');
		}
		
		if (false === ($folder = GWF_PMFolder::insertFolder($userid, $form->getVar('foldername')))) {
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		return '';
	}
	
	############
	### Move ###
	############
	private function onMove(Module_PM $module, $ids=NULL)
	{
		$ids = Common::getPost('pm');
		if (!(is_array($ids))) {
			return '';
		}
		
		$user = GWF_Session::getUser();
		
		if (false === ($folder = GWF_PMFolder::getByID(Common::getPost('folders')))) {
			return $module->error('err_folder');
		}
		
		if ($folder->getVar('pmf_uid') !== $user->getID()) {
			return $module->error('err_folder');
		}
		
		$count = 0;
		foreach ($ids as $id => $stub)
		{
			if (false === ($pm = GWF_PM::getByID($id))) {
				continue;
			}
			if (false === ($pm->canRead($user))) {
				continue;
			}
			if (false === $pm->move($user, $folder)) {
				continue;
			}
			$count++;
		}
		
		$this->sanitize($module);
		return $module->message('msg_moved', array($count));
	}
	
}

?>