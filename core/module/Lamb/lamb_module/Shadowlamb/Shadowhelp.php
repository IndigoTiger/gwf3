<?php
/**
 * The evil Shadowhelp files.
 * @author gizmore
 */
final class Shadowhelp
{
	public static function unshortcut($word)
	{
		if ('' === ($word = strtolower(trim($word))))
		{
			return 'root';
		}
		$shortcuts = array(
			'def' => 'defense',
			'dmg' => 'damage',
			'skill' => 'skills',
			'attribute' => 'attributes',
			'casting' => 'cast',
			'bmi' => 'body_mass',
		);
		$shortcuts = array_merge($shortcuts, Shadowcmd::$CMD_SHORTCUTS, SR_Player::$ATTRIBUTE, SR_Player::$SKILL, SR_Player::$KNOWLEDGE);
		
		if (isset($shortcuts[$word]))
		{
			return $shortcuts[$word];
		}
		
		return $word;
	}
	
	public static function getAllHelp($player=NULL)
	{
		$xppk = $player === NULL ? SR_Player::XP_PER_KARMA : $player->getXPPerKarma();
		
		$ele = $player === NULL ? 0 : $player->get('elephants');
		$ele_gain = $player === NULL ? 0 : $player->getHPGain();
		$ele_time = GWF_Time::humanDuration(SR_Player::HP_REFRESH_TIMER); 
		
		$orca = $player === NULL ? 0 : $player->get('orcas');
		$orca_gain = $player === NULL ? 0 : $player->getMPGain();
		$orca_time = GWF_Time::humanDuration(SR_Player::MP_REFRESH_TIMER); 
		
		$crit = $player === NULL ? 0 : sprintf('%.03f', $player->getCritPermille()/1000);
		
		$eqs = implode('|', array_keys(SR_Player::$EQUIPMENT));
		$back = array(
			'root' => "Check out these topics",
			array(
			
				'beginners' => 'First steps',
				array(
					'get_started' => 'First you should #talk to the hotelier. then you should #equip clothes. Then you should #party up with players and #join their parties. Then you should #explore the first city.',
					'where_am_i' => 'You begin in the Redmond Hotel. Check your party status with #(p)arty. Check your #known_places with #(kp). Find new places with #(exp)lore. Do not forget to check your #e(q)uipment and cleanup your #(i)nventory.',
					'what_is_it' => "Shadowlamb is a full featured mmorpg. You can #(j)oin parties, solve #(qu)ests, runecraft your items and learn magic spells. It combines multiple irc networks into a single gameworld, and thus is unique among all irc games.",
				),
				
				'attributes' => "Player Command. Usage: #(a)ttributes. Attributes",
				array(
					'body' => "The body attribute raises your max_hp by ".SR_Player::HP_PER_BODY.'.',
					'magic' => "The magic attribute raises your max_mp by ".SR_Player::MP_PER_MAGIC.'.',
					'strength' => "The strength attribute raises attack for melee weapons. It also raises the max weight you can carry.",
					'quickness' => "Quickness raises your defense by increasing your chance to evade attacks. Also your busytime and explore times will decrease.",
					'wisdom' => "Wisdom increases duration of magic spells and increases your chance for successfull spell casting.",
					'intelligence' => "Intelligence increases the power of your magic spells.",
					'charisma' => "Charisma raises the time that hirelings follow you.",
					'luck' => "Luck increases the chance of better drops.",
					'reputation' => "Reputation determines how famous you are amongst the world of Shadowlamb. Some quests require a minimum reputation.",
					'essence' => "Essence describes how wasted your body and mind is. Every player starts with an essence of 6. You cannot lvlup your essence. Although it is known that some rare runes can increase your essence. Essence is essential for casting spells.",
				),
				
				'skills' => 'Player Command. Usage: #(sk)ills. Show your learned Skills',
				array(
					'melee' => 'The melee skill will increase your attack for melee weapons.',
					'ninja' => 'The ninja skill will increase your damage and attack for ninja weapons.',
					'firearms' => 'The firearms skill will increase your damage and attack for fireweapons.',
					'bows' => 'The bows skill will increase your damage and attack for bows.',
					'pistols' => 'The pistols skill will increase your damage and attack for pistols.',
					'shotguns' => 'The shotguns skill will increase your damage and attack for shotguns.',
					'smgs' => 'The smgs skill will increase your damage and attack for small machine guns.',
					'hmgs' => 'The hmgs skill will increase your damage and attack for heavy machine guns.',
					'computers' => 'The computers skill will increase your chance to hack computers.',
					'electronics' => 'The electronics skill will increase your chance to disable traps.',
					'biotech' => 'The biotech skill will increase your healing when using items.',
					'negotiation' => 'The negotiation skill will lower prices when buying items, and raise prices when selling items.',
					'sharpshooter' => 'The sharpshooter skill will raise your chance for a critical hit. This also applies to melee and ninja weapons.',
					'searching' => 'The searching skill will increase the dropchance on searches.',
					'lockpicking' => 'The lockpicking skill will increase your chance on picking locks.',
					'thief' => 'The thief skill will increase your chance on thieving items of shops and not getting caught. ',
				),
				
				'combat' => 'Combat topics',
				array(
					'combat1' => "In combat you have a one-item-stack. When your busytime is over your last command gets executed. The default command is #attack <random>.",
					'combat2' => "Spells and weapons have a combat distance. Before you can attack an enemy, it has to be in range. Therefor, the #attack command will lock the target you have chosen.",
					'combat3' => "If you do nothing in combat, your default action is #attack rand(), locking a random target.",
					'combat4' => "Do not forget to cleanup your inventory. An overloaded character might get a malus on their combat stats.",
					'busytime' => "When a fight starts, you have an initial busytime. When this time is over your last command gets executed, and you get busy again, depending on your action.",
					'range' => 'In combat you have a range and position. Party A has positive position and Party B has negative postition. You need to be in range for weapons and spells.',
					'range2' => 'Spells and weapons have a minimum range. A range of 2m has no malus in combat / is considered 0.',
					'distance' => "Imagine the combat as a line. +10 +8 +6 +4 +2 +0 -2 -4 -6 -8 -10. One party starts in the positive, the other in the negative. You can set your default distance with #sd.",
					'death' => "When you die you might loose a random item. Also you will loose XP and nuyen you might have in your pocket.",
				),
				
				'your_status' => 'Your character status',
				array(
				
					'gender' => 'Your gender',
					array(
						'male' => 'Males have a slight strength and body bonus.',
						'female' => 'Females have a slight intelligence and charisma bonus.',
					),
				
					'race' => 'There two major types of races',
					array(
					
						'player_races' => 'Available player races',
						array(
							'fairy' => 'The fairy is small, weak and very hard to play. It is meant to be a party character.',
							'elve' => 'The elve is weak and difficult to play. He enjoys the arcane powers. As every elve he has a bonus on the bows skill.',
							'halfelve' => 'The halfelve are often merchants or teachers that stopped to learn magic.',
							'vampire' => 'Vampires are undead creatures. They have a low body and HP and trust in equipment they have collected over their years.',
							'darkelve' => 'The darkelve combines melee, bows and magic into a playable character.',
							'woodelve' => 'The woodelve combines melee, bows and magic into a playable character.',
							'human' => 'The human is the intermediate character of the player_races. There are player_races and npc_races.',
							'gnome' => 'The gnome is a bit smaller than a halfork.',
							'dwarf' => 'The dwarf is a bit smaller than a gnome.',
							'halfork' =>  'The halfork is a bit dumb, but easy to play for beginners.',
							'halftroll'=> 'The halftroll is a bit dumb, but very easy to play for beginners.',
							'ork' => 'The ork is dumb and easy to play for beginners.',
							'troll' => 'The population of trolls has increased by 140% in the last year.',
							'gremlin' => 'The gremlin is not meant to be played because it cannot swim.',
						),
						
						'npc_races' => 'NPC only races',
						array(
							'droid' => 'An electronical device that makes your life harder.',
							'dragon' => 'A mysterious creature from the very same planet.',
						),
					),
					
					'asl' => 'Player command. Usage: #asl [<age|bmi|height>]. Use #aslset to setup your asl. Show your',
					array(
						'age' => 'Your character\'s age, in years.',
						'body_mass' => 'Your characters body_mass in gramm.',
						'height' => 'Your character\'s height in centimeters.',
					),
					
					'level' => 'Player command. Show the party- and memberlevels. Your level determines what mobs you can encounter. '.SR_Player::XP_PER_LEVEL.' XP equal 1 level.',
					'hp' => "HP are your hitpoints. Use #hp to see all party HP. You can refresh hitpoints in Hotel, or by healing items and spells.",
					'mp' => "MP are your manapoints. Use #mp to see all party MP. To cast magic #spells you need MP. You can refresh manapoints in Hotel, or by using certain potions or items.",
					'atk' => 'Your chances to hit.',
					'defense' => 'You chance to evade attacks. Reduces hits. You can increase defense with quickness',
					'damage' => 'The min and max damage you can cause.',
					'arm' => "Armor reduces damage. There are two types of armor",
					array(
						'marm' => 'Your melee armor. Reduces melee damage.',
						'farm' => 'Your fireweapon armor. Reduces firearms damage.',
					),
					'xp' => NULL,
					'karma' => NULL,
					'weight' => NULL,
					'max_weight' => 'The max_weight you can carry. If you exceed your max_weight you get a malus on your attack and defense or cannot move at all.',
					'nuyen' => NULL,
					
					'special_stats' => 'There are some special stats too:',
					array(
						'spellatk' => 'Increases your magic attack. This is also affected by essence and intelligence.',
						'spelldef' => 'Increases your magic defense. This is also affected by essence and intelligence.',
						'orcas' => "Increases your MP refreshening. This is also affected by the magic attribute. Your current orcas level is {$orca} and you gain {$orca_gain} every {$orca_time}.",
						'elephants' => "Increases your HP refreshening. Your current elephants level is {$ele} and you gain {$ele_gain} every {$ele_time}.",
						'critical' => "Your current chance of a critical hit is {$crit}%.",
					),
				),
				
				'effects' => 'Known effects',
				array(
					'alc' => 'The alcohol effect determines how drunk a character is. If a character is too drunk, it might do random actions in combat.',
					'caf' => 'The caffeine effect determines how caffinated a character is. If a character is too caffinated it cannot sleep.',
				),
				
//				'items' => "There are ".SR_Item::getTotalItemCount()." items",
//				self::getItemTree($player),
				
				'all_spells' => 'There are '.SR_Spell::getTotalSpellCount().' spells',
				self::getSpellTree($player),
				
				'all_cmds' => 'There are a lot of commands',
				array(
				
//					'www_cmds' => 'Wrapper Commands for the WWW',
//					array(
//						'helo' => 'Will make the www client join the irc network ... kinda',
//					),
//					
//					'debug_cmds' => 'Debugging commands',
//					array(
//						'debug' => 'Undocumented',
//					),

					'leader_cmds' => 'Some special leader commands',
					array(
						'kick' => 'Leader command. Usage: #kick <player>. Kick a player from you party. See #ban.',
					),
					
					'guest_cmds' => 'These commands always work',
					array(
						'start' => 'Player command. The first command you have to type in Shadowlamb. Usage: #start <race> <gender>.',
						'help' => 'Browse the Shadowhelp file. Usage: #help [<keyword>].',
						'stats' => 'Print current gameworld stats.',
						'motd' => 'Print the current "message of the day".',
						'world' => 'Print info about the world of Shadowlamb.',
						'players' => 'Usage: #players <page>. Print info about the current players. Paginated.',
					),
					
					'gm_cmds' => '"Game Master" commands for debugging and cheating',
					array(
						'gm' => 'GM command. Usage: #gm <username> <field> <value>.',
						'gmc' => 'GM command. Usage: #gmc. Cleanup the database. Handle with care!',
						'gmd' => 'GM command. Usage: #gmd <player> <remote command to execute>.',
						'gmi' => 'GM command. Usage: #gmi <username> <itemname>. Example: gmi gizmore LeatherVest_of_strength:1,quickness:4,marm:4,foo:4',
						#'gmk' => 'GM command. Usage: #gmk <username> <field> <knowledge>',
						'gml' => "GM command. Usage: #gml <username> <city> <location>. Teleport a party to a location.",
						'gmm' => "GM command. Usage: #gmm <the message>. Send a hyperglobal message to all Shadowlamb channels.",
						'gms' => "GM command. Usage: #gms <player>. Print a lot of status of a player.",
						'gmsp' => "GM command. Usage: #gmsp <player> <spell> <level>. Adjust the spell for a player.",
						'gmt' => "GM command. Usage: #gmt <username> <enemy,enemy,...>. Attack a party with enemies for debugging purposes.",
					),
						
					'status_cmds' => 'Commands for player information',
					array(
						'commands' => "Player command. Usage: #(c)ommands. List your currently available commands. These are ordered by always,leader,special,location and change depending on your current location and party action.",
						'ccommands' => "Player command. Usage: #(cc)ommands. List many hidden default commands. These always work and you maybe don't even use them.",
						'status' => 'Player command. Usage: #(s)tatus. View your status.',
						'attributes' => NULL,
						'skills' => NULL,
						'asl' => NULL,
						'aslset' => "Player command. Usage: #aslset RANDOM || #aslset <age>y <bmi>kg <hgt>cm. Example: #aslset 20y 140cm 80kg.",
						'equipment' => 'Player command. Usage: #e(q)uipment. View your equipment.',
						array(
							'amulet' => 'You can #equip amulets and wear them as #equipment.',
							'armor' => 'You can #equip armory and wear them as #equipment.',
							'boots' => 'You can #equip boots and wear them as #equipment.',
							'earring' => 'You can #equip earrings and wear them as #equipment.',
							'helmet' => 'You can #equip helmets and wear them as #equipment.',
							'legs' => 'You can #equip legs and wear them as #equipment.',
							'ring' => 'You can #equip rings and wear them as #equipment.',
							'shield' => 'You can #equip shields and wear them as #equipment.',
							'weapon' => 'You can #equip weapons and wear them as #equipment.',
							'mount' => 'You can #equip mounts to lower your travel times. Also you can store items in them.',
						),
				
					'party' => 'Player command. Usage. #(p)arty. View your party status.',
						'inventory' => 'Player command. Usage: #(i)nventory. View your inventory.',
						'cyberware' => 'Player command. Usage: #()yberware [<cy_id>]. View your cyberware.',
						'effects' => 'Player command. Usage: #(ef)fects. View your current effects. For example after beeing the target of a spell or drinking a potion.',
						'examine' => 'Player command. Usage: #ex <inv_id|'.$eqs.'|itemname>. Examine your items.',
						'known_places' => "Player command. Usage: #known_places|#kp. List your known places in your current city. You can go to them with #(g)oto. Use #(exp)lore to find new places.",
						'known_words' => "Player command. Usage: #known_words|#kw. List some useful words you have learned, which you can enumerate with #talk and #say.",
						'known_spells' => 'Player command. Usage: #known_spells|#ks <[ks_id|spell_name]>. List your known magic spells or examine a spell.',
						'quests' => 'Player command. Usage: #(qu)ests [<accepted|rejected|done|failed|aborted>] [<id>]. Shows info about your quests.',
						'nuyen' => 'Player command. Usage: #(n)u(y)en/#ny. Show the parties nuyen. Nuyen is the currency(money) in Shadowlamb. It means "New Yen".',
						'karma' => 'Player command. Usage: #(ka)rma. Show the parties karma. With karma you can lvlup your skills, attributes and spells.',
						'hp' => NULL,
						'mp' => NULL,
						'weight' => "Player command. Usage: #(we)ight. Show how much stuff the party is carrying. You can increase your max_weight with strength.",
						'lvlup' => "Player command. Usage: #lvlup <skill|attribute|spell|knowledge>. Increase your level for an attribute,skill,spell or knowledge by using karma.",
						'level' => NULL,
						'party_level' => 'Player command. Usage: #(p)arty_(l)evel. Show the party level.',
					),
					
					'chat_cmds' => 'Commands for chat and talk',
					array(
						'party_message' => 'Player command. Usage: #party_message|#pm <your message ...>. Send a message to all party members. Useful for cross-server/cross-channel messages.',
						'say' => "Player command. Usage: #say <#kw_id|the message>. If you meet other parties on street you can use #say to talk to them. Inside and outside locations this sends messages accross the servers. In fight this sends messages to enemy party.",
						'talk' => "Player command. Usage: #talk <#kw_id|word>. In many locations you can use #talk to talk to the NPCs. If there is more than one NPC it is often #ttX. Always check your #c inside locations.",
						'shout' => "Player command. Usage: #shout <the message>. Shout a message to all shadowlamb channels on all servers.",
						'whisper' => "Player command. Usage: #(w)hisper <player> <the message>. Send a message to another player. This works accross networks.",
					),
					
					'combat_cmds' => 'Commands work in combat',
					array(
						'forward' => "Combat command. Usage: #forward|#fw. Move forward in distance.",
						'backward' => "Combat command. Usage: #backward|#bw. Move backwards in distance.",
						'flee' => "Combat command. Usage: #(fl)ee. Try to flee from the enemy. If successful you will #part the current #(p)arty.",
						'attack' => "Combat command. Usage: #attack|## [<enemy_name|enemy_enum>]. Select your target to attack with your weapon. Attack will lock your target, so you don't need to type attack all the time. See #help busytime and #help combat.",
						'set_distance' => 'Player command. Usage: #(s)et_(d)istance/#sd <meters>. Set your default combat distance.',
					),
					
					'action_cmds' => 'Action commands',
					array(
						'npc' => 'Leader command. Usage: #npc <the remote command>. Execute a command in the name of your NPC.',
						'say' => NULL,
						'use' => 'Player command. Usage: #(u)se <inv_id|item_name> [<target_name|target_enum>]. Use an item. In combat this costs time.',
						'cast' => 'Player command. Usage: #(ca)st [<ks_id|spell_name>] [<target_name|target_enum>]. Cast a spell. If spell is friendly the enum is member_enum. If spell is offensive the enum is enemy enum. See #ks|#known_spells for your spells.',
						'drop' => 'Player command. Usage: #drop <inv_id|item_name> [<amount>]. Drop one or multiple items. Used to save weight.',
						'equip' => 'Player command. Usage: #(eq)uip <itemname|inv_id>. Equip yourself with an item. Will cost time in combat.',
						'unequip' => 'Player command. Usage: #uneqip|#uq <'.$eqs.'>. Unequip a wearing item. Will cost time in combat.',
						'reload' => "Player command. Usage: #(r)eload. Reload your weapon. This is only needed for fireweapons and costs time in combat.",
						'hijack' => 'Leader command. Usage: #hijack [<target>]. Try to break into a player mount and steal an item.',
						'give' => 'Player command. Usage: #give <player_name> <inv_id|item> [<amount>]. Give a player in the same location some items. In combat this costs time. See also #givekp, #givekw and #giveny.',
						'givekp' => 'Player command. Usage: #givekp <player_name> <#(kp)_id|place>. Tell a player in your location about a known place. See #give, #givekw and #giveny.',
						'givekw' => 'Player command. Usage: #givekw <player_name> <#(kw)_id|word. Tell a player in your location about a known word. See #give, #givekp and #giveny.',
						'giveny' => 'Player command. Usage: #giveny <player_name> <nuyen>. Give a player in your location some nuyen. See #give, #givekp and #givekw.',
					),
					
					'option_cmds' => 'Commands that change your player and client options',
					array(
						'redmond' => 'Player command. Usage: #redmond. If idle you can teleport to Redmond_Hotel. This will cost some XP from the karmapool and part your current party.',
						'reset' => 'Player command. Usage: #reset. #reset. Use reset to delete your player and start over. Handle with care!',
						'enable' => 'Player command. Usage: #enable <help|notice|privmsg>. Toggle user interface options for your player.',
						'disable' => 'Player command. Usage: #disable <help>. Toggle user interface options for your player.',
						'running_mode' => "Player command. Usage: #(r)unning_(m)ode. Use it twice to convert your character into a real runner. This means raised max stats, but instant death.",
					),
					
					'move_cmds' => 'Commands that change the party location or action',
					array(
						'explore' => 'Leader command. Usage: #(exp)lore. Start to explore the current city. When the explore time is over, you find a new #kp. When you have found a new known_place, you are outside of it. Use #(en)ter to enter it.',
						'goto' => "Leader command. Usage: #(g)oto <kp_id|place>. Goto another place in your current city.",
						'hunt' => "Leader command. Usage: #hunt <player>. Hunt another human party.",
						'enter' => 'Leader command. Usage: #(en)ter. Enter a location. Check your current location with #kp. find new locations with #(exp)lore.',
						'exit' => 'Leader command. Usage: #exit. Exit a location and return outside of it in the same city. To leave a dungeon, use #leave in the dungeons exit.',
						'sleep' => "Leader command. Usage: #sleep. In Hotels and some other locations, you can sleep to rest and restore your HP and MP.",
						'stop' => "Leader command. Usage: #stop. The leader can interrupt a moving party with the stop command. Your #(exp)lore and #(g)oto timers will reset.",
					),
					
					'party_cmds' => 'Party commands',
					array(
						'leader' => 'Leader command. Usage: #(le)ader <member_name|member_enum>. Set a new party leader for your party.',
						'request_leader' => 'Player command. Usage: #request_leader|#rl. Request leadership in case the current leader is away.',
						'join' => 'Player command. Usage: #(j)oin <player>. Join the party of another player who is at the same location as you. You can #look to see which players are around you.',
						'part' => 'Player command. Usage: #part. Leave your current party and create a fresh one.',
						'fight' => 'Leader Command. Usage: #fight [<player>]. When you meet talking parties on the street you can #fight them. This command also works inside PVP locations.',
						'bye' => 'Leader Command. Usage: #bye. Say goodbye to a talking party.',
						'party_loot' => "Leader command. Usage: #pl <kill|cycle|rand>. Define who loots the items from kills. The default is killer.",
						'info' => "Player command. Usage: #info. Show the info text of your current location.",
						'ban' => 'Leader command. Usage: #ban [<player>]. Ban all or one player from your party. If no argument is given you ban all players.',
						'unban' => 'Leader command. Usage: #unban [<player>]. Unban all or one player from your party. If no argument is given your party will be open to all players again.',
					),
					
					'mount_cmds' => 'Mount commands',
					array(
						'mount' => 'Player command. Usage #mount [<push|pop|clean>] [<item>]. Show your mount or push and pop items from it. Items in your mount can be stolen via #hijack from other players.',
						'mounts' => 'Player command. Usage #mounts. Show all the mounts in your party.',
					),
					
					'location_cmds' => 'All location commands',
					array(
					
						'look' => "Player command. Usage: #look. Look around to spot other players in your current location. You can see players from other servers too.",
					
						'shop_cmds' => 'Store commands',
						array(
							'buy' => 'Location command. Usage: #buy <view_id|item_name>. In shops you can buy items with this command. The price depends on your negotiation.',
							'sell' => 'Location command. Usage: #sell <inv_id|item_name>. In shops you can sell your items with this command. The price depends on your negotiation.',
							'view' => 'Location command. Usage: #view [<view_id>]. In shops you can view the shops items or examine a shop item with this command.',
						),
						
						'bank_cmds' => 'Bank commands',
						array(
							'pushi' => "Location command. Usage: #pushi <item>. In banks you can store items and nuyen to keep them safe for later usage.",
							'pushy' => "Location command. Usage: #pushy <amount>. In banks you can store items and nuyen to keep them safe for later usage.",
							'popi' => "Location command. Usage: #popi <bankid>. In banks you can store items and nuyen to keep them safe for later usage.",
							'popy' => "Location command. Usage: #popy <amount>. In banks you can store items and nuyen to keep them safe for later usage.",
						),

						'smith_cmds' => 'Blacksmith commands',
						array(
							'clean' => 'Location command. Usage: #clean <item>. Will remove all modifiers from an item.',
							'break' => 'Location command. Usage: #break <item>. Will destroy an item and release it`s runes, which you will receive.',
							'split' => 'Location command. Usage: #split <rune>. Will split a rune into multiple runes. Useful to extract modifiers from high level runes.',
							'upgrade' => 'Location command. Usage: #upgrade <item> <rune>. Apply a rune on your equipment. This may fail or even destroy the item.',
							'simulate' => 'Location command. Usage: #simulate <item> <rune>. Simulates an upgrade and prints the odds of fail and destroy.',
						),
						
						'school_cmds' => 'School and Temple commands',
						array(
							'courses' => 'Location command. Usage: #courses. In schools you can show available courses to learn.',
							'learn' => 'Location command. Usage: #learn <course>. In schools you can learn new skills and spells.',
						),
						
						'hospital_cmds' => 'Hospital commands',
						array(
							'implant' => 'Location command. Usage #implant <view_id|item>. In hospitals you can implant cyberware. Cyberware will cost essence, which is important for magic spells.',
							'unplant' => 'Location command. Usage #unplant <cy_id|item>. In hospitals you can unimplant your cyberware, in case it conflicts with other cyberware or you need more essence.',
							'heal_cmd' => 'Location command. Usage #heal. In hospitals you can quickly get healed for a small fee.',
						),
						
						'subway_cmds' => 'Subway commands',
						array(
							'travel' => 'Location command. Usage: #travel [<target>]. In subways you can travel to other cities. When no argument is given, the available targets are shown.',
						),
//						
						'hotel_cmds' => 'Hotel commands',
						array(
							'sleep' => NULL,#'Location command. Usage: #sleep. In hotels you can sleep to recover your parties HP and MP. In some hotels this cost some nuyen.',
						),
						
						'dungeon_cmds' => 'Dungeon commands',
						array(
							'leave' => 'Location command. Usage: #leave. In most dungeons you can goto an exit and issue leave there to leave the dungeon.',
							'search' => 'Location command. Usage: #search. In some dungeon rooms you can use #search to search for hidden items.',
						),
					),
				),
				
				'various' => 'I know about',
				array(
				
					'annoying' => 'Annoying stuff',
					array(
						'annoy_kvirc' => "Add a new event handler for 'OnQueryMessage'. Code: if(\$0 == \"BotNick\"){ echo \$3-; halt; }",
						'bots' => 'You are not allowed to do actions with an automated script (botting). If you get caught all your characters will be deleted. Do not whine! You have been warned!',
						'cheating' => 'If you get caught all your characters will be deleted. Do not whine! You have been warned!',
					),
					
					'location' => "Each (p)arty has a location. There are multiple cities and dungeons with lots of locations. You can #(g)oto your #known_places and #(exp)lore your current city to find more. Check your #kp to see all your known places in the current city.",
				
					'action' => 'Each #(p)arty has an action',
					array(
						'action_delete' => 'This means the party is going to be deleted from database.',
						'action_talk' => 'This means the party is talking to another party.',
						'action_fight' => 'This means the party is fighting another party.',
						'action_inside' => 'This means the party is inside a location.',
						'action_outside' => 'This means the party is outside a location. Use #(en)ter to enter it.',
						'action_sleep' => 'This means the party is sleeping.',
						'action_travel' => 'This means the party is travelling to another city.',
						'action_explore' => 'This means the party is exploring the current city.',
						'action_goto' => 'This means the party is going to a location in the current city.',
						'action_hunt' => 'This means the party is hunting another human player.',
					),
				
					'xp' => "Collect {$xppk} XP and gain 1 Karma. With Karma you can #(l)vlup.",
					'runes' => "You can runecraft items at the Blacksmith, but you need to solve the blacksmith quest first.",
					'decker' => "In the Shadowrun(tm) world, a decker is a hacker, who directly connects to computersystem using cyberdecks.",
					'enum' => "To choose targets with items and spells, you can use enumeration. like #use item 1,2,3, #attack 1,2,3 #spell foo 1,2,3.",
					'statted' => "Statted means crafted, like Cap_of_strength:1. The more modifiers the more complex is your item. The higher the modifiers, the more complex is your item. Adding a new modifier is more complex than increasing the power of the same modifiers.",
					
					'teachers' => 'The teachers',
					array(
						'jmoncayo' => 'jmoncayo is the founder of the \'School of Fireweapons\' in Redmond.',
						'caesum' => 'Caesum is the founder of the \'School of Cryptography and applied Math\' in Seattle.',
						'livinskull' => 'livinskull is the founder of the "School of Computers" in the Amerindian Area.',
						'freeartman' => 'FreeArtMan is the founder of the "School of Electronics" in the Amerindian Area.',
					),
				),
			),
		);
		return $back;
	}
	
//	private static function getItemTree($player)
//	{
//		$back = array('all_items' => 'to be done');
//		return $back;
//	}

