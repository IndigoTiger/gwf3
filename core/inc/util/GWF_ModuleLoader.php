<?php
final class GWF_ModuleLoader
{
	############
	### Sort ###
	############
	/**
	 * Sort an array of modules. The module array is a reference. Additionally Returns the same sorted array.
	 * @param array $modules
	 * @param string $by
	 * @param string $dir
	 */
	public static function sortModules(array &$modules, $by, $dir)
	{
		uasort($modules, array(__CLASS__, 'sort_'.$by.'_'.$dir));
		return $modules;
	}
	public static function sort_module_priority_ASC($a, $b) { return $a->getPriority() - $b->getPriority(); }
	public static function sort_module_priority_DESC($a, $b) { return $b->getPriority() - $a->getPriority(); }
	public static function sort_module_name_ASC($a, $b) { return strcasecmp($a->getName(), $b->getName()); }
	public static function sort_module_name_DESC($a, $b) { return strcasecmp($b->getName(), $a->getName()); }
	
	############
	### Vars ###
	############
	public static function getModuleVars($module_id)
	{
		return GDO::table('GWF_ModuleVar')->selectAll('mv_key, mv_val, mv_value, mv_type, mv_min, mv_max', 'mv_mid='.intval($module_id));
	}
	
	public static function saveModuleVar(GWF_Module $module, $key, $value)
	{
		if (false === ($mv = GDO::table('GWF_ModuleVar')->getRow($module->getID(), $key))) {
			return false;
		}
		if (false === ($val = self::getVarValueMV($value, $mv))) {
			return false;
		}
		return $mv->saveVars(array(
			'mv_val' => $val,
			'mv_value' => $value,
		));
	}
	
	public static function getVarValueMV($value, GWF_ModuleVar $mv)
	{
		return self::getVarValue($value, $mv->getVar('mv_type'), $mv->getVar('mv_min'), $mv->getVar('mv_max'), $exceed);
	}
	
	
	###############
	### Load FS ###
	###############
	public static function loadModulesFS()
	{
		if (false == ($files = @scandir('module'))) {
			echo GWF_HTML::err('ERR_FILE_NOT_FOUND', array('module'));
			return false;
		}
		$modules = array();
		foreach ($files as $name)
		{
			if (Common::startsWith($name, '.')) {
				continue;
			}
			
			if (false !== ($module = GWF_Module::getModule($name))) {
				continue;
			}
			
			elseif (false !== ($module = self::loadModuleFS($name))) {
				GWF_Module::$MODULES[$name] = $module;
			}
		}
		return GWF_Module::$MODULES;
	}
	
	public static function loadModuleFS($name)
	{
		if (isset(GWF_Module::$MODULES[$name])) {
			return GWF_Module::$MODULES[$name];
		}
		
		$modulename = "Module_$name";
		$filename = "core/module/$name/$modulename.php";
		if (!Common::isFile($filename)) {
			return false;
		}
		require_once $filename;
		
		if (!class_exists($modulename)) {
			return false;
		}
		$module = new $modulename();
		$module instanceof GWF_Module;
		
		if (false === ($module_db = GWF_Module::loadModuleDB($name))) {
			$options = 0;
			$options |= $module->getDefaultAutoLoad() ? GWF_Module::AUTOLOAD : 0;
//			$options |= $module->getDefaultEnabled() ? GWF_Module::ENABLED : 0;
			$data = array(
				'module_id' => 0,
				'module_name' => $name,
				'module_priority' => $module->getDefaultPriority(),
				'module_version' => 0.0,
				'module_options' => $options,
			);
		}
		else {
			$data = $module_db->getGDOData();
		}
		
		GWF_Module::$MODULES[$name] = $module;
		
		$module->setGDOData($data);
		$module->setOption(GWF_Module::AUTOLOAD, $module->getDefaultAutoLoad());
		$module->loadVars();
		$module->onStartup();
		return $module;
	}
	
	###############
	### Install ###
	###############
	public static function installModule(GWF_Module $module, $dropTables=false)
	{
		$module->onLoadLanguage();
		return
			self::installModuleClasses($module, $dropTables).
			self::installModuleB($module, $dropTables).
			$module->onInstall($dropTables);
	}
	
	private static function installModuleClasses(GWF_Module $module, $dropTables=false)
	{
		return self::installModuleClassesB($module, $module->getClasses(), $dropTables);
	}
	
