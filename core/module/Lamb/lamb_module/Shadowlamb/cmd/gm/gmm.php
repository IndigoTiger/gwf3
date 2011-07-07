<?php
final class Shadowcmd_gmm extends Shadowcmd
{
	public static function execute(SR_Player $player, array $args)
	{
		if (count($args) === 0) {
			Shadowrap::instance($player)->reply(Shadowhelp::getHelp($player, 'gmm'));
			return false;
		}
		Shadowshout::sendGlobalMessage('[Shadowlamb] '.implode(' ', $args));
		return true;
	}
}
?>