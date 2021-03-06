<?php
abstract class SR_Bank extends SR_Location
{
	public function getAbstractClassName() { return __CLASS__; }
	
	public function getAreaSize() { return 48; }
	
	public function getTransactionPrice() { return 0; }
	
	public function getCommands(SR_Player $player) { return array('view', 'viewi', 'push', 'pop', 'pushy', 'popy', 'pushall', 'popall'); }
	
	public function getFoundText(SR_Player $player)
	{
		return $player->lang('stub_found_bank', array($this->getCity()));
	}

	public function getEnterText(SR_Player $player)
	{
		return $player->lang('stub_enter_bank', array($this->getCity()));
	}

	public function getHelpText(SR_Player $player)
	{
		return $player->lang('hlp_bank', array(Shadowfunc::displayNuyen($this->calcPrice($player))));
// 		$c = Shadowrun4::SR_SHORTCUT;
// 		$p = Shadowfunc::displayNuyen($this->calcPrice($player));
// 		return "In a bank you can use {$c}push and {$c}pop to bank items, and {$c}pushy and {$c}popy to store nuyen. Use {$c}view to list or search your banked items. Every transaction costs $p for you.";
	}
	
	public function calcPrice(SR_Player $player)
	{
		if (0 >= ($base = $this->getTransactionPrice())) {
			return 0;
		}
		return Shadowfunc::calcBuyPrice($base, $player);
	}
	
	public function checkAfford(SR_Player $player, $sendmoney=0)
	{
		# Free?
		if (0 >= ($price = $this->calcPrice($player)))
		{
			
			return true;
		}
		
		$nuyen = $player->getNuyen();
		if ($nuyen < ($price+$sendmoney))
		{
			$player->msg('1100', array(Shadowfunc::displayNuyen($price), Shadowfunc::displayNuyen($nuyen-$sendmoney)));
// 			return sprintf('You can not afford to use the bank. This cost %s and you only have %s to spare.', Shadowfunc::displayNuyen($price), Shadowfunc::displayNuyen($nuyen-$sendmoney));
			return false;
		}
		
		return true;
	}
	
	private function pay(SR_Player $player)
	{
		# Free?
		if (0 >= ($price = $this->calcPrice($player)))
		{
			return true;
		}

		# Error
		if (false === $player->pay($price))
		{
			return false;
		}
		
		# Announce payment
		return $player->msg('5143', array($price));
// 		return sprintf('You pay %s nuyen.', $price);
	}
	
	#############
	### Items ###
	#############
	public function on_view(SR_Player $player, array $args)
	{
		$items = $player->getBankItems();
		$text = array(
			'prefix' => $player->lang('bank'),
			'code' => '5188',
		);
		return Shadowfunc::genericViewI($player, $items, $args, $text, false);
	}

	public function on_viewi(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);

		if (count($args) !== 1)
		{
			$bot->reply(Shadowhelp::getHelp($player, 'viewi'));
			return false;
		}

		if (false === ($item = $player->getBankItem($args[0])))
		{
			$bot->rply('1101'); # item not in bank
			return false;
		}