	public static function installModuleClassesB(GWF_Module $module, array $classnames, $dropTables=false)
	{
		$name = $module->getName();
		$back = '';
		foreach ($classnames as $classname)
		{
			require_once "core/module/$name/$classname.php";
			$table = GDO::table($classname);
			if ($table instanceof GDO)
			{
				if (false === GDO::table($classname)->createTable($dropTables)) {
					$back .= GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
				}
			}
		}
		return $back;
	}
	
	private static function installModuleB(GWF_Module $module, $dropTables=false)
	{
		
		if (false === $module->saveOption(GWF_Module::AUTOLOAD, $module->getDefaultAutoLoad())) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		$vdb = $module->getVersionDB();
		if ($vdb == 0)
		{
			return self::installModuleC($module, $dropTables);
		}
		else
		{
			return self::upgradeModule($module, $dropTables);
		}
	}
	
	private static function installModuleC(GWF_Module $module, $dropTables=false)
	{
		$module->setVar('module_version', $module->getVersionFS());
//		$module->setOption(GWF_Module::ENABLED, $module->getDefaultEnabled());
		if (false === $module->replace()) {
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
//		if (false === $module->saveOption(GWF_Module::ENABLED, true)) {
//			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
//		}
		return '';
	}
	
	private static function upgradeModule(GWF_Module $module, $dropTables=false)
	{
		$back = '';
		$name = $module->getName();
		$vfs = round($module->getVersionFS(), 2);
		$vdb = round($module->getVersionDB(), 2);
		
		while ($vdb < $vfs)
		{
			$vdb += 0.01;
			$back .= self::upgradeModuleStep($module, $vdb);
		}
		
//		if ($back === '') {
//			$module->saveOption(GWF_Module::ENABLED, true);
//		}
		
		return $back;
	}
	
	private static function upgradeModuleStep(GWF_Module $module, $version)
	{
		$name = $module->getName();
		$vstr = str_replace('.', '_', sprintf('%.02f', $version));
		$path = sprintf('core/module/%s/Upgrade_%s_%s.php', $name, $name, $vstr);

		if (Common::isFile($path))
		{
			require_once $path;
			$func = sprintf('Upgrade_%s_%s', $name, $vstr);
			if (!function_exists($func)) {
				return  'Missing function in upgrade: '.$func;
			}
			
			$result = call_user_func($func, $module);
			
			if ( ($result === true) || ($result === '') || ($result === NULL))
			{
			}
			else
			{
				return $error;
			}
		}

		if (false === $module->saveVar('module_version', $version))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		
		echo GWF_HTML::message('GWF', sprintf('Upgraded module to version %.02f.', $version));
		
		return '';
	}
	
	/**
	 * Install modulevars for a module. $vars is an array of array($default_value, $type[, $min, $max]).
	 * @param GWF_Module $module
	 * @param array $vars
	 * return error message or ''
	 */
	public static function installVars(GWF_Module $module, array $vars)
	{
		$old_vars = $module->getModuleVars();
		
		$id = $module->getID();
		$var_t = GDO::table('GWF_ModuleVar');
		
		# TODO: SAFE CLEANUP
//		if (false === $var_t->deleteWhere("mv_mid=$id")) {
//			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
//		}
		
		$back = '';
		
		foreach ($vars as $key => $d)
		{
			$value = isset($old_vars[$key]) ? $old_vars[$key] : $d[0];
			$type = $d[1];
			$min = isset($d[2]) ? $d[2] : NULL;
			$max = isset($d[3]) ? $d[3] : NULL;
			
			if (false === ($val = self::getVarValue($value, $type, $min, $max))) {
				$back .= GWF_HTML::err('ERR_PARAMETER', array(__FILE__, __LINE__, '$key='.$key.', $value='.htmlspecialchars($value).', $type='.$type));
				continue;
			}
			
			if (false === $var_t->insertAssoc(array(
				'mv_mid' => $id,
				'mv_key' => $key,
				'mv_val' => $val,
				'mv_value' => $value,
				'mv_type' => $type,
				'mv_min' => $min,
				'mv_max' => $max,
			
			), true)) {
				$back .= GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
				continue;
			}
		}
		return $back;
	}
	
	public static function getVarValue($value, $type, $min, $max, &$exceed=0)
	{
		switch ($type)
		{
			case 'time':
				$value = GWF_TimeConvert::humanToSeconds($value);
				# Fallthrough
			
			case 'int':
				if (!is_numeric($value)) { return false; }
				if ( ($min !== NULL) && ($value < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && ($value > $max) ) { $exceed = 1; return false; }
				return (string)intval($value);
				
			case 'float':
				if (!is_numeric($value)) { return false; }
				if ( ($min !== NULL) && ($value < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && ($value > $max) ) { $exceed = 1; return false; }
				return (string)floatval($value);
				
			case 'text':
				if ( ($min !== NULL) && (strlen($value) < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && (strlen($value) > $max) ) { $exceed = 1; return false; }
				return $value;
				
			case 'bool':
				return self::getBoolValue($value);
				
			case 'script':
				return $value;
				
			default:
				return false;
		}
	}
	
	private static function getBoolValue($value)
	{
		if (is_numeric($value))
		{
			return $value > 0 ? '1' : '0';
		}
		
		$true = array('on', 'yes', 'true', '1', 'y', 'ja', 'si', 'oui');
		$false = array('off', 'no', 'false', '0', 'n', 'nein', 'no', 'non');
		$value = strtolower($value);
		if (in_array($value, $true, true)) {
			return '1';
		}
		elseif (in_array($value, $false, true)) {
			return '0';
		}
		else {
			return false;
		}
	}
	
	#############################
	### Write HT Config files ###
	#############################
	public static function installHTAccess(array $modules)
	{
//		if (false === self::installHTMenu($modules)) {
//			return false;
//		}
		if (false === self::installHTHooks($modules)) {
			return false;
		}
		if (false === self::installHTAccess2($modules)) {
			return false;
		}
		return true;
	}

//	public static function installHTMenu(array $modules)
//	{
//		foreach ($modules as $module)
//		{
//			$module->onAddToMenu();
//		}
//	}
	
	public static function installHTHooks(array $modules)
	{
		foreach ($modules as $module)
		{
			if ($module->isEnabled())
			{
				$module->onAddHooks();
			}
		}
		return GWF_Hook::writeHooks();
	}
	
	public static function installHTAccess2(array $modules)
	{
		$hta = '';
		foreach ($modules as $module)
		{
			$module instanceof GWF_Module;
			
			if (!$module->isEnabled()) {
				continue;
			}
			
			$hta .= '# '.$module->getName().PHP_EOL;
			$methods = self::getAllMethods($module);
			foreach ($methods as $method)
			{
				$hta .= $method->getHTAccess($module);
			}
			$hta .= PHP_EOL;
		}
		$hta = GWF_HTAccess::getHTAccess().$hta;
		return file_put_contents('.htaccess', $hta);
	}
	
	public static function getAllMethods(GWF_Module $module)
	{
		$back = array();
		$name = $module->getName();
		$path = "core/module/$name/method";
		
		if (!Common::isDir($path)) {
			return array();
		}
		
		if (false === ($dir = scandir($path))) {
			die('Cannot access '.$path.' in '.__METHOD__.' line '.__LINE__);
		}
		
		foreach ($dir as $file)
		{
			if ($file{0} === '.') {
				continue;
			}
			
			if (false === ($method = $module->getMethod(substr($file, 0, -4)))) {
				die('NO METHOD for '.$file);
			}
			$back[] = $method;
		}
		return $back;
	}
	
	### 
	
	public static function sortVarsByType(array &$vars)
	{
		uasort($vars, array(__CLASS__, 'sort_vars_type'));
		return $vars;
	}
	
	public static function sort_vars_type($a, $b)
	{
		if (0 !== ($back = strcmp($a['mv_type'], $b['mv_type']))) {
			return $back;
		}
		return strcmp($a['mv_key'], $b['mv_key']);
	}
	
	###
	
	public static function checkModuleDependencies(GWF_Module $module)
	{
		return false;
		return $error;
	}
	
	###
	
	public static function cronjobs()
	{
		GWF_Log::outputLogMessages(true);
		ob_start();
		
		$modules = self::loadModulesFS();
		foreach ($modules as $module)
		{
			$module instanceof GWF_Module;
			$module->onInclude();
			$module->onLoadLanguage();
			$module->onCronjob();
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	##
	## 
	
	public static function addColumn(GDO $gdo, $columnname)
	{
		$defs = $gdo->getColumnDefcache();
		$define = $defs[$columnname];
		return gdo_db()->createColumn($gdo->getTableName(), $columnname, $define);
	}
	
	public static function renameColumn(GDO $gdo, $old_columnname, $new_columnname)
	{
		return self::changeColumn($gdo, $old_columnname, $new_columnname);
	}
	
	public static function changeColumn(GDO $gdo, $old_columnname, $new_columnname)
	{
		$defs = $gdo->getColumnDefcache();
//		if (isset($defs[$new_columnname])) {
//			return true;
//		}
		$define = $defs[$new_columnname];
		return gdo_db()->changeColumn($gdo->getTableName(), $old_columnname, $new_columnname, $define);
	}
	
}
?>