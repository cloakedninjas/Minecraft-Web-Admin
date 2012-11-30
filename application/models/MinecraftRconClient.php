<?php

/**
 * Commands for Minecraft v3.1.1
 */

class Application_Model_MinecraftRconClient extends Application_Model_RconClient {

	/**
	 * Blacklists the name playername from the server so that they can no longer connect.
	 * Note: Bans supercede any whitelisting in place.
	 * @param string $playername
	 */
	public function ban($playername) {
		return $this->sendCommand('ban ' . $playername);
	}

	/**
	 * Blacklists an IP address so that all subsequent connections from it are rejected.
	 * @param string $ip
	 */
	public function banIp($ip) {
		return $this->sendCommand('ban-ip ' . $ip);
	}

	/**
	 * Displays the banlist.
	 * If $ips is set, displays banned IP addresses
	 * @param boolean $ips
	 */
	public function banlist($ips = false) {
		if ($ips) {
			return $this->sendCommand('banlist ips');
		}
		else {
			return $this->sendCommand('banlist');
		}
	}

	/**
	 * Revokes a player's operator status.
	 * @param string $playername
	 */
	public function deOp($playername) {
		return $this->sendCommand('deop ' . $playername);
	}

	/**
	 * Changes the game mode for playername to.
	 * Survival (0),
	 * Creative (1),
	 * Adventure (2)
	 * @param int $mode
	 * @param string $playername
	 */
	public function gameMode($mode, $playername) {
		return $this->sendCommand('gamemode ' . $mode . ' ' . $playername);
	}

	/**
	 * Spawns amount (defaults to 1) of the item defined by data-value with the specified damage value (defaults to 0) at playername's location
	 * @param string $playername
	 * @param int $item
	 * @param int $amount
	 * @param string $dmg_val
	 */
	public function give($playername, $item, $amount=null, $dmg_val=null) {
		$cmd = 'give ' . $playername . ' ' . $item;

		if ($amount !== null) {
			$cmd .= ' ' . intval($amount);
		}

		if ($dmg_val !== null) {
			$cmd .= ' ' . $dmg_val;
		}

		return $this->sendCommand($cmd);
	}

	/**
	 * Forcibly disconnects playername from the server.
	 * @param string $playername
	 */
	public function kick($playername) {
		return $this->sendCommand('kick ' . $playername);
	}

	/**
	 * Kill the player
	 * @param string $playername
	 */
	public function kill($playername) {
		return $this->sendCommand('kill ' . $playername);
	}

	/**
	 * Shows the names of all currently-connected players.
	 */
	public function listPlayers() {
		return $this->sendCommand('list');
	}

	/**
	 * Sends a narrative message to the other players in the form of "* Server actiontext" (e.g., "* Server sneezes.").
	 * @param string $actiontext
	 */
	public function me($actiontext) {
		return $this->sendCommand('me ' . $actiontext);
	}

	/**
	 * Grants playername operator status on the server.
	 * @param string $playername
	 */
	public function op($playername) {
		return $this->sendCommand('op ' . $playername);
	}

	/**
	 * Removes playername from the blacklist, allowing them to connect again.
	 * @param string $playername
	 */
	public function pardon($playername) {
		return $this->sendCommand('pardon ' . $playername);
	}

	/**
	 * Removes ip-address from the IP blacklist, allowing players from that IP address to connect to the server.
	 * @param string $ip
	 */
	public function pardonIp($ip) {
		return $this->sendCommand('pardon-ip ' . $ip);
	}

	/**
	 * Forces the server to write all pending changes to the world to disk.
	 */
	public function saveAll() {
		return $this->sendCommand('save-all');
	}

	/**
	 * Disables the server writing to the world files. All changes will temporarily be queued.
	 */
	public function saveOff() {
		return $this->sendCommand('save-off');
	}

	/**
	 * Enables the server writing to the world files. This is the default behaviour.
	 */
	public function saveOn() {
		return $this->sendCommand('save-on');
	}

	/**
	 * Broadcasts message to all players on the server (in bright pink letters)
	 * @param string $message
	 */
	public function say($message) {
		return $this->sendCommand('say ' . $message);
	}

	/**
	 * Gracefully shuts down the server.
	 */
	public function stop() {
		return $this->sendCommand('stop');
	}

	/**
	 * Used to private message a player on the server.
	 * @param string $player
	 * @param string $message
	 */
	public function tell($player, $message) {
		return $this->sendCommand('tell ' . $player . ' ' . $message);
	}

	/**
	 * Set or increment the world time. number is an integer between 0 and 24000, inclusive, where 0 is dawn, 6000 midday, 12000 dusk and 18000 midnight (i.e. the clock is bisected; left side is night, right side is day).
	 * Note: You can also subtract from the current time by using the 'add X' modifier, by using a negative value (i.e. 'time add -6000' would change midnight to dusk)
	 * @param string $method
	 * @param int $number
	 */
	public function time($method, $number) {
		return $this->sendCommand('time ' . $method . ' ' . $number);
	}

	/**
	 * Toggles rain and snow.
	 */
	public function toggleDownfall() {
		return $this->sendCommand('toggledownfall');
	}

	/**
	 * Teleports player playername to targetplayer's location or x,y,z coordinates
	 */
	public function tp() {
		$args = func_get_args();

		if (func_num_args() == 2) {
			return $this->sendCommand('tp ' . $args[0] . ' ' . $args[1]);
		}
		elseif (func_num_args() == 4) {
			return $this->sendCommand('tp ' . $args[0] . ' ' . $args[1] . ' ' . $args[2] . ' ' . $args[3]);
		}
		else {
			return false;
		}
	}

	/**
	 * Perform actions on the server whitelist, including:<br />
	 * add | remove,
	 * list,
	 * on | off,
	 * reload
	 * @param string $method
	 * @param string $playername
	 */
	public function whiteList($method, $playername=null) {
		if ($method == 'add' || $method == 'remove') {
			return $this->sendCommand('whitelist ' . $method . ' ' . $playername);
		}
		else {
			return $this->sendCommand('whitelist ' . $method);
		}

	}

	/**
	 * Gives the specified user the given number of orbs. Maximum is 5000 per command.
	 * Negative amounts may be used to remove experience progress, but not actual levels.
	 * @param int $amount
	 * @param string $playername
	 */
	public function xp($amount, $playername) {
		return $this->sendCommand('xp ' . $amount . ' ' . $playername);
	}

	/**
	 * Sets the default game mode that is shown on the world selection menu.
	 * New players that join the world will be put into the default game mode
	 * @param int $mode
	 */
	public function defaultGameMode($mode) {
		return $this->sendCommand('defaultgamemode ' . $mode);
	}

}