		return $bot->rply('5189', array($item->getItemInfo($player)));
	}
	
	public function on_push(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		
		if ( (count($args) === 0) || (count($args) > 2) )
		{
			$bot->reply(Shadowhelp::getHelp($player, 'push'));
			return false;
		}
		
		$args[0] = strtolower($args[0]);
		
		if (false === $this->checkAfford($player))
		{
// 			$bot->reply($error);
			return false;
		}
		if (false === ($item = $player->getInvItem($args[0])))
		{
			$bot->rply('1029');
// 			$bot->reply('You don`t have that item in your inventory.');
			return false;
		}
		
		# Equipped?
// 		if ($item->isEquipped($player))
// 		{
// 			$player->unequip($item);
// 			$player->removeFromInventory($item);
// 			$player->putInBank($item);
// 			$stored = 1;
// 		}
		
		# A stackable?
		if ($item->isItemStackable())
		{
			$have_amt = $item->getAmount();
			
			# Store all amt
			if (count($args) === 1)
			{
				$player->removeFromInventory($item);
				$player->putInBank($item);
				$stored = $have_amt;
			}
			
			# Split item
			else
			{
				$amt = (int) $args[1];
				if ($amt < 1)
				{
					$bot->rply('1038');
// 					$bot->reply('Please push a positive amount of items.');
					return false;
				}
				if ($amt > $have_amt)
				{
					$bot->rply('1040', array($item->getItemName()));
// 					$bot->reply(sprintf('You have not that much %s.', $item->getItemName()));
					return false;
				}
				
				$item->useAmount($player, $amt);
				$item2 = SR_Item::createByName($item->getItemName(), $amt, true);
				$item2->saveVar('sr4it_uid', $player->getID());
				$player->putInBank($item2);
				$stored = $amt;
			}
		}
		
		# Not stackable
		else
		{
			if (count($args) === 1)
			{
				$player->removeFromInventory($item);
				$player->putInBank($item);
				$stored = 1;
			}
			
			else
			{
				$amt = (int)$args[1];
				if ($amt < 1)
				{
					$bot->rply('1038');
// 					$bot->reply('Please push a larger amount than zero.');
					return false;
				}
				
				$items2 = $player->getInvItems($item->getItemName(), $amt);
				if (count($items2) < $amt)
				{
					$bot->rply('1040', array($item->getItemName()));
// 					$bot->reply(sprintf('You have not that much %s.', $item->getItemName()));
					return false;
				}
				
				$stored = 0;
				foreach ($items2 as $item2)
				{
					if ($player->removeFromInventory($item2))
					{
						if ($player->putInBank($item2))
						{
							$stored++;
						}
					}
				}
			}
		}
		
		# Pay
		if (false === $this->pay($player))
		{
			return false;
		}
		
		$player->modify();
		return $bot->rply('5144', array(
			$stored, $item->displayFullName($player),
			Shadowfunc::displayWeight($player->get('weight')), Shadowfunc::displayWeight($player->get('max_weight'))
		));
// 		$paymsg .= sprintf('put %d of your %s into your bank account. You now carry %s/%s.',
// 			$stored, $item->getItemName(),
// 			Shadowfunc::displayWeight($player->get('weight')), Shadowfunc::displayWeight($player->get('max_weight'))
// 		);
		# Out
// 		$bot->reply($paymsg);
// 		return true;
	}
	
	public function on_pushall(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		$argc = count($args);
		if ($argc !== 1)
		{
			$bot->reply(Shadowhelp::getHelp($player, 'pushall'));
			return false;
		}
		
		$inv = $player->getInventorySorted();
		$min = 1;
		$max = count($inv);
		
		if (preg_match('/^\\d*-?\\d*$/', $args[0]))
		{
			$lims = explode('-',$args[0]);
			if (count($lims) === 1)
			{
				$from = (int)$lims[0];
				$to = $from;
			}
			else
			{
				$from = (strlen($lims[0]) === 0) ? $min : (int)$lims[0];
				$to = (strlen($lims[1]) === 0) ? $max : (int)$lims[1];
			}
		}
		else
		{
			$bot->reply(Shadowhelp::getHelp($player, 'pushall'));
			return false;
		}
		
		if ( ($from > $to) || ($from < 1) || ($to > $max) )
		{
			$bot->rply('1194');
			return false;
		}

		$item_price = $this->calcPrice($player);
		$max_price = ($to-$from+1) * $item_price;
		$has_nuyen = $player->getNuyen();
		if ($has_nuyen < $max_price)
		{
			$player->msg('1100', array(Shadowfunc::displayNuyen($max_price), Shadowfunc::displayNuyen($has_nuyen)));
			return false;
		}
		
		$i = 1;
		$pushed = 0;
		$skipped = 0;
		$price = 0;
		foreach ($inv as $itemname => $data)
		{
			if ($i >= $from)
			{
				$has_pushed = false;
				foreach ($data[1] as $item)
				{
					$amt = $item->getAmount();

					if ($player->removeFromInventory($item, false))
					{
						if($player->putInBank($item))
						{
							$pushed += $amt;
							$has_pushed = true;
						} else {
							if (!$player->giveItem($item))
							{
								Dog_Log::error(sprintf('Command pushall in %s made %s lose item %s (id: %d)!',$this->getName(),$player->getName(),$item->getNamePacked($player),$item->getID()));
							}
							$skipped += $amt;
						}
					} else {
						$skipped += $amt;
					}
				}

				if ($has_pushed)
				{
					$price += $item_price;
				}

				if ($i === $to)
				{
					break;
				}
			}
			$i++;
		}
		$player->modify();

		$player->pay($price);

		$msg_code = '5317';
		$msg_args = array($pushed, Shadowfunc::displayNuyen($price));
		if ($skipped !== 0)
		{
			$msg_code = '5318';
			$msg_args[] = $skipped;
		}
		$msg_args[] = Shadowfunc::displayWeight($player->get('weight'));
		$msg_args[] = Shadowfunc::displayWeight($player->get('max_weight'));
		return $bot->rply($msg_code, $msg_args);
	}
	
	/**
	 * Pop items from your bank.
	 * @param SR_Player $player
	 * @param array $args
	 * @return boolean
	 */
	public function on_pop(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);

		# Errors
		if ( (count($args) === 0) || (count($args) > 2) )
		{
			$bot->reply(Shadowhelp::getHelp($player, 'pop'));
			return false;
		}
		if (false === $this->checkAfford($player))
		{
			return false;
		}
		if (false === ($item = $player->getBankItem($args[0])))
		{
			$bot->rply('1101');
// 			$bot->reply('You don`t have that item in your bank.');
			return false;
		}
		
		$itemname = $args[0];
		
		# Whole stack or single
		if (count($args) === 1)
		{
			if (!$player->removeFromBank($item))
			{
				$bot->rply('1101');
// 				$bot->reply('You don`t have that item in your bank.');
				return false;
			}
			if (!$player->giveItems(array($item)))
			{
				$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
				return false;
			}

			$collected = $item->getAmount();
		}
		
		else
		{
			# Args
			$amt = (int)$args[1];
			if ($amt <= 0)
			{
				$bot->rply('1038');
// 				$bot->reply('Please pop a positve amount of items.');
				return false;
			}
			
			# Limits
			if ($item->isItemStackable())
			{
				$have_amt = $item->getAmount();
			}
			else
			{
				$items2 = $player->getBankItemsByItemName($item->getItemName());
				$have_amt = count($items2);
			}
			if ($amt > $have_amt)
			{
				$bot->rply('1102', array($item->getItemName()));
// 				$bot->reply(sprintf('You do not have that much %s in your bank.', $item->getItemName()));
				return false;
			}
			
			# Split Stack
			if ($item->isItemStackable())
			{
				if (false === $item->useAmount($player, $amt))
				{
					$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
					return false;
				}
				
				if (false === $item2 = SR_Item::createByName($item->getItemName(), $amt, true))
				{
					$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
					return false;
				}

				if (false === $player->giveItem($item2))
				{
					$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
					return false;
				}
				
				$collected = $amt;
			}
			
			# Multi Equipment
			else
			{
				$collected = 0;
				foreach ($items2 as $item2)
				{
					if (false === $player->removeFromBank($item2))
					{
						$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
					}
					elseif (false === $player->giveItem($item2))
					{
						$bot->reply(sprintf('Database error in %s line %s.', __FILE__, __LINE__));
					}
					else
					{
						$collected++;
						if ($collected >= $amt)
						{
							break;
						}
					}
				}
			}
		}
		
		$player->modify();
// 		$player->updateInventory();
		
		if (false === $this->pay($player))
		{
			return false;
		}
		
		return $bot->rply('5145', array(
			$collected, $item->displayFullName($player),
			Shadowfunc::displayWeight($player->get('weight')), Shadowfunc::displayWeight($player->get('max_weight'))
		));
		
// 		if ('' === ($paymsg = $this->pay($player))) {
// 			$paymsg .= 'You ';
// 		}
// 		$paymsg .= sprintf('remove %d %s from your bank account and put it into your inventory. You now carry %s/%s.',
// 			$collected, $item->getItemName(),
// 			Shadowfunc::displayWeight($player->get('weight')), Shadowfunc::displayWeight($player->get('max_weight'))
// 		);
// 		$bot->reply($paymsg);
// 		return true;
	}
	
	public function on_popall(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		$argc = count($args);
		if ($argc !== 1)
		{
			$bot->reply(Shadowhelp::getHelp($player, 'popall'));
			return false;
		}
		
		$inv = $player->getBankSorted();
		$min = 1;
		$max = count($inv);
		
		if (preg_match('/^\\d*-?\\d*$/', $args[0]))
		{
			$lims = explode('-',$args[0]);
			if (count($lims) === 1)
			{
				$from = (int)$lims[0];
				$to = $from;
			}
			else
			{
				$from = (strlen($lims[0]) === 0) ? $min : (int)$lims[0];
				$to = (strlen($lims[1]) === 0) ? $max : (int)$lims[1];
			}
		}
		else
		{
			$bot->reply(Shadowhelp::getHelp($player, 'popall'));
			return false;
		}
		
		if ( ($from > $to) || ($from < 1) || ($to > $max) )
		{
			$bot->rply('1194');
			return false;
		}

		$item_price = $this->calcPrice($player);
		$max_price = ($to-$from+1) * $item_price;
		$has_nuyen = $player->getNuyen();
		if ($has_nuyen < $max_price)
		{
			$player->msg('1100', array(Shadowfunc::displayNuyen($max_price), Shadowfunc::displayNuyen($has_nuyen)));
			return false;
		}
		
		$i = 1;
		$popped = 0;
		$skipped = 0;
		$price = 0;
		foreach ($inv as $itemname => $data)
		{
			if ($i >= $from)
			{
				$has_popped = false;
				foreach ($data[1] as $item)
				{
					$amt = $item->getAmount();

					if ($player->removeFromBank($item))
					{
						if($player->giveItem($item))
						{
							$popped += $amt;
							$has_popped = true;
						} else {
							if (!$player->putInBank($item))
							{
								Dog_Log::error(sprintf('Command popall in %s made %s lose item %s (id: %d)!',$this->getName(),$player->getName(),$item->getNamePacked($player),$item->getID()));
							}
							$skipped += $amt;
						}
					} else {
						$skipped += $amt;
					}
				}

				if ($has_popped)
				{
					$price += $item_price;
				}

				if ($i === $to)
				{
					break;
				}
			}
			$i++;
		}
		$player->modify();

		$player->pay($price);

		$msg_code = '5319';
		$msg_args = array($popped, Shadowfunc::displayNuyen($price));
		if ($skipped !== 0)
		{
			$msg_code = '5320';
			$msg_args[] = $skipped;
		}
		$msg_args[] = Shadowfunc::displayWeight($player->get('weight'));
		$msg_args[] = Shadowfunc::displayWeight($player->get('max_weight'));
		return $bot->rply($msg_code, $msg_args);
	}
	
	##################
	### Show Nuyen ###
	##################
	private function showNuyen(SR_Player $player, $code='5146')
	{
		$bot = Shadowrap::instance($player);
		return $bot->rply($code, array(
				$player->displayNuyen(), $player->displayBankNuyen(),
				Shadowfunc::displayNuyen($this->calcPrice($player))
		));
// 		$bot->reply(sprintf('You carry %s. In your bank are %s. Every transaction costs %s', $player->displayNuyen(), $player->displayBankNuyen(), Shadowfunc::displayNuyen($this->calcPrice($player))));
	}
	
	##################
	### Push Nuyen ###
	##################
	public function on_pushy(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		if (count($args) !== 1)
		{
			$this->showNuyen($player, '5146');
			return true;
		}
		
		if (0 >= ($want = round(floatval($args[0]), 2)))
		{
			$bot->rply('1062');
// 			$bot->reply(sprintf('Please push a positive amount of nuyen.'));
			return false;
		}
		
		if (false === $this->checkAfford($player, $want))
		{
			return false;
		}
		
		
		$have = $player->getNuyen();
		if ($want > $have)
		{
			$bot->rply('1103', array(Shadowfunc::displayNuyen($want), $player->displayNuyen()));
// 			$bot->reply(sprintf('You can not push %s, because you only carry %s.', Shadowfunc::displayNuyen($want), $player->displayNuyen()));
			return false;
		}
		
		if (false === $this->pay($player))
		{
			return false;
		}
		
		$player->alterField('bank_nuyen', $want);
		$player->giveNuyen(-$want);
		$have = $player->getBase('bank_nuyen');
		
		return $bot->rply('5147', array(
			Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have), $player->displayNuyen()
		));
// 		$paymsg .= sprintf('push %s into your bank account(now %s) and keep %s in your inventory.', Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have), $player->displayNuyen());
// 		$bot->reply($paymsg);
// 		return true;
		
	}

	#################
	### Pop Nuyen ###
	#################
	public function on_popy(SR_Player $player, array $args)
	{
		$bot = Shadowrap::instance($player);
		if (count($args) !== 1)
		{
			$this->showNuyen($player, '5279');
			return true;
		}
		
		if (false === $this->checkAfford($player))
		{
			return false;
		}
		
		if (0 >= ($want = round(floatval($args[0]), 2)))
		{
			$bot->rply('1062');
// 			$bot->reply(sprintf('Please pop a positive amount of nuyen.'));
			return false;
		}
		
		$have = $player->getBase('bank_nuyen');
		if ($want > $have)
		{
			$bot->rply('1104', array(Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have)));
// 			$bot->reply(sprintf('You can not pop %s, because you only have %s in your bank account.', Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have)));
			return false;
		}

		if (false === $this->pay($player))
		{
			return false;
		}
		
		$player->alterField('bank_nuyen', -$want);
		$player->giveNuyen($want);
		$have = $player->getBase('bank_nuyen');
		
		return $bot->rply('5148', array(
			Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have), $player->displayNuyen()
		));
// 		$paymsg .= sprintf('pop %s from your bank account(%s left) and now carry %s.', Shadowfunc::displayNuyen($want), Shadowfunc::displayNuyen($have), $player->displayNuyen());
// 		$bot->reply($paymsg);
// 		return true;
	}
}
?>
