<?php
/**
 * Abstract tower class.
 * Can beam party around.
 * @author gizmore
 */
abstract class SR_Tower extends SR_Location
{
	################
	### Location ###
	################
	public function isPVP() { return true; }
	public function hasATM() { return false; }
	
	#############
	### Tower ###
	#############
	/**
	 * Set party to travel mode.
	 * @param SR_Player $player
	 * @param string $target
	 * @param int $eta
	 */
	public function teleport(SR_Player $player, $target, $eta=4)
	{
		if (false === ($location = Shadowrun4::getLocationByTarget($target)))
		{
			Lamb_Log::logError('Unknown $target "'.$target.'" for '.__METHOD__.' in '.__FILE__.' line '.__LINE__.'.');
			return false;
		}
		$party = $player->getParty();
		$party->pushAction(SR_Party::ACTION_TRAVEL, $target, $eta);
		$party->giveKnowledge('places', $target);
		return true;
	}
	
	/**
	 * Beam a party to a target location.
	 * @param SR_Player $player
	 * @param string $target
	 * @param string $action
	 */
	public function beam(SR_Player $player, $target='Redmond_Hotel', $action='inside')
	{
		if (false === ($location = Shadowrun4::getLocationByTarget($target)))
		{
			Lamb_Log::logError('Unknown $target "'.$target.'" for '.__METHOD__.' in '.__FILE__.' line '.__LINE__.'.');
			return false;
		}
		
		$party = $player->getParty();
		$party->giveKnowledge('places', $target);

		# City changed?
		$oldcity = $party->getCity();
		$party->pushAction($action, $target, $eta);
		$newcity = $party->getCity();
		if ($oldcity !== $newcity)
		{
			$city = $party->getCityClass();
			$city->onCityEnter($party);
		}
		
		return true;
	}
}
?>