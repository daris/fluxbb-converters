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
		'g_id'				=>	$ob['gr_id'], 
		'g_title'			=>	$ob['gr_name'],	
		'g_user_title'		=> 	$ob['gr_name'],
	);

	// For FluxBB 1.5 compatibility
	if(substr(FORUM_VERSION,0,3) == '1.5')
	{
		if ($ob['gr_cond'] >= 0)
		{
			$g = $fdb->query('SELECT gr_id, MIN(gr_cond) AS gr_next FROM '.$fdb->prefix.'groups WHERE gr_cond > '.$ob['gr_cond']) or error('Unable to fetch next group', __FILE__, __LINE__, $fdb->error());
			$group = $fdb->fetch_assoc($g);
			$todb['g_promote_min_posts'] = $ob['gr_cond'];
			$todb['g_promote_next_group'] = $group['gr_next'];
		}
		else
		{
			$todb['g_promote_min_posts'] = 0;
			$todb['g_promote_next_group'] = 0;
		}
	}

	// Save data
	insertdata('groups', $todb, __FILE__, __LINE__);
}
