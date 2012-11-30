<?php
$start = microtime(true);

include_once("rcon.class.php");

// Extend the rcon class to tweak it for minecraft.
class minecraftRcon extends rcon {

	function Auth () {
		global $start;


		$PackID = $this->_Write(SERVERDATA_AUTH,$this->Password);

		// Real response (id: -1 = failure)

		$ret = $this->_PacketRead();

		return $ret[0]['ID'] != -1;
	}

	function mcRconCommand($Command) {
		$this->_Write(SERVERDATA_EXECCOMMAND,$Command,'');

		$ret = $this->Read();

		return $ret[$this->_Id]['S1'];
	}
}

// Server connection varialbes
$server = "localhost";
$rconPort = 25575;
$rconPass = "cockmuncher";

// Connect to the server
$r = new minecraftRcon($server, $rconPort, $rconPass);

//var_dump($r->mcRconCommand('list'));

// Authenticate, and if so, execute command(s)
if ( $r->Auth() ) {
	//$end = microtime(true);
	//echo "<p>" . ($end - $start) . "</p>";
	echo "Authenticated\n";

// Send a command
var_dump($r->mcRconCommand('list'));
}

//$end = microtime(true);
//echo "<p>" . ($end - $start) . "</p>";