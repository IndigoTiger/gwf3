<?php
final class Shadowcmd_skills extends Shadowcmd
{
	public static function execute(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		$bot->reply(sprintf('Your skills: %s.', Shadowfunc::getSkills($player)));
		return true;
	}
}
?>