<?php
/**
 * Abstract Lamb Module
 * @author gizmore
 * @version 3.0
 * @since 1.0
 */
abstract class Lamb_Module
{
	############
	### Name ###
	############
	private $name;
	public function getName() { return $this->name; }
	public function setName($name) { $this->name = $name; }
	
	############
	### Init ###
	############
	private $inited = array();
	public function initServer(Lamb_Server $server)
	{
		$sid = $server->getID();
		if (!in_array($sid, $this->inited))
		{
			$this->inited[] = $sid;
			return $this->onInitServer($server);
		}
		return false;
	}
	
	############
	### Help ###
	############
	public function getHelpText($command)
	{
		$h = $this->getHelp();
		if (!isset($h[$command]))
		{
			return '';
		}
		return str_replace('%CMD%', LAMB_TRIGGER.$command, $h[$command]);
	}
	
	# --------------------------- #
	# --- Override Below here --- #
	# --------------------------- #
	
	###############
	### Getters ###
	###############
	public function getHelp() { return array(); }
	public function getTriggers($priviledge) { return array(); }
	################
	### Triggers ###
	################
	public function onInstall() {} # Always called once before startup.
	public function onInit() {} # Called once on startup.
	public function onInitServer(Lamb_Server $server) {} # Called when a server goes online.
	public function onOnline() {} # Called once after all severs are online.
	public function onInitTimers() {} # Called after onOnline and after a timer flush. 
	public function onEvent(Lamb $bot, Lamb_Server $server, $event, $from, $args) {}
	public function onJoin(Lamb_Server $server, Lamb_User $user, Lamb_Channel $channel) {}
	public function onCTPC(Lamb_Server $server, Lamb_User $user, $from, $origin, $message) {}
	public function onNotice(Lamb_Server $server, Lamb_User $user, $from, $origin, $message) {}
	public function onPrivmsg(Lamb_Server $server, Lamb_User $user, $from, $origin, $message) {}
	public function onTrigger(Lamb_Server $server, Lamb_User $user, $from, $origin, $command, $message) {}
}
?>