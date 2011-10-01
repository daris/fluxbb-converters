<?php

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE user_id>'.$start.' ORDER BY user_id LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = 0;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['user_id'];
	echo htmlspecialchars($ob['user_name']).' ('.$ob['user_id'].")<br>\n"; flush();

	// Fetch last post info
	$post_res = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts WHERE post_author='.$ob['user_id'].' ORDER BY post_datestamp DESC LIMIT 1') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	if( $fdb->num_rows($post_res) > 0 ) {
		$last_ob = $fdb->fetch_assoc($post_res);
		$ob['last_post_time'] = $last_ob['post_datestamp'];
	}
	else {
		$ob['last_post_time'] = 'null';
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
		'username'			=>		$ob['user_name'],
		'password'			=>		$ob['user_password'],
		'email'				=>		$ob['user_email'],
		'url'					=>		$ob['user_web'],
		'icq'					=>		$ob['user_icq'],
		'msn'					=>		$ob['user_msn'],
//			'aim'					=>		$ob[''],
		'yahoo'				=>		$ob['user_yahoo'],
		'signature'			=>		$ob['user_sig'],
		'timezone'			=>		$ob['user_offset'],
		'num_posts'			=>		$ob['user_posts'],
		'last_post'			=>		$ob['last_post_time'],
		'registered'		=>		$ob['user_joined'],
		'last_visit'		=>		$ob['user_lastvisit'],
		'location'			=>		$ob['user_location'],
		'email_setting'	=>		!$ob['user_hide_email'],
	);

	if($_SESSION['pun_version'] == '1.1') 
		$todb['last_action'] = $ob['user_lastvisit'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('user_id', 'users', $last_id);
