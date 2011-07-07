<?php
/**
 * Mibbit chat page. Fullscreen possible.
 * @author gizmore
 */
final class Chat_Mibbit extends GWF_Method
{
	public function getHTAccess(GWF_Module $module)
	{
		return
			'RewriteRule ^irc_chat/?$ index.php?mo=Chat&me=Mibbit'.PHP_EOL.
			'RewriteRule ^irc_chat_fullscreen/?$ index.php?mo=Chat&me=Mibbit&fullscreen=yes'.PHP_EOL;
	}
	
	public function execute(GWF_Module $module)
	{
		return $this->templateMibbit($module);
	}
	
	private function templateMibbit(Module_Chat $module)
	{
		if (!$module->cfgMibbit()) {
			return GWF_HTML::err('ERR_MODULE_DISABLED', array( 'Chat_Mibbit'));
		}
		
		GWF_Website::setPageTitle($module->lang('pt_irc_chat'));
		GWF_Website::setMetaTags($module->lang('mt_irc_chat'));
		GWF_Website::setMetaDescr($module->lang('md_irc_chat'));
		
		$tVars = array(
			'href_webchat' => GWF_WEB_ROOT.'chat',
			'href_ircchat' => GWF_WEB_ROOT.'irc_chat',
			'href_ircchat_full' => GWF_WEB_ROOT.'irc_chat_fullscreen',
			'mibbit_url' => $module->cfgMibbitURL(),
			'mibbit' => $module->cfgMibbit(),
			'gwf_chat' => $module->cfgGWFChat(),
		);
		return $module->templatePHP('mibbit.php', $tVars);
	}	
}
?>