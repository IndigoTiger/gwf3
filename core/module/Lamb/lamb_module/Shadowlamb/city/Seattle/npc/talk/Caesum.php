<?php
final class Seattle_Caesum extends SR_TalkingNPC
{
	public function getName() { return 'Caesum'; }
	public function onNPCTalk(SR_Player $player, $word, array $args)
	{
		switch ($word)
		{
			default: $this->reply('Hello. My name is Caesum and i lead the school of cryptography and applied math.');
		}
	}
}
?>