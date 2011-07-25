<?php

$lang = array(

	'ERR_DATABASE' => 'Adatbázis hiba a(z) %1% állomány %2% sorában.',
	'ERR_FILE_NOT_FOUND' => 'Az állomány nem található: %1%',
	'ERR_MODULE_DISABLED' => 'A(z) %1% modul jelenleg le van tiltva.',
	'ERR_LOGIN_REQUIRED' => 'Ehhez a művelethez előbb jelentkezz be.',
	'ERR_NO_PERMISSION' => 'Hozzáférés megtagadva.',
	'ERR_WRONG_CAPTCHA' => 'Helyesen kell megadnod az alábbi képen szereplő betűket.',
	'ERR_MODULE_MISSING' => 'A(z) %1% modul nem található.',
	'ERR_COOKIES_REQUIRED' => 'A munkamenet lejárt vagy engedélyezned kell a sütiket a bőngésződben.<br/>Kérlek frissítsd az oldalt.',
	'ERR_UNKNOWN_USER' => 'Ismeretlen felhasználó.',
	'ERR_UNKNOWN_GROUP' => 'Ismeretlen csoport.',
	'ERR_UNKNOWN_COUNTRY' => 'Ismeretlen ország.',
	'ERR_UNKNOWN_LANGUAGE' => 'Ismeretlen nyelv.',
	'ERR_METHOD_MISSING' => 'Ismeretlen metódus: %1% a(z) %2% modulban.',
	'ERR_GENERAL' => 'Nem meghatározott hiba: %1% a(z) %2% sorban.',
	'ERR_WRITE_FILE' => 'Nem írható az állomány: %1%.',
	'ERR_CLASS_NOT_FOUND' => 'Ismeretlen osztály: %1%.',
	'ERR_MISSING_VAR' => 'Hiányzó HTTP POST változó: %1%.',
	'ERR_MISSING_UPLOAD' => 'Fel kell töltened egy állományt.',
	'ERR_MAIL_SENT' => 'Hiba történt az elektronikus levél küldésekor.',
	'ERR_CSRF' => 'Hibás CSRF token. Vagy kétszer próbáltál adatot küldeni, vagy lejárt a munkameneted időközben.',
	'ERR_HOOK' => 'A hook hamissal tért vissza: %1%.',
	'ERR_PARAMETER' => 'Hibás  in %1% line %2%. Function argument %3% is invalid.',
	'ERR_DEPENDENCY' => 'Nem feloldható függőség: modulhoz/%1%/művelethez/%2% szükséges a(z) %3% v%4% modul.',
	'ERR_SEARCH_TERM' => 'A keresési feltétel legalább mimimum %1% maximum %2% karakter hosszú lehet.',
	'ERR_SEARCH_NO_MATCH' => 'A keresésed, &quot;%1%&quot; nem talált egyezést.',
	'ERR_POST_VAR' => 'Váratlan POST változó: %1%.',

	# GWF_Time
	'unit_sec_s' => 'm',
	'unit_min_s' => 'p',
	'unit_hour_s' => 'ó',
	'unit_day_s' => 'n',
	'unit_month_s' => 'h',
	'unit_year_s' => 'é',

	'M1' => 'Január',
	'M2' => 'Február',
	'M3' => 'Március',
	'M4' => 'Április',
	'M5' => 'Május',
	'M6' => 'Június',
	'M7' => 'Július',
	'M8' => 'Augusztus',
	'M9' => 'Szeptember',
	'M10' => 'Október',
	'M11' => 'November',
	'M12' => 'December',

	'm1' => 'Jan',
	'm2' => 'Feb',
	'm3' => 'Mar',
	'm4' => 'Apr',
	'm5' => 'Maj',
	'm6' => 'Jun',
	'm7' => 'Jul',
	'm8' => 'Aug',
	'm9' => 'Sep',
	'm10' => 'Okt',
	'm11' => 'Nov',
	'm12' => 'Dec',

	'D0' => 'Vasárnap',
	'D1' => 'Hétfő',
	'D2' => 'Kedd',
	'D3' => 'Szerda',
	'D4' => 'Csütörtök',
	'D5' => 'Péntek',
	'D6' => 'Szombat',

	'd0' => 'Vas',
	'd1' => 'Hét',
	'd2' => 'Ked',
	'd3' => 'Sze',
	'd4' => 'Csü',
	'd5' => 'Pén',
	'd6' => 'Szo',

	'ago_s' => '%1% másodperce',
	'ago_m' => '%1% perce',
	'ago_h' => '%1% órája',
	'ago_d' => '%1% napja',

	###
	### TODO: GWF_DateFormat, is problematic, because en != en [us/gb]
	###
	### Here you have to specify how a default dateformats looks for different languages.
	### You have the following substitutes:
	### Year:   Y=1990, y=90
	### Month:  m=01,   n=1,  M=January, N=Jan
	### Day:    d=01,   j=1,  l=Tuesday, D=Tue
	### Hour:   H:23    h=11
	### Minute: i:59
	### Second: s:59
	'df4' => 'Y', # 2009
	'df6' => 'M Y', # January 2009
	'df8' => 'D, M j, Y', # Tue, January 9, 2009
	'df10' => 'M d, Y - H:00', # January 09, 2009 - 23:00
	'df12' => 'M d, Y - H:i',  # January 09, 2009 - 23:59
	'df14' => 'M d, Y - H:i:s',# January 09, 2009 - 23:59:59

	'datecache' => array(
		array('Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec'),
		array('Január','Február','Március','Április','Május','Június','Július','Augusztus','Szeptember','Október','November','December'),
		array('Vas','Hét','Ked','Sze','Csü','Pén','Szo'),
		array('Vasárnap','Hétfő','Kedd','Szerda','Csütörtök','Péntek','Szombat'),
		array(4=>'Y', 6=>'M Y', 8=>'D, M j, Y', 10=>'M d, Y - H:00', 12=>'M d, Y - H:i', 14=>'M d, Y - H:i:s'),
	),

	# GWF_Form
	'th_captcha1' => '<a href="http://hu.wikipedia.org/wiki/Captcha">Captcha</a>',
	'th_captcha2' => 'Pötyögd be az 5 betűt a CAPTCHA képről',
	'tt_password' => 'A jelszavaknak legalább 8 karakter hosszúnak kell lennie.',
	'tt_captcha1' => 'A kattints a CAPTCHA képre, hogy egy új képet igényelhess.',
	'tt_captcha2' => 'Pötyögd be újra, hogy meggyőződhessünk arról, valóban humanoid vagy.',

	# GWF_Category
	'no_category' => 'Összes kategória',
	'sel_category' => 'Válassz egy kategóriát',

	# GWF_Language
	'sel_language' => 'Válassz egy nyelvet',
	'unknown_lang' => 'Ismeretlen nyelv',

	# GWF_Country
	'sel_country' => 'Válassz egy országot',
	'unknown_country' => 'Ismeretlen ország',
	'alt_flag' => '%1%',

	# GWF_User#gender
	'gender_male' => 'Férfi',
	'gender_female' => 'Nő',
	'gender_no_gender' => 'Ismeretlen nem',

	# GWF_User#avatar
	'alt_avatar' => '%1% Avatárja',

	# GWF_Group
	'sel_group' => 'Válassz egy felhasználói csoportot',

	# Date select
	'sel_year' => 'Válassz évet',
	'sel_month' => 'Válassz hónapot',
	'sel_day' => 'Válassz napot',
	'sel_older' => 'Idősebb, mint',
	'sel_younger' => 'Fiatalabb, mint',

	### General Bits! ###
	'guest' => 'Vendég',
	'unknown' => 'Ismeretlen',
	'never' => 'Soha',
	'search' => 'Keresés',
	'term' => 'Kifejezés',
	'by' => 'által',
	'and' => 'és',

	'alt_flag' => '%1% zászló',


	# v2.01 (copyright)
	'copy' => '&copy; %1% '.GWF_SITENAME.'. Minden jog fenntartva.',
	'copygwf' => 'A '.GWF_SITENAME.' <a href="http://gwf.gizmore.org">GWF</a>, BSD licenszhez hasonló Weboldal Keretrendszert használ.',

	# v2.02 (recaptcha+required_fields)
	'form_required' => '%1% jel azt jelent, kötelező mező.',

	# v2.03 BBCode
	'bbhelp_b' => 'félkövér',
	'bbhelp_i' => 'dőlt',
	'bbhelp_u' => 'aláhúzott',
	'bbhelp_code' => 'Ide jön a kód',
	'bbhelp_quote' => 'A szöveg itt egy idézet',
	'bbhelp_url' => 'Szöveg linkelése',
	'bbhelp_email' => 'Email link',
	'bbhelp_noparse' => 'BB-decode tiltása itt.',
	'bbhelp_level' => 'Szöveg, amely csak bizonyos felhasználói szint felett nézhető meg.',
	'bbhelp_spoiler' => 'Láthatatlan szöveg, ami klikkelésre kinyílik.',

	# v2.04 BBCode3
	'quote_from' => 'Idézet tőle: %1%',
	'code' => 'kód',
	'for' => 'részére',
	
	# 2.05 Bits
	'yes' => 'Igen',
	'no' => 'Nem',

	# 2.06 spoiler
	'bbspoiler_info' => 'Click for spoiler',

	# 3.00 Filesize
	'filesize' => array('B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB', 'ZB'),
	'err_bb_level' => 'You need a userlevel of %1% to see this content.',
);

?>