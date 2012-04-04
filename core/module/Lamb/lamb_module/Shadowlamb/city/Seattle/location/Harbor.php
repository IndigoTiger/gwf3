<?php
final class Seattle_Harbor extends SR_Tower
{
	public function getFoundPercentage() { return 40.00; }
// 	public function getEnterText(SR_Player $player) {}
// 	public function getFoundText(SR_Player $player) { return 'You found the Seattle harbor. A big area for ships and stuff.'; }
	public function getFoundText(SR_Player $player) { return $this->lang($player, 'found'); }
	public function onEnter(SR_Player $player)
	{
		$this->partyMessage($player, 'enter');
// 		$player->getParty()->notice('You enter the Harbor ...');
		return $this->teleportInside($player, 'Harbor_Exit');
	}
}
?>