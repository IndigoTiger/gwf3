<?php
final class Shadowcmd_drop extends Shadowcmd
{
	public static function execute(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		
		if ( (count($args) < 1) || (count($args) > 2) ) {
			$bot->reply(Shadowhelp::getHelp($player, 'drop'));
			return false;
		}
		
		if (false === ($item = $player->getInvItem($args[0]))) {
			$bot->reply('You don`t have that item.');
			return false;
		}
		
		
		$amt = count($args) === 2 ? (int)$args[1] : 1;
		
		if ($amt < 1) {
			$bot->reply('You can only drop a positive amount of items.');
			return false;
		}
		
		if (!$item->isItemDropable()) {
			$bot->reply('You should not drop that item.');
			return false;
		}
		
		# Drop stackable.
		if ($item->isItemStackable())
		{
			if ($amt > $item->getAmount())
			{
				$bot->reply('You don\'t have that much '.$item->getName().'.');
				return false;
			}
			if (false === $item->useAmount($player, $amt))
			{
				$bot->reply('Database error 9.');
				return false;
			}
		
			$dropped = $amt;
		}
		

		else
		{
			$dropped = 0;
			while ($dropped < $amt)
			{
				if (false === ($item2 = $player->getInvItem($args[0])))
				{
					break;
				}
				if ($player->removeFromInventory($item2))
				{
					$dropped++;
				}
			}
		}
		
		$bot->reply(sprintf('You got rid of %d %s.',$dropped, $item->getItemName()));
		$player->modify();
		
		return true;
	}
}
?>