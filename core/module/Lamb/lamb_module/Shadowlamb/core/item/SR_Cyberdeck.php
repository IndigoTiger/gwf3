<?php
abstract class SR_Cyberdeck extends SR_Usable
{
	public abstract function getCyberdeckLevel();
	
	public function displayType() { return 'Cyberdeck'; }
	public function displayLevel() { return ' Lvl:'.$this->getCyberdeckLevel(); }
	
	public function onItemUse(SR_Player $player, array $args)
	{
		$p = $player->getParty();
		if (false === ($l = $p->getLocationClass(SR_Party::ACTION_INSIDE)))
		{
			$player->message('This item only works inside locations with computers.');
			return false;
		}

		$computers = $l->getComputers();
		$bot = Shadowrap::instance($player);
		
		if (count($computers) === 0)
		{
			$bot->reply('You don\'t see any Computers with a Headcomputer interface here.');
			return false;
		}
		
		if (count($args) !== 1)
		{
			$i = 1;
			$out = '';
			foreach ($computers as $pc)
			{
				$out .= sprintf(", \x02%s\x02-%s", $i++, $pc);
			}
			$bot->reply(sprintf('Possible targets: %s.', substr($out, 2)));
			return true;
		}
		
		if (false === ($computer = $this->getComputerTarget($player, $computers, $args[0])))
		{
			$bot->reply('The target computer is invalid.');
			return false;
		}

		if (false === ($computer = SR_Computer::getInstance($computer)))
		{
			$bot->reply('Database error.');
			return false;
		}
		
		if (!$player->hasHeadcomputer())
		{
			$bot->reply('You don\'t have a headcomputer.');
			return false;
		}
		
		if ($player->getBase('computers') < 0)
		{
			$bot->reply('You need to learn the computers skill first.');
			return false;
		}
		
		$computer->onHack($player, $this);
		
		return true;
	}
	
	public function getComputerTarget(SR_Player $player, array $computers, $arg)
	{
		if (is_numeric($arg))
		{
			$arg = (int)$arg;
			if (($arg > 0) && ($arg <= count($computers)))
			{
				$back = array_slice($computers, $arg-1, 1, false);
				return $back[0];
			}
			return false;
		}
		
		foreach ($computers as $computer)
		{
			if ($computer === $arg)
			{
				return $computer;
			}
		}
		
		return false;
	}
}
?>