	private static function getSpellTree($player)
	{
		$back = array();
		$spells = SR_Spell::getSpells();
		ksort($spells);
		
		foreach ($spells as $name => $spell)
		{
			$spell instanceof SR_Spell;
			if ( ($player === NULL) || ($spell->getBaseLevel($player) < 0) )
			{
				$back[$name] = self::getSpellHelpGuest($spell);
			}
			else
			{
				$back[$name] = self::getSpellHelp($spell, $player);
			}
		}
		return $back;
	}
	
	private static function getSpellHelpGuest(SR_Spell $spell)
	{
		return sprintf('%s %s. %s', $spell->displayType(), $spell->displayClass(), $spell->getHelp());
		
	}
	
	private static function getSpellHelp(SR_Spell $spell, SR_Player $player)
	{
		return sprintf('%s %s. Level %s (%s MP). %s', $spell->displayType(), $spell->displayClass(), $spell->getLevel($player), $spell->getManaCost($player), $spell->getHelp());
	}
	
	###############
	### Trigger ###
	###############
	private static function getHelpRec($topic, &$root, &$results)
	{
		$len = count($root);
		
		for ($i = 0; $i < $len; $i++)
		{
			$item1 = array_slice($root, $i, 1, true);
			$key1 = key($item1);
			$item1 = $item1[$key1];
			
			if (is_array($item1))
			{
				self::getHelpRec($topic, $item1, $results);
			}
			
			elseif ( ($key1 === $topic) && ($item1 !== NULL) )
			{
				$item2 = '';
				if ($i <($len-1))
				{
					$item2 = array_slice($root, $i+1, 1, true);
					$key2 = key($item2);
					$item2 = $item2[$key2];
				}
				
				$results[] = array($item1, $item2);
			}
		}
	}
	
	private static function collectKeysRec(&$root, &$keywords)
	{
		foreach ($root as $key => $value)
		{
			if (is_array($value))
			{
				self::collectKeysRec($root[$key], $keywords);
			}
			else
			{
				if (!in_array($key, $keywords, true))
				{
					$keywords[] = $key;
				}
			}
		}
	}
	
	public static function sort_strlen($a, $b)
	{
		return strlen($b) - strlen($a);
	}
	
	public static function getHelp(SR_Player $player, $topic)
	{
		$bot = Shadowrap::instance($player);
		
		# Shortcuts
		$topic = self::unshortcut($topic);
		
		# Build tree
		$help = self::getAllHelp($player);
		
		# Collect results
		$results = array();
		self::getHelpRec($topic, $help, $results);
		if (count($results) === 0)
		{
			return 'This help topic is unknown.';
		}
		
		# Display
		$back = '';
		$keywords = self::getHighlightKeywords();
		self::collectKeysRec($help, $keywords);
		usort($keywords, array(__CLASS__, 'sort_strlen'));
		foreach ($results as $result)
		{
			$back .= ' '.self::displayResult($bot, $result, $keywords);
		}
		return substr($back, 1);
	}
	
	private static function getHighlightKeywords()
	{
		return array('example');
	}
	
	private static function displayResult(Shadowrap $bot, array $two_items, array &$keywords)
	{
		list($text, $item) = $two_items;
		
		$keys = array();
		if (is_array($item))
		{
			foreach ($item as $key => $var)
			{
				if (is_string($var))
				{
					$keys[] = $key;
				}
			}
			$text .= ': '.implode(', ', $keys).'.';
		}

		# Highlight
		$b = chr(2);
		foreach ($keywords as $keyword)
		{
			$text = preg_replace("/([^a-z0-9_])({$keyword})([^a-z0-9_])/i", "$1{$b}$2{$b}$3", $text);
		}
		
		return $text;
	}
}
?>