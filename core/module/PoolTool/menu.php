<?php
function Module_PoolTool_menu()
{
	require_once 'core/module/PoolTool/PT_Menu.php';
	return PT_Menu::display();
}
?>