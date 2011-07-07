<?php
final class Shadowcmd_stop extends Shadowcmd
{
	public static function execute(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		if (false !== ($error = self::checkLeader($player))) {
			$bot->reply($error);
			return false;
		}
		$p = $player->getParty();
		
		if ($p->isSleeping())
		{
			$p->popAction(false);
			$p->notice(sprintf('You interrupt the parties sleeping and continue %s', $p->displayAction()));
			return true;
		}
		
		if (!$p->isMoving())
		{
			$bot->reply('Your party is not moving.');
			return false;
		}
		
		$c = $p->getCity();
		$p->pushAction(SR_Party::ACTION_OUTSIDE, $c);
		$p->notice('The party stopped. What now?!');
		return true;
	}
}
?>