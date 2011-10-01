<?php

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'members WHERE ID_MEMBER>'.$start.' ORDER BY ID_MEMBER LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = 0;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['ID_MEMBER'];
	echo htmlspecialchars($ob['memberName']).' ('.$ob['ID_MEMBER'].")<br>\n"; flush();

	// Fetch last post info
	$post_res = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE ID_MEMBER='.$ob['ID_MEMBER'].' ORDER BY posterTime DESC LIMIT 1') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	if( $fdb->num_rows($post_res) > 0 ) {
		$last_ob = $fdb->fetch_assoc($post_res);
		$ob['last_post_time'] = $last_ob['posterTime'];
	}
	else {
		$ob['last_post_time'] = 'null';
	}

	// Check for user/guest collision
	if($ob['ID_MEMBER'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT ID_MEMBER FROM '.$fdb->prefix."members ORDER BY ID_MEMBER DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['ID_MEMBER'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['ID_MEMBER'];
	}
	
	if ($ob['ID_GROUP'] == 1)
		$ob['ID_GROUP'] = PUN_ADMIN;
	elseif ($ob['ID_GROUP'] == 2 || $ob['ID_GROUP'] == 3)
		$ob['ID_GROUP'] = PUN_MOD;
	else
		$ob['ID_GROUP'] = PUN_MEMBER;
	
	// Dataarray
	$todb = array(
		'id'					=>		$ob['ID_MEMBER'],
		'group_id'				=>		$ob['ID_GROUP'],
		'username'			=>		$ob['memberName'],
		'password'			=>		$ob['passwd'],
		'url'					=>		$ob['websiteUrl'],
		'icq'					=>		$ob['ICQ'],
		'msn'					=>		$ob['MSN'],
		'aim'					=>		$ob['AIM'],
		'yahoo'				=>		$ob['YIM'],
		'signature'			=>		$ob['signature'],
		'timezone'			=>		$ob['timeOffset'],
		'num_posts'			=>		$ob['posts'],
		'last_post'			=>		$ob['last_post_time'],
		'registered'		=>		$ob['dateRegistered'],
		'last_visit'		=>		$ob['lastLogin'],
		'location'			=>		$ob['location'],
		'email'				=>		$ob['emailAddress'],
		'email_setting'	=>		!$ob['hideEmail'],
	);

	if($_SESSION['pun_version'] == '1.1') 
		$todb['last_action'] = $ob['lastLogin'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('ID_MEMBER', 'members', $last_id);
