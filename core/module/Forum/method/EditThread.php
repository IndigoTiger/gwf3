<?php

final class Forum_EditThread extends GWF_Method
{
	public function isLoginRequired() { return true; }
	
	public function getHTAccess(GWF_Module $module)
	{
		return
			'RewriteRule ^forum/edit/thread/([0-9]+)/[^/]+$ index.php?mo=Forum&me=EditThread&tid=$1'.PHP_EOL;
	}
	
	/**
	 * @var GWF_ForumThread
	 */
	private $thread;
	
	public function execute(GWF_Module $module)
	{
		if (false === ($this->thread = $module->getCurrentThread())) {
			return $module->error('err_thread');
		}
		
		if (!$this->thread->hasEditPermission(GWF_Session::getUser())) {
			return GWF_HTML::err('ERR_NO_PERMISSION');
		}
		
		require_once GWF_CORE_PATH.'module/Forum/GWF_ForumBoardSelect.php';
		
		if (false !== Common::getPost('edit')) {
			return $this->onEdit($module).$this->templateEditThread($module);
		}
		
		if (false !== Common::getPost('delete')) {
			return $this->onDelete($module);
		}
		
		return $this->templateEditThread($module);
	}
	
	private function getForm(Module_Forum $module)
	{
		$t = $this->thread;
		$data = array(
			'sticky' => array(GWF_Form::CHECKBOX, $t->isSticky(), $module->lang('th_sticky')),
			'hidden' => array(GWF_Form::CHECKBOX, $t->isHidden(), $module->lang('th_hidden')),
			'closed' => array(GWF_Form::CHECKBOX, $t->isClosed(), $module->lang('th_closed')),
			'guest_view' => array(GWF_Form::CHECKBOX, $t->isGuestView(), $module->lang('th_guest_view')),
			'invisible' => array(GWF_Form::CHECKBOX, $t->isInvisible(), $module->lang('th_invisble')),
			'title' => array(GWF_Form::STRING, $t->getVar('thread_title'), $module->lang('th_title')), 
			'merge' => array(GWF_Form::SELECT, $t->getMergeSelect('merge'), $module->lang('th_merge')),
//			'move' => array(GWF_Form::GDO, $t->getBoardID(), $module->lang('th_board'), 0, 'GWF_ForumBoard'),
			'move' => array(GWF_Form::SELECT, GWF_ForumBoardSelect::single('move', $t->getBoardID()), $module->lang('th_board')),
			'edit' => array(GWF_Form::SUBMIT, $module->lang('btn_edit')),
			'delete' => array(GWF_Form::SUBMIT, $module->lang('th_delete')),
		);
		return new GWF_Form($this, $data);
	}
	
	private function templateEditThread(Module_Forum $module)
	{
		$form = $this->getForm($module);
		$tVars = array(
			'thread' => $this->thread,
			'href_add_poll' => $this->thread->hrefAddPoll(),
			'form' => $form->templateY($module->lang('ft_edit_thread')),
		);
		return $module->templatePHP('edit_thread.php', $tVars);
	}

	##################
	### Validators ###
	##################
	public function validate_title(Module_Forum $module, $arg) { return $module->validate_title($arg); }
	public function validate_merge(Module_Forum $module, $arg) { return $module->validate_thread($arg); }
	public function validate_move(Module_Forum $module, $arg) { return $module->validate_parentid($arg); }
	
	private function onEdit(Module_Forum $module)
	{
		$t = $this->thread;
		$form = $this->getForm($module);
		
		if (false !== ($error = $form->validate($module))) {
			return $error;
		}
		
		$t->saveOption(GWF_ForumThread::HIDDEN, isset($_POST['hidden']));
		$t->saveOption(GWF_ForumThread::STICKY, isset($_POST['sticky']));
		$t->saveOption(GWF_ForumThread::CLOSED, isset($_POST['closed']));
		$t->saveOption(GWF_ForumThread::GUEST_VIEW, isset($_POST['guest_view']));
		$t->saveOption(GWF_ForumThread::INVISIBLE, isset($_POST['invisible']));
		$t->saveVars(array(
			'thread_title' => $form->getVar('title'),
		));
		
		if ($t->getBoardID() !== ($bid = $form->getVar('move'))) {
//			var_dump($bid);
			return $this->onMove($module, $t, GWF_ForumBoard::getBoard($bid));
		}
		else if ($t->getID() !== ($tid = intval($form->getVar('merge')))) {
			return $this->onMerge($module, $t, GWF_ForumThread::getThread($tid));
		}
		
		return $module->message('msg_edited_thread');
	}
	
	private function onDelete(Module_Forum $module)
	{
		$t = $this->thread;
		$form = $this->getForm($module);
		
		if (false !== ($error = $form->validate($module))) {
			return $error.$this->templateEditThread($module);
		}
		
		if (false === $t->deleteThread()) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__)).$this->templateEditThread($module);
		}
		
		$module->cachePostcount();
		
		return $module->message('msg_thread_deleted');
	}
	
	private function onMove(Module_Forum $module, GWF_ForumThread $t, GWF_ForumBoard $b)
	{
//		if (false === ($b->isThreadAllowed())) {
//			$_POST['move'] = $t->getBoardID();
//			return $module->error('err_no_thread_allowed');
//		}
		
		$pc = $t->getPostCount();
		
		if (false === $t->getBoard()->adjustCounters(-1, -$pc)) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		if (false === $t->saveVar('thread_bid', $b->getID())) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		if (false === $b->adjustCounters(1, $pc)) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		return $module->message('msg_thread_moved', array($t->display('thread_title'), $b->display('board_title')));
	}

	private function onMerge(Module_Forum $module, GWF_ForumThread $first, GWF_ForumThread $last)
	{
		// Delete thread
		if (false === $last->delete()) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}

		// Sum counters
		if (false === ($first->saveVar('thread_postcount', $first->getPostCount()+$last->getPostCount()))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		if (false === ($first->saveVar('thread_viewcount', $first->getVar('thread_viewcount')+$last->getVar('thread_viewcount')))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		if (false === ($first->saveVar('thread_thanks', $first->getVar('thread_thanks')+$last->getVar('thread_thanks')))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		if (false === ($first->saveVar('thread_votes_up', $first->getVar('thread_votes_up')+$last->getVar('thread_votes_up')))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		if (false === ($first->saveVar('thread_votes_down', $first->getVar('thread_votes_down')+$last->getVar('thread_votes_down')))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		// -1 thread
		if (false === $first->getBoard()->adjustCounters(-1, 0))  {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		// change threadid's
		$t1id = $first->getID();
		$t2id = $last->getID();
		if (false === (GDO::table('GWF_ForumPost')->update("post_tid=$t1id", "post_tid=$t2id"))) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		// Done
		return $module->message('msg_merged');
	}
}

?>