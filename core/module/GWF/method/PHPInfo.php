<?php
final class GWF_PHPInfo extends GWF_Method
{
	public function getUserGroups() { return array(GWF_Group::ADMIN); }
	
	public function execute(GWF_Module $module)
	{
		phpinfo();
		die();
	}
}
?>