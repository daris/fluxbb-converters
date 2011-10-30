<?php
//Look for banned IPs
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'banned') or myerror("Unable to get ban data");

while($ob = $fdb->fetch_assoc($result))
{
	// Dataarray
	$todb = array(
		'ip'		=> ($ob['ban_ip'] == '') ? 'null' : long2ip($ob['ban_ip']),
		'expire'	=> ($ob['ban_expires'] == '') ? 'null' : $ob['ban_expires']
	);

	// Save data
	insertdata('bans', $todb, __FILE__, __LINE__);		
}
