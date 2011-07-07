<?php
final class Lamb_SLHelp extends GWF_Method
{
	public function getHTAccess(GWF_Module $module)
	{
		return 'RewriteRule ^shadowhelp/?$ index.php?mo=Lamb&me=SLHelp'.PHP_EOL;
	}
	
	public function execute(GWF_Module $module)
	{
		return $this->templateShadowhelp($module);		
	}
	
	private function templateShadowhelp(Module_Lamb $module)
	{
		$tVars = array(
		);
		return $module->template('shadowhelp.php', $tVars);
	}
}
?>