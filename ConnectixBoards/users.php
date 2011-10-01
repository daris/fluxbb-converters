<?php

$groups = array();
$group_result = $db->query('SELECT g_id FROM '.$db->prefix.'groups') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $db->error());
while ($cur_group = $db->fetch_assoc($group_result))
	$groups[] = $cur_group['g_id'];

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE usr_id>'.$start.' ORDER BY usr_id LIMIT '.$_SESSION['limit']) or myerror('Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while ($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['usr_id'];
	echo htmlspecialchars($ob['usr_name']).' ('.$ob['usr_id'].")<br>\n"; flush();

	// Last post
	$lastresult = $fdb->query('SELECT msg_timestamp FROM '.$fdb->prefix.'messages WHERE msg_userid ='.$ob['usr_id'].' ORDER BY msg_id DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $fdb->error());
	$last = $fdb->fetch_assoc($lastresult);
	$last['msg_timestamp'] == 0 ? $last['msg_timestamp'] = 'null' : null;

	// Convert cb group id to fluxbb group id
	if ($ob['usr_class'] == 1)
		$ob['usr_class'] = PUN_ADMIN;
	elseif ($ob['usr_class'] == 2)
		$ob['usr_class'] = PUN_MOD;
	elseif ($ob['usr_class'] == 3 || $ob['usr_class'] == 4) // user
		$ob['usr_class'] = PUN_MEMBER;
/*		else
		++$group_id;
*/			
/*		if ($group_id > $group_count) // something went wrong
		$group_id = PUN_MEMBER; // set as user
*/
	if (!in_array($ob['usr_class'], $groups))
		$ob['usr_class'] = PUN_MEMBER;

	if ($ob['usr_punished'] != '')
	{
		list($type,$start,$expires) = explode('|', $ob['usr_punished']);
		
		//Dataarray
		$todb = array(
			'username'	=> $ob['usr_name'],
			'email'		=> $ob['usr_email'],
			'ip'		=> long2ip($ob['usr_ip']),
			'expire'	=> $start + $expires,
		);
	
		// Save data
		insertdata('bans', $todb, __FILE__, __LINE__);
	}
	
	// Dataarray
	$todb = array(
		'id'				=>		++$ob['usr_id'],
		'group_id'			=>		$ob['usr_class'],
		'username'		=>		$ob['usr_name'],
		'password'		=>		$ob['usr_password'],
		'url'				=>		$ob['usr_website'],
		'icq'				=>		$ob['usr_icq'],
		'msn'				=>		$ob['usr_msn'],
		'aim'				=>		$ob['usr_aim'],
		'yahoo'			=>		$ob['usr_yahoo'],
		'num_posts'		=>		$ob['usr_nbmess'],
		'last_post'		=>		$last['msg_timestamp'],
		'location'		=>		$ob['usr_place'],
		'email_setting'=>		!$ob['usr_publicemail'],
		'timezone'		=>		(int)$ob['usr_pref_timezone'],
		'dst'			=>		$ob['usr_pref_ctsummer'],
		'last_visit'	=>		$ob['usr_lastconnect'],
		'signature'		=>		convert_posts($ob['usr_signature']),
		'email'			=>		$ob['usr_email'],
		'registration_ip' =>	long2ip($ob['usr_ip']),
		'realname'		=>		$ob['usr_realname'],
	);

	// Handle the user registered date
	$todb['registered'] = intval($ob['usr_registertime']) > 0 ? $ob['usr_registertime'] : strtotime($ob['usr_registertime']);

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('usr_id', 'users', $last_id);
