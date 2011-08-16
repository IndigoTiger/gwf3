{if $user->isLoggedIn()}
<span style="float:left; vertical-align: middle;">
	Hi <a href="{$root}profile/{$user->display('user_name')}" title="{$user->display('user_name')}'S Profile">{$user->display('user_name')}</a>; 
	unseen: 
	<a href="{$root}pm">MSG's: {$SF->getUnreadPM($user)}</a>,
	<a href="{$root}news">News: {$SF->getUnreadNews($user)}</a>, 
	<a href="{$root}forum">Forum: {$SF->getUnreadForum($user, true)}</a>, 
	<a href="{$root}links">Links{$SF->getUnreadLinks($user)}</a>
	{*Answers: 3; Articles: 3, Changes: 0, *}  ; 
</span>
{else}
{$SF->getLoginForm()}
{/if}

<span style="float:right; vertical-align: middle;">
{if $user->isLoggedIn()}
	Last Login: <span class="color">{GWF_Time::displayTimestamp($user->getVar('user_lastlogin'))}</span>
{/if}
	<a href="de/"><img src="/templates/{$SF->getDesign()}/images/German.png" alt="[DE]" title="{$SF->lang('change_language')}"></a>
	<a href="en/"><img src="/templates/{$SF->getDesign()}/images/America.png" alt="[EN]" title="{$SF->lang('change_language')}"></a>
</span>