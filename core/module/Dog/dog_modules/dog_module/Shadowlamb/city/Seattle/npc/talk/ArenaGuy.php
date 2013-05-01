<?php
final class Seattle_ArenaGuy extends SR_TalkingNPC
{
// 	public function getName() { return 'The director'; }
	public function getName() { return $this->langNPC('name'); }
	public function getNPCModifiers() { return array('race' => 'human'); }
	public function onNPCTalk(SR_Player $player, $word, array $args)
	{
// 		$c = Shadowrun4::SR_SHORTCUT;
// 		$b = chr(2);
		switch ($word)
		{
			case "fight": #$msg = "Yes, you can fight for money here."; break;
			case 'blackmarket': #$msg = 'Some of our best fighters buy their equipment there.'; break;
			case 'renraku': #$msg = 'I was a security employee for Renraku a while ago. I got fired. Now I have my own small business.'; break;
			case 'challenge': #$msg = "Come on, just try {$c}challenge!"; break;
			case 'malois': #$msg = 'Challengers come and challengers go.'; break;
				return $this->rply($word);
			default: #$msg = "Hello chummer. Interested in a {$b}fight{$b}? We pay well. Try {$c}challenge."; break;
				return $this->rply('default');
		}
// 		return $this->reply($msg);
	}
}
?>
