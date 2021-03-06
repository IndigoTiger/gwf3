<?php
final class Quest_Troll_Support extends SR_Quest
{
// 	public function getQuestName() { return 'TrollSupport'; }
// 	public function getQuestDescription() { return sprintf('Kill %d/%d Delaware Headhunters and return to Larry, the Troll chief.', $this->getAmount(), $this->getNeededAmount()); }
	public function getQuestDescription() { return $this->lang('descr', array($this->getAmount(), $this->getNeededAmount())); }

	public function getRewardXP() { return 7; }
	public function getRewardNuyen() { return 800; }
	public function getNeededAmount() { return 10; }
	
	public function checkQuest(SR_NPC $npc, SR_Player $player)
	{
		if ($this->getAmount() >= $this->getNeededAmount())
		{
			$npc->reply($this->lang('thx'));
// 			$npc->reply('Thanks! You are consider friend now.');
			return $this->onSolve($player);
		}
		else
		{
			$npc->reply($this->lang('more'));
// 			$npc->reply('You too weak?');
		}
		return false;
	}
	
	public function onNPCQuestTalkB(SR_TalkingNPC $npc, SR_Player $player, $word, array $args=NULL)
	{
		$dp = $this->displayRewardNuyen();
		switch ($word)
		{
			case 'shadowrun':
				$npc->reply($this->lang('sr1'));
// 				$npc->reply("Haha, you are a good boy ...");
				$npc->reply($this->lang('sr2'));
// 				$npc->reply('There are headhunter killing trolls and orks. You kill them back!');
				$npc->reply($this->lang('sr3', array($this->getNeededAmount(), $dp)));
// 				$npc->reply(sprintf('Kill %d and I pay %s.', $this->getNeededAmount(), $dp));
				break;
			case 'confirm':
				$npc->reply($this->lang('confirm'));
// 				$npc->reply("Go!");
				break;
			case 'yes':
				$npc->reply($this->lang('yes'));
// 				$npc->reply("You still here?");
				break;
			case 'no':
				$npc->reply($this->lang('no'));
// 				$npc->reply('You should go!');
				break;
		}
		return true;
	}
	
	public function onKillHeadHunter(SR_Player $player)
	{
		if ($this->isInQuest($player))
		{
			$this->increaseAmount(1);
			$player->message($this->lang('kill', array($this->getAmount(), $this->getNeededAmount())));
// 			$player->message(sprintf('Now you killed %d of %d Headhunters for Larry, the Troll chief.', $this->getAmount(), $this->getNeededAmount()));
		}
		return true;
	}
}
?>
