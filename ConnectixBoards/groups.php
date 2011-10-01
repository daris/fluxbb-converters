<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'groups WHERE gr_id > 4') or myerror('Unable to fetch groups', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result))
{
	echo htmlspecialchars($ob['gr_name']).' ('.$ob['gr_id'].")<br>\n"; flush();
	
	if (trim($ob['gr_name']) == '')
		$ob['gr_name'] = 'Group';
	
	// Dataarray
	$todb = array(
		'g_id'					=>		$ob['gr_id'], 
		'g_title'			=>		$ob['gr_name'],	
		'g_user_title'		=> 	$ob['gr_name'],
	);

	// Save data
	insertdata('groups', $todb, __FILE__, __LINE__);
}
