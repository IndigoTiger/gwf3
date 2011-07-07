<?php
/**
 * This module is the SpaceFramework::init.
 * It's the API to communicate to all SF-Classes.
 * @author SpaceOne
 * @copyright Florian Best
 * @version 1.01
 * @since 10.05.2011
 * @visit www.florianbest.de
 * @license none
 */
final class Module_SF extends GWF_Module
{
	public function getVersion() { return 1.01; }
	public function getDefaultPriority() { return 50; }
	public function getDefaultAutoLoad() { return defined('GWF_SF') ? true : false; }
	public function getClasses() { 
		$classes = array('SF', 'SF_Navigation'); 
//		if($this->cfgShellIsEnabled()) {
//			$classes[] = 'Shellfunctions';
//		}
		return $classes;
	}
	public function onLoadLanguage() { return $this->loadLanguage('lang/SF'); }
	public function getAdminSectionURL() { return $this->getMethodURL('Config'); }
	public function getShellPath() { return htmlspecialchars($_SERVER['SCRIPT_NAME']); }
	public function onStartup() { 
		if(!isset($_GET['mo'])) $_GET['mo'] = GWF_DEFAULT_MODULE;
		if(!isset($_GET['me'])) $_GET['me'] = GWF_DEFAULT_METHOD;
	
		if(defined('GWF_SF')) {
			$this->onInclude();
			require_once 'method/color.php';
		}
	}
	public function onInstall($dropTable)
	{
		return GWF_ModuleLoader::installVars($this, array(
			'default_layout' => array('space', 'text', '0', '11'),
			'default_design' => array('SF', 'text', '0', '11'),
			'default_color' => array('green', 'text', '0', '11'),
			'shell_is_enabled' => array(true, 'bool'),
		));
	}
	##############
	### Config ###
	##############
	public function cfgdefaultLayout() { return $this->getModuleVar('default_layout', 'space'); }
	public function cfgdefaultDesign() { return $this->getModuleVar('default_design', 'SF'); }
	public function cfgdefaultColor() { return $this->getModuleVar('default_color', 'green'); }
	public function cfgCookieTime() { return (time()+60*60*24*30); }
	public function cfgShellIsEnabled() { return $this->getModuleVar('shell_is_enabled', true); }
}

?>