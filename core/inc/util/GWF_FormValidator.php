<?php
final class GWF_FormValidator
{
	private static $SKIPPERS = array(
		GWF_Form::SUBMIT,
		GWF_Form::SUBMIT_IMG,
//		GWF_Form::SUBMITS,
//		GWF_Form::SUBMIT_IMBS,
		GWF_Form::CHECKBOX,
		GWF_Form::SSTRING,
		GWF_Form::STRING_NO_CHECK,
		GWF_Form::DIVIDER,
		GWF_Form::HEADLINE,
		GWF_Form::FILE_OPT,
	);
	
	public static function validate(GWF_Module $module, GWF_Form $form, $validator)
	{
		if (false === ($errors = self::validateB($module, $form, $validator))) {
			return false;
		}
		return $errors;
	}
	
	private static function validateB(GWF_Module $module, GWF_Form $form, $validator)
	{	
		if (false !== ($error = self::validateCSRF($module, $form, $validator))) {
			return GWF_HTML::error($module->getName(), $error, false);
		}
		
		if (false !== ($errors = self::validateMissingVars($module, $form, $validator))) {
			return GWF_HTML::errorA($module->getName(), $errors, false);
		}
		
		if (false !== ($errors = self::validateVars($module, $form, $validator))) {
			return GWF_HTML::errorA($module->getName(), $errors, false);
		}
		return false;
	}
	
	private static function validateCSRF(GWF_Module $module, GWF_Form $form, $validator)
	{
		if (false === ($token = GWF_CSRF::validateToken())) {
			return GWF_HTML::lang('ERR_CSRF');
		}
		# Debug Mode
		if ($token === true) { return false; }
		
		if ($token !== $form->getCSRFToken()) {
			return GWF_HTML::lang('ERR_CSRF');
		}
		return false;
	}
	
	private static function validateCaptcha(GWF_Module $module, GWF_Form $form, $validator, $key)
	{
		if (GWF_Session::getOrDefault('php_captcha', false) !== strtoupper($_POST[$key])) {
			$form->onNewCaptcha();
			return GWF_HTML::lang('ERR_WRONG_CAPTCHA');
		}
//		GWF_Session::remove('php_captcha');
		$form->onSolvedCaptcha();
		return false;
	}
	
	private static function validateMissingVars(GWF_Module $module, GWF_Form $form, $validator)
	{
		$errors = array();
		$check_sent = $_POST;
		$check_need = array();
		
		foreach ($form->getFormData() as $key => $data)
		{
			if (in_array($data[0], self::$SKIPPERS, true)) {
				unset($check_sent[$key]);
				continue;
			}
			
			switch ($data[0])
			{
				case GWF_Form::VALIDATOR:
					break;
					
				case GWF_Form::SELECT_A:
					unset($check_sent[$key]);
					break;
				
				case GWF_Form::DATE:
				case GWF_Form::DATE_FUTURE:
					switch ($data[4])
					{
						case 14: $check_need[] = $key.'s';
						case 12: $check_need[] = $key.'i';
						case 10: $check_need[] = $key.'h';
						case 8: $check_need[] = $key.'d';
						case 6: $check_need[] = $key.'m';
						case 4: $check_need[] = $key.'y';
							break;
						default: die('Date field is invalid in form!');
					}
					break;
					
				case GWF_Form::SUBMITS:
				case GWF_Form::SUBMIT_IMGS:
						foreach (array_keys($data[1]) as $key)
						{
//							if (false !== ($i = array_search($key, $check_sent, true))) {
//								unset ($check_sent[$i]);
//							}
							unset($check_sent[$key]);

						}
					break;
					
				case GWF_Form::FILE:
					if (false === GWF_Upload::getFile($key))
					{
						$check_need[$key] = '';#$key;
					}
					break;
					
				case GWF_Form::INT:
				case GWF_Form::STRING:
					if (Common::endsWith($key, ']')) {
						$key = Common::substrUntil($key, '[');
						if (!in_array($key, $check_need)) {
							$check_need[] = $key;
						}
						break;
					}
				
				default:
					$check_need[] = $key;
					break;
			}
		}
		
		foreach ($check_need as $key)
		{
			if (!isset($check_sent[$key])) {
				$errors[] = GWF_HTML::lang('ERR_MISSING_VAR', array(htmlspecialchars($key)));
			} else {
				unset ($check_sent[$key]);
			}
		}
		
		
		foreach ($check_sent as $key => $value)
		{
			$errors[] = GWF_HTML::lang('ERR_POST_VAR', array(htmlspecialchars($key)));
		}

		return count($errors) === 0 ? false : $errors;
	}
	
	private static function validateVars(GWF_Module $module, GWF_Form $form, $validator)
	{
		$errors = array();
		foreach ($form->getFormData() as $key => $data)
		{
			# Skippers
			if ( (in_array($data[0], self::$SKIPPERS, true)) || ($data[0] === GWF_Form::SUBMITS) || ($data[0] === GWF_Form::SUBMIT_IMGS) ) {
				continue;
			}
			
			# Captcha
			if ($data[0] === GWF_Form::CAPTCHA) {
				if (false !== ($error = self::validateCaptcha($module, $form, $validator, $key))) {
					$errors[] = $error;
				}
				continue;
			}
			
			# Validators
			$func_name = 'validate_'.Common::substrUntil($key, '[', $key);
			$function = array($validator, $func_name);
			if (!method_exists($validator, $func_name)) {
				$errors[] = GWF_HTML::lang('ERR_METHOD_MISSING', array($func_name, get_class($validator)));
				continue;
			}
			if (false !== ($error = call_user_func($function, $module, $form->getVar($key)))) {
				$errors[] = $error;
			}
		}
		return count($errors) === 0 ? false : $errors;
	}
}
?>