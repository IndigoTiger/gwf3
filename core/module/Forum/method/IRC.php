<?php
final class Forum_IRC extends GWF_Method
{
	public function getHTAccess(GWF_Module $module)
	{
		return
			'RewriteCond %{QUERY_STRING} datestamp=([^&]+)&limit=([^&]+)'.PHP_EOL.
			'RewriteRule ^nimda_forum.php$ index.php?mo=Forum&me=IRC&datestamp=%1&limit=%2&no_session=true&ajax=true'.PHP_EOL;
	}

	public function execute(GWF_Module $module)
	{
		GWF_Website::plaintext();
		
		if (false === ($datestamp = Common::getGet('datestamp'))) {
			return 'TRY ?datestamp=YYYYMMDDHHIISS&limit=5';
		}
		
		if (strlen($datestamp) !== 14) {
			return 'TRY ?datestamp=YYYYMMDDHHIISS&limit=5';
		}
		
		if (0 === ($limit = Common::getGetInt('limit', 0))) {
			return 'TRY ?datestamp=YYYYMMDDHHIISS&limit=5';
		}
		
		$date = GDO::escape($datestamp);
		
		$limit = Common::clamp($limit, 1, 25);
		
		if (false === ($result = GDO::table('GWF_ForumThread')->selectObjects('*', "thread_lastdate>='$date' AND thread_options&4=0", 'thread_lastdate DESC', $limit))) {
			return GWF_HTML::lang('ERR_DATABASE', __FILE__, __LINE__);
		}
		
		$back = '';
		
		$unknown = GWF_HTML::lang('unknown');
		
		foreach (array_reverse($result) as $thread)
		{
			#timestamp::lock::postid::threadid::posturl::userurl::username::title
			$thread instanceof GWF_ForumThread;
			$locked = $thread->getVar('thread_gid') === '0' ? '0' : '1';
			$back .= $thread->getVar('thread_tid');
			$back .= '::';
			$back .= $thread->getVar('thread_lastdate');
			$back .= '::';
			$back .= $thread->getVar('thread_gid');
			$back .= '::';
			$back .= GWF_DOMAIN.$thread->getLastPageHREF($locked==='1');
			$back .= '::';
			$back .= $locked === '1' ? $unknown : $thread->getVar('thread_lastposter');
			$back .= '::';
			$back .= $locked === '1' ? $unknown : $thread->getVar('thread_title');
			$back .= PHP_EOL;
		}	
		return $back;
	}
}
?>