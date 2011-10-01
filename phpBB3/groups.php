<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'groups WHERE group_id > 6') or myerror('Unable to fetch groups', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result))
{
	echo htmlspecialchars($ob['group_name']).' ('.$ob['group_id'].")<br>\n"; flush();
	
	// Dataarray
	$todb = array(
		'g_id'					=>		$ob['group_id'],
		'g_title'			=>		$ob['group_name'],	
		'g_user_title'		=> 	$ob['group_name'],
	);

	// Save data
	insertdata('groups', $todb, __FILE__, __LINE__);
}
