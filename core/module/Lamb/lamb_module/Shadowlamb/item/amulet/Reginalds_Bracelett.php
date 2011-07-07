<?php
final class Item_Reginalds_Bracelett extends SR_Amulet
{
	public function getItemDescription() { return 'The bracelett of reginalds wife. It is beautiful with a big emerald.'; }
	public function getItemWeight() { return 550; }
	public function getItemPrice() { return 79.95; }
	public function getItemModifiersA(SR_Player $player)
	{
		return array(
			'charisma' => 0.5,
		);
	}
}