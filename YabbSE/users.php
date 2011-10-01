<?php

// Fetch user info
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'members WHERE ID_MEMBER>'.$start.' ORDER BY ID_MEMBER LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['ID_MEMBER'];
	echo '<br>'.htmlspecialchars($ob['memberName']).' ('.$ob['ID_MEMBER'].")\n"; flush();

	// Check for user/guest collision
	if( $ob['ID_MEMBER'] == 1 )
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT ID_MEMBER FROM '.$fdb->prefix."members ORDER BY ID_MEMBER DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['ID_MEMBER'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['ID_MEMBER'];
	}

	// Dataarray
	$todb = array(
		'id'					=>		$ob['ID_MEMBER'],
		'username'			=> 	$ob['memberName'],
		'password'			=> 	$ob['passwd'],
		'url'					=> 	$ob['websiteUrl'],
		'title'				=>		$ob['usertitle'],
		'icq'					=> 	$ob['ICQ'],
		'aim'					=> 	$ob['AIM'],
		'msn'					=>		$ob['MSN'],
		'yahoo'				=> 	$ob['YIM'],
		'signature'			=> 	$ob['signature'],
		'timezone'			=> 	$ob['timeOffset'],
		'num_posts'			=> 	$ob['posts'],
		'registered'		=> 	$ob['dateRegistered'],
		'last_visit'		=> 	$ob['lastLogin'],
		'email_setting'	=>		$ob['hideEmail'],
		'location'			=> 	$ob['location'],
		'email'				=> 	$ob['emailAddress'],
	);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['lastLogin'];

	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('ID_MEMBER', 'members', $last_id);

