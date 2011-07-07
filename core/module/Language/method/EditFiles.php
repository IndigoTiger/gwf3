<?php
final class Language_EditFiles extends GWF_Method
{
	private $files = array();
	
	public function isLoginRequired() { return true; }
	
	public function execute(GWF_Module $module)
	{
		if (false !== ($filename = Common::getGet('filename'))) {
			return $this->templateFile($module, $filename);
		}
		
		if (false !== Common::getPost('save_file')) {
			return $this->onSaveFile($module);
		}
		
		return $this->templateFiles($module);
	}
	
	/**
	 * Gather the files, recursively
	 * @return unknown_type
	 */
	private function gatherFiles()
	{
		return $this->gatherFilesRec('.');
	}

	private function gatherFile($fullpath)
	{
		if ( !is_file($fullpath) || !is_readable($fullpath) ) {
			return false;
		}
		
		if (1 !== preg_match('/_([a-z]{2})\.php/', $fullpath, $matches)) {
			return false;
		}
		
		$iso = $matches[1];
		return array($fullpath, $this->isBranched($fullpath), GWF_LangFile::getByPath($fullpath, true), $iso, Common::getFile($fullpath));
	}
	
	
	private function gatherFilesRec($path)
	{
		if (false === ($dir = dir($path))) {
			echo GWF_HTML::err('ERR_FILE_NOT_FOUND', array( $path));
			return false;
		}
		
		while (false !== ($entry = $dir->read()))
		{
			if (Common::startsWith($entry, '.')) {
				continue;
			}
			
			$fullpath = $path.'/'.$entry;
			if (is_dir($fullpath)) {
				$this->gatherFilesRec($fullpath);
			}
			elseif (1===preg_match('/_([a-z]{2})\.php$/', $entry, $matches)) {
				$iso = $matches[1];
				$this->files[] = array($fullpath, $this->isBranched($fullpath), GWF_LangFile::getByPath($fullpath), $iso, $entry);
			}
			else {
//				echo "SKIP ".$fullpath;
			}
		}
		
		return true;
	}
	
	private function isBranched($fullpath)
	{
		$filename = Common::getFile($fullpath);
		return is_file($fullpath.'../'.$filename);
	}
	
	private function templateFiles(Module_Language $module)
	{
		$this->gatherFiles();
		
		$tVars = array(
			'files' => $this->files,
			'href_checker' => $module->getMethodURL('Checker'),
			'href_bundle' => $module->getMethodURL('Bundle'),
		);
		return $module->templatePHP('files.php', $tVars);
	}

	private function templateFile(Module_Language $module, $filename)
	{
		if (false === ($file = $this->gatherFile($filename))) {
			return GWF_HTML::err('ERR_FILE_NOT_FOUND', array( GWF_HTML::display($filename)));
		}
		
		$fileclass = GWF_LangFile::getByPath($file[0]);
		$form = $this->getFileForm($module, $file);
		
		$tVars = array(
			'file' => $file,
			'class' => $fileclass,
			'form' => $form->templateY($module->lang('ft_edit_file', array( GWF_HTML::display($filename)))),
		);
		return $module->templatePHP('file.php', $tVars);
	}

	private function getFileForm(Module_Language $module, array $file)
	{
		$class = $file[2];
		$data = array();
		$data['text'] = array(GWF_Form::MESSAGE, $class->getVar('lf_data'), $module->lang('th_lf_data'));
		$data['cmds'] = array(GWF_Form::SUBMITS, array('check_syntax'=>$module->lang('btn_chksyn'),'save_file'=>$module->lang('btn_edit') ) );
		return new GWF_Form($this, $data);
	}
}
?>