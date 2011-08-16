<div class="gwf3_form">
<!-- <div class="gw3_form_title" ><?php echo $tVars['title']?></div>  -->
	<form action="<?php echo $tVars['action']; ?>" method="<?php echo $tVars['method']; ?>" enctype="<?php echo $tVars['enctype']; ?>">
	<table>
		<thead>
			<tr><th colspan="100"><?php echo $tVars['title']?></th></tr>
			<tr>
<?php
$no_head = array(GWF_Form::HIDDEN, GWF_Form::SUBMIT);
foreach ($tVars['data'] as $key => $data)
{
	if (in_array($data[0], $no_head, true)) {
		echo '<th></th>'; 
		continue;
	}
	printf('<th>%s</th>', $key);
}
?>
			</tr>
		</thead>
		<tbody>
			<tr>
<?php
foreach ($tVars['data'] as $key => $data)
{
	echo '<td>';
	switch ($data[0])
	{
		case GWF_Form::HIDDEN:
			printf('<input type="hidden" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::INT:
		case GWF_Form::STRING:
			printf('<input type="text" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::PASSWORD:
			printf('<input type="password" name="%s" value="" />'.PHP_EOL, $key);
			break;
		case GWF_Form::CHECKBOX:
			printf('<input type="checkbox" name="%s"%s value="" />'.PHP_EOL, $key, ($data[1]?' checked="checked"':''));
			break;
		case GWF_Form::SUBMIT:
			printf('<input type="submit" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
			case GWF_Form::DATE:
			case GWF_Form::SELECT:
			case GWF_Form::SSTRING:
				printf('%s'.PHP_EOL, $data[1]);
				break;
			
//		case GWF_Form::CAPTCHA:
//			printf('<tr><td>%s</td><td>%s</td><td><img src="%simg/captcha.php" onclick="this.src=\'%simg/captcha.php?\'+(new Date()).getTime();" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha1'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha1')), GWF_WEB_ROOT, GWF_WEB_ROOT);
//			printf('<tr><td>%s</td><td>%s</td><td><input type="text" name="%s" value="%s" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha2'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha2')), $key, $data[1]);
//			break;
			
		default:
			die(sprintf('Your '.__FILE__.' is missing datatype %d', $data[0]));
		
	}
	echo '</td>';
}
?>
			</tr>
		</tbody>
	</table>
	</form>
</div>