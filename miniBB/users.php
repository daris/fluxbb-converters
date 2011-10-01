<?php

// Fetch user info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE user_id>'.$start.' ORDER BY user_id LIMIT '.ceil($_SESSION['limit']/5)) or myerror('phpBB: Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['user_id'];
	echo '<br>'.htmlspecialchars($ob['username']).' ('.$ob['user_id'].")\n"; flush();
	
	// Last post
	$lastresult = $fdb->query('SELECT post_time FROM '.$fdb->prefix.'posts WHERE poster_id ='.$ob['user_id'].' ORDER BY poster_id DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $db->error());
	$time_string = $fdb->result($lastresult, 0);
	$last_post = $time_string != '' ? strtotime($time_string) : 'null';

	// Post count
	$res = $fdb->query('SELECT count(*) AS count FROM '.$fdb->prefix.'posts WHERE poster_id ='.$ob['user_id']) or myerror("Unable to get post count", __FILE__, __LINE__, $fdb->error());
	list($post_count) = $fdb->fetch_row($res);

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
		'username'			=>		$ob['username'],
		'password'			=>		$ob['user_password'],
		'url'					=>		$ob['user_website'],
		'icq'					=>		$ob['user_icq'],
		'num_posts'			=>		$post_count,
		'last_post'			=>		$last_post,
		'registered'		=>		strtotime($ob['user_regdate']),
		'location'			=>		$ob['user_from'],
		'email'				=>		$ob['user_email'],
	);

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

// More users?
convredirect('user_id', 'users', $last_id);
