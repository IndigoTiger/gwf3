<?php
final class Redmond_Hideout extends SR_Entrance
{
	public function getFoundPercentage() { return 8.00; }
// 	public function getFoundText(SR_Player $player) { return 'You see two punks coming out of a ruinous house. You stealthy move to one of the windows. Seems like this is one of the Punks hideout.'; }
	public function getFoundText(SR_Player $player) { return $this->lang($player, 'found'); }

	public function getExitLocation() { return 'Hideout_Exit'; }
	
	public function onEnter(SR_Player $player)
	{
		$party = $player->getParty();
		$dice = rand(0, 6);
		if ($dice < 2) {
			
			$this->partyMessage($player, 'lucky');
// 			$party->notice('You silently search the door and windows for an entrance. You were lucky and sneak in...');
			$this->teleportInside($player, 'Hideout_Exit');
		}
		else if ($dice < 4) {
			$this->partyMessage($player, 'noluck');
// 			$party->notice('You silently search the door and windows for an entrance. You have no luck, everything\'s closed.');
		}
		else if ($dice < 6) {
			$this->partyMessage($player, 'fight2');
// 			$party->notice('You silently search the door and windows for an entrance. Two punks notice you and attack!');
			SR_NPC::createEnemyParty('Redmond_Cyberpunk','Redmond_Cyberpunk')->fight($party, true);
		}
		else {
			$this->partyMessage($player, 'fight4');
// 			$party->notice('You take a look through the doors keyhole. A party of four punks opens the door and surprises you.');
			SR_NPC::createEnemyParty('Redmond_Cyberpunk','Redmond_Cyberpunk','Redmond_Pinkhead','Redmond_Lamer')->fight($party, true);
		}
	}
}
?>
