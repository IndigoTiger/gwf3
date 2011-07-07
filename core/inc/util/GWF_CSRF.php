<?php
/**
 * Providing CSRF protection and blocking repost of userdata.
 * @author gizmore
 */
final class GWF_CSRF
{
	const DEBUG = false; # Debug mode
	const TOKEN_NAME = 'gwfcsrf'; # session and input name
	const TOKEN_ENTROPY = 8; # number of random chars
	const MAX_TOKENS = 100;
	
	/**
	 * Get HTML for hidden form input.
	 * @param $userdata
	 * @return string html
	 */
	public static function hiddenForm($userdata)
	{
		$token = self::generateToken($userdata);
		return '<input type="hidden" name="'.self::TOKEN_NAME.'" value="'.$token.'"/>';
	}
	
	/**
	 * Generate a new token in session.
	 * Returns tokenid. the session is an array of tokenid => userdata
	 * @param $userdata
	 * @return string
	 */
	public static function generateToken($userdata)
	{
		if (self::DEBUG) {
			return "disabled";
		}
		
		if (!GWF_Session::exists(self::TOKEN_NAME)) {
			GWF_Session::set(self::TOKEN_NAME, array());
		}
		
		$a = &GWF_Session::get(self::TOKEN_NAME);
		$token = Common::randomKey(self::TOKEN_ENTROPY);
		
		$a[$token] = array(time(), $userdata);
		
		self::cleanupOldTokens();
		
		return $token;
	}
	
	private static function cleanupOldTokens()
	{
		$tokens = GWF_Session::get(self::TOKEN_NAME);
		if (self::MAX_TOKENS < ($count = count($tokens)))
		{
			GWF_Session::set(self::TOKEN_NAME, array_slice($tokens, $count-self::MAX_TOKENS, self::MAX_TOKENS, true));
		}
	}
	
	/**
	 * Validate token.
	 * @return $userdata
	 * */
	public static function validateToken()
	{
		if (self::DEBUG) {
			unset($_POST[self::TOKEN_NAME]);
			return true;
		}
		
		# Post request, we check always check for csrf
		if (count($_POST) > 0)
		{
			if (false === ($token = Common::getPost(self::TOKEN_NAME)))
			{
				return false;
			}
			
			if (!GWF_Session::exists(self::TOKEN_NAME)) {
				return false;
			}
			
			
			$tokens =& GWF_Session::get(self::TOKEN_NAME);
			foreach ($tokens as $id => $d)
			{
//				echo "Check .$id. and .$token.<br/>";
				if (intval($d[0], 10) < (time() - 7200))
				{
//					echo "Deleting $id<br/>";
					unset($tokens[$id]);
				}
				else if ($id === $token)
				{
					$back = (string) $d[1];
					unset($tokens[$id]);
					unset($_POST[self::TOKEN_NAME]);
//					echo "return $back;<br/>";
					return $back;
				}
			}
			
			return false;
		}
		return true;
	}
}

?>