<?php
$lang = array(
	# Page Titles
	'pt_list' => 'Download Section',
	'mt_list' => 'Download Section, Downloads, Exclusive downloads, '.GWF_SITENAME,
	'md_list' => 'Exclusive downloads on '.GWF_SITENAME.'.',

	# Page Info
	'pi_add' => 'For best user experience upload your file first, it will get stored into your session. Afterwards alter the options.<br/>The maximum upload size is set to %1%.',

	# Form Titles
	'ft_add' => 'Upload a file',
	'ft_edit' => 'Edit Download',
	'ft_token' => 'Enter your download token',

	# Errors
	'err_file' => 'You have to upload a file.',
	'err_filename' => 'Your specified filename is invalid. Max length is %1%. Try to use basic ascii chars.',
	'err_level' => 'The user level has to be >= 0.',
	'err_descr' => 'The description has to be 0-%1% chars long.',
	'err_price' => 'The price has to be between %1% and %2%.',
	'err_dlid' => 'The download could not be found.',
	'err_token' => 'Your download token is invalid.',

	# Messages
	'prompt_download' => 'Press OK to download the file',
	'msg_uploaded' => 'Your file got uploaded successfully.',
	'msg_edited' => 'The download has been edited successfully.',
	'msg_deleted' => 'The download has been deleted successfully.',

	# Table Headers
	'th_dl_filename' => 'Filename',
	'th_file' => 'File',
	'th_dl_id' => 'ID',
	'th_dl_gid' => 'Needed Group',
	'th_dl_level' => 'Needed Level',
	'th_dl_descr' => 'Description',
	'th_dl_price' => 'Price',
	'th_dl_count' => 'Downloads',
	'th_dl_size' => 'Filesize',
	'th_user_name' => 'Uploader',
	'th_adult' => 'Adult content?',
	'th_huname' => 'Hide Username?',
	'th_vs_avg' => 'Vote',
	'th_dl_expires' => 'Expires',
	'th_dl_expiretime' => 'Download valid for',
	'th_paid_download' => 'A payment is needed to download this file',
	'th_token' => 'Download Token',

	# Buttons
	'btn_add' => 'Add',
	'btn_edit' => 'Edit',
	'btn_delete' => 'Delete',
	'btn_upload' => 'Upload',
	'btn_download' => 'Download',
	'btn_remove' => 'Remove',

	# Admin config
	'cfg_anon_downld' => 'Allow guest downloads',
	'cfg_anon_upload' => 'Allow guest uploads',
	'cfg_user_upload' => 'Allow user uploads',
	'cfg_dl_gvotes' => 'Allow guest votes',	
	'cfg_dl_gcaptcha' => 'Guest Upload Captcha',	
	'cfg_dl_descr_max' => 'Max. description length',
	'cfg_dl_descr_min' => 'Min. description length',
	'cfg_dl_ipp' => 'Items per page',
	'cfg_dl_maxvote' => 'Max. votescore',
	'cfg_dl_minvote' => 'Min. votescore',

	# Order
	'order_title' => 'Download token for %1% (Token: %2%)',
	'order_descr' => 'Purchased download token for %1%. Valid for %2%. Token: %3%',
	'msg_purchased' => 'Your payment has been received and a download token has been inserted.<br/>Your token is \'%1%\' and it is valid for %2%.<br/><b>Write the token down if you have no account at '.GWF_SITENAME.'!</b><br/>Else simply <a href="%3%">follow this link</a>.',

	# v2.01 (+col)
	'th_purchases' => 'Purchases',

	# v2.02 Expire + finsih
	'err_dl_expire' => 'The expire time has to be between 0 seconds and 5 years.',
	'th_dl_expire' => 'Download expires after',
	'tt_dl_expire' => 'Duration expression like 5 seconds or 1 month 3 days.',
	'th_dl_guest_view' => 'Guest Visible?',
	'tt_dl_guest_view' => 'May guests see this download?',
	'th_dl_guest_down' => 'Guest Downloadable?',
	'tt_dl_guest_down' => 'May guests download this file?',
	'ft_reup' => 'Re-Upload File',
	'order_descr2' => 'Purchased download token for %1%. Token: %2%.',
	'msg_purchased2' => 'Your payment has been received and a download token has been inserted.<br/>Your token is \'%1%\'.<br/><b>Write the token down if you have no account at '.GWF_SITENAME.'!</b><br/>Else simply <a href="%2%">follow this link</a>.',
	'err_group' => 'You need to be in the %1% usergroup to download this file.',
	'err_level' => 'You need a userlevel of %1% to download this file.',
	'err_guest' => 'Guests are not allowed to download this file.',
	'err_adult' => 'This is adult content.',

	'th_dl_date' => 'Date',
);
?>