<?php
$lang = array(

	'pt_login' => 'Kirjaudu sisään kohteeseen '.GWF_SITENAME,
	'title_login' => 'Kirjaudu sisään',
	
	'th_username' => 'Käyttäjänimi',
	'th_password' => 'Salasana',
	'th_login' => 'Kirjautuminen',
	'btn_login' => 'Kirjautuminen',

	'err_login' => 'Tuntematon käyttäjänimi',
	'err_login2' => 'Väärä salasana. Sinulla on %1% yritystä jäljellä ennen kuin sinut estetään seuraavaksi ajaksi: %2%.',
	'err_blocked' => 'Ole hyvä ja odota %1% ennen kuin yrität uudestaan.',

	'welcome' => 
		'Tervetuloa '.GWF_SITENAME.'-sivuille, %1%.<br/><br/>'.
		'Toivottavasti pidät sivuistamme ja selaaminen on hauskaa.<br/>'.
		'Mikäli sinulla on jotakin kysyttävää, älä epäröi kysyä meiltä!',

	'welcome_back' => 
		'Tervetuloa takaisin '.GWF_SITENAME.'-sivuille, %1%.<br/><br/>'.
		'Viimeisin toimintasi oli %2% IP-osoitteesta: %3%.',

	'logout_info' => 'Olet nyt kirjautunut ulos.',

	# Admin Config
	'cfg_captcha' => 'Käytä Captchaa?',	
	'cfg_max_tries' => 'Kirjautumisyritysen enimmäismäärä',	
	'cfg_try_exceed' => 'tämän ajan sisällä',

	'info_no_cookie' => 'Selaimesi ei tue keksejä (Cookies), tai ei salli niitä '.GWF_SITENAME.'-sivuilla, mutta kirjautuminen vaatii keksit',
	
	'th_bind_ip' => 'Rajoita istunto tähän IP-osoitteeseen',
	'tt_bind_ip' => 'Turvatoimi keksivarkaiden varalle.',

	'err_failures' => 'On tapahtunut %1% epäonnistunutta kirjautumista ja saatat olla epäonnistuneen tai tulevan hyökkäyksen kohde.',

	# v1.01 (login failures)
	'cfg_lf_cleanup_i' => 'Puhdista käyttäjän epäonnistumiset kirjautumisen jälkeen?',
	'cfg_lf_cleanup_t' => 'Puhdista vanhat epäonnistumiset tietyn ajan kuluttua',

	# v2.00 (login history)
	'msg_last_login' => 'Viimeisin kirjautumisesi oli %1% from %2% (%3%).<br/>Voit myös <a href="%4%">tarkistaa kirjautumishistoriasi täältä</a>.',
	'th_loghis_time' => 'Päiväys',
	'th_loghis_ip' => 'IP ',
	'th_hostname' => 'Hostname ',

	# v2.01 (clear hist)
	'ft_clear' => 'Clear login history',
	'btn_clear' => 'Clear',
	'msg_cleared' => 'Your login history has been cleared.',
	'info_cleared' => 'Your login history was last cleared at %1% from this IP: %2% / %3%',
);
?>