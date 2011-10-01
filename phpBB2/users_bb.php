<?php

$groups = array();
$group_result = $db->query('SELECT g_id FROM '.$db->prefix.'groups') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $db->error());
while ($cur_group = $db->fetch_assoc($group_result))
	$groups[] = $cur_group['g_id'];

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE uid>'.$start.' ORDER BY uid LIMIT '.$_SESSION['limit']) or myerror('Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['uid'];

	echo htmlspecialchars($ob['uname']).' ('.$ob['uid'].")<br>\n"; flush();

	// Rank -> Title
	$title = null;
	if($ob['rank'] != 0)
	{
		$res = $fdb->query('SELECT rank_title FROM '.$fdb->prefix.'ranks WHERE rank_id ='.$ob['rank'].' LIMIT 1') or myerror("Unable to get user rank", __FILE__, __LINE__, $fdb->error());
		list($title) = $fdb->fetch_row($res);
		
		if ($title == 'Site Admin')
			$title = null;
	}

	// Last post
	$lastresult = $fdb->query('SELECT post_time FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts WHERE uid ='.$ob['uid'].' ORDER BY post_id DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $fdb->error());
	$last = $fdb->fetch_assoc($lastresult);
	$last['post_time'] == 0 ? $last['post_time'] = 'null' : null;

	// Check for user/guest collision
/*		if($ob['uid'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT uid FROM '.$fdb->prefix."users ORDER BY uid DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_uid) = $fdb->fetch_row($last_result);
		$ob['uid'] = ++$last_uid;
		$_SESSION['admin_id'] = $ob['uid'];
	}*/

	// Group id
	$group_result = $fdb->query('SELECT MIN(groupid) FROM '.$fdb->prefix.'groups_users_link WHERE uid='.$ob['uid']) or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
	if ($fdb->num_rows($group_result))
		list($group_id) = $fdb->fetch_row($group_result);
	else
		$group_id = 3;

	// Convert phpbb group id to fluxbb group id
	if ($group_id == 1)
		$group_id = PUN_ADMIN;
	elseif ($group_id == 2)
		$group_id = PUN_MEMBER;
	elseif ($group_id == 3)
		$group_id = PUN_GUEST;
/*		else
		++$group_id;
*/			
/*		if ($group_id > $group_count) // something went wrong
		$group_id = PUN_MEMBER; // set as user
*/
	if (!in_array($group_id, $groups))
		$group_id = PUN_MEMBER;
	
	// Dataarray
	$todb = array(
		'id'				=>		++$ob['uid'],
		'group_id'			=>		$group_id,
		'username'		=>		$ob['uname'],
		'password'		=>		$ob['pass'],
		'title'			=>		$title,
		'url'				=>		$ob['url'],
		'icq'				=>		$ob['user_icq'],
		'msn'				=>		$ob['user_msnm'],
		'aim'				=>		$ob['user_aim'],
		'yahoo'			=>		$ob['user_yim'],
		'num_posts'		=>		$ob['posts'],
		'last_post'		=>		$last['post_time'],
		'location'		=>		$ob['user_from'],
		'email_setting'=>		!$ob['user_viewemail'],
		'timezone'		=>		(int)$ob['timezone_offset'],
		'last_visit'	=>		$ob['last_login'],
		'signature'		=>		convert_posts($ob['user_sig']),
		'email'			=>		$ob['email'],
	);

	// Handle the user registered date
	$todb['registered'] = intval($ob['user_regdate']) > 0 ? $ob['user_regdate'] : strtotime($ob['user_regdate']);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['last_login'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('uid', 'users', $last_id);
