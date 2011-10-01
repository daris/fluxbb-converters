<?php

// Fetch user info
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE user_id>'.$start.' ORDER BY user_id LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['user_id'];
	echo '<br>'.htmlspecialchars($ob['username']).' ('.$ob['user_id'].")\n"; flush();

	// Fetch last post
	if( $ob['posts'] > 0 ) {
		$post_res = $fdb->query('SELECT datestamp FROM '.$fdb->prefix.'messages WHERE user_id='.$ob['user_id'].' ORDER BY datestamp DESC') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
		list($last_post_date) = $fdb->fetch_row($post_res);
	}
	else {
		$last_post_date = 'null';
	}

	// Check for user/guest collision
	if($ob['user_id'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT user_id FROM '.$fdb->prefix."users ORDER BY user_id DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['user_id'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['user_id'];
	}
	
	// Dataarray
	$todb = array(
		'id'					=>		$ob['user_id'],
		'username'			=> 	$ob['username'],
		'password'			=> 	$ob['password'],
		'email'				=> 	$ob['email'],
		'email_setting'	=>		$ob['hide_email'],
		'signature'			=> 	$ob['signature'],
		'registered'		=> 	$ob['date_added'],
		'num_posts'			=> 	$ob['posts'],
		'last_post'			=>		$last_post_date,

/*
		'url'					=> 	$ob['websiteUrl'],
		'title'				=>		$ob['usertitle'],
		'icq'					=> 	$ob['ICQ'],
		'aim'					=> 	$ob['AIM'],
		'msn'					=>		$ob['MSN'],
		'yahoo'				=> 	$ob['YIM'],
		'timezone'			=> 	$ob['timeOffset'],
		'last_visit'		=> 	$ob['lastLogin'],
		'location'			=> 	$ob['location'],
*/
	);

	// Fetch last message id
	$result = $fdb->query('SELECT datestamp FROM '.$fdb->prefix.'messages WHERE user_id='.$ob['user_id'].' ORDER BY datestamp DESC LIMIT 1') or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
	if($db->num_rows($result))
		$todb['last_post'] = $db->result($result, 0);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['date_last_active'];
	else
		$todb['last_visit'] = $ob['date_last_active'];

	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('user_id', 'users', $last_id);
