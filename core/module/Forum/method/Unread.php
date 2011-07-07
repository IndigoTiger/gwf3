<?php

final class Forum_Unread extends GWF_Method
{
	public function isLoginRequired() { return true; }
	
	public function execute(GWF_Module $module)
	{
		return $this->templateUnread($module);
	}
	
	public function templateUnread(Module_Forum $module)
	{
		$by = Common::getGet('by', 'thread_lastdate');
		$dir = Common::getGet('dir', 'DESC');
		$page = intval(Common::getGet('page', 1));
		$t = GDO::table('GWF_ForumThread');
		$ipp = $module->getThreadsPerPage();
		$orderby = $t->getMultiOrderby($by, $dir);
		$conditions = GWF_ForumThread::getUnreadQuery(GWF_Session::getUser());
		$nItems = $t->countRows($conditions);
		$nPages = GWF_PageMenu::getPagecount($ipp, $nItems);
		$page = Common::clamp($page, 1, $nPages);
		$threads = $t->selectObjects('*', $conditions, $orderby, $ipp, GWF_PageMenu::getFrom($page, $ipp));
		$pmhref = $this->getMethodHref(sprintf('&by=%s&dir=%s&page=%%PAGE%%', urlencode($by), urlencode($dir)));
		$tVars = array(
			'pagemenu' => GWF_PageMenu::display($page, $nPages, $pmhref),
			'threads' => $threads,
			'board' => $module->getCurrentBoard(),
			'sort_url' => $this->getMethodHref(sprintf('&by=%%BY%%&dir=%%DIR%%&page=1')),
		);
		return $module->templatePHP('unread.php', $tVars);
	}
}

?>