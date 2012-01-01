<div class="gwf_buttons_outer">
<div class="gwf_buttons">
<?php
echo GWF_Button::generic(WC_HTML::lang('btn_site_masters_old'), GWF_WEB_ROOT.'old_site_masters', 'generic', '', $tVars['old']===true);
echo GWF_Button::generic(WC_HTML::lang('btn_site_masters'), GWF_WEB_ROOT.'site_masters', 'generic', '', $tVars['old']===false);
?>
</div>
</div>

<?php
$headers = array(
	array('', 'user_country'),
	array($tLang->lang('th_user_name'), 'user_name'),
	array($tLang->lang('th_site_name'), 'site_name'),
	array($tLang->lang('th_sitemas_firstdate'), 'sitemas_firstdate'),
	array($tLang->lang('th_sitemas_date'), 'sitemas_date'),
	array($tLang->lang('th_sitemas_tracktime')),
	array($tLang->lang('th_sitemas_startperc'), 'sitemas_startperc'),
);
if ($tVars['old'] === true) {
	$headers[] = array($tLang->lang('th_regat_onsitescore'), 'sitemas_currperc');
}

echo $tVars['page_menu'];
?>
<table>
	<?php echo GWF_Table::displayHeaders2($headers); ?>
	<?php
	foreach ($tVars['masters'] as $master)
	{
		$master instanceof WC_SiteMaster;
		$site = $master->getSite();
		$user = $master->getUser();
		echo GWF_Table::rowStart();
		echo sprintf('<td class="ri">%s</td>', $user->displayCountryFlag());
		echo sprintf('<td><a href="%s">%s</a></td>', GWF_WEB_ROOT.'profile/'.$user->urlencode('user_name'), $user->displayUsername());
//		echo sprintf('<td class="ce">%s</td>', $site->display('site_name'));
		echo sprintf('<td class="ce">%s</td>', $site->displayLink());
		echo sprintf('<td class="gwf_date">%s</td>', $master->displayFirstDate());
		echo sprintf('<td class="gwf_date">%s</td>', $master->displayDate());
		echo sprintf('<td class="gwf_date">%s</td>', $master->displayTrackTime());
		echo sprintf('<td class="gwf_num">%s</td>', $master->displayStartPerc());
		if ($tVars['old'] === true) {
			echo sprintf('<td class="gwf_num">%s</td>', $master->displayCurrPerc());
		}
		echo GWF_Table::rowEnd();
	}
	?>
</table>
<?php
echo $tVars['page_menu'];
?>