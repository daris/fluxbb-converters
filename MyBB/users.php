<?php

if ($db->field_exists('users', 'salt'))
	$db->alter_field('users', 'salt', 'varchar(10)', false, '');
else
	$db->add_field('users', 'salt', 'varchar(10)', false, '', 'password');

$groups = array();
$group_result = $db->query('SELECT g_id FROM '.$db->prefix.'groups') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $db->error());
while ($cur_group = $db->fetch_assoc($group_result))
	$groups[] = $cur_group['g_id'];

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE uid>'.$start.' ORDER BY uid LIMIT '.ceil($_SESSION['limit'])) or myerror('Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['uid'];
	$ob['uid']++;
	echo htmlspecialchars($ob['username']).' ('.$ob['uid'].")<br>\n"; flush();
/*
	// Rank -> Title
	$title = null;
	if($ob['user_rank'] != 0){
		$res = $fdb->query('SELECT rank_title FROM '.$fdb->prefix.$_SESSION['phpnuke'].'ranks WHERE rank_id ='.$ob['user_rank'].' LIMIT 1') or myerror("Unable to get user rank", __FILE__, __LINE__, $fdb->error());
		list($title) = $fdb->fetch_row($res);
		
		if ($title == 'Site Admin')
			$title = null;
	}
*/
	// Last post
	$lastresult = $fdb->query('SELECT dateline FROM '.$fdb->prefix.'posts WHERE uid ='.$ob['uid'].' ORDER BY pid DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $fdb->error());
	$last = $fdb->fetch_assoc($lastresult);
	$last['dateline'] == 0 ? $last['dateline'] = 'null' : null;

	// Check for user/guest collision
/*		if($ob['uid'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT uid FROM '.$fdb->prefix.'users ORDER BY uid DESC LIMIT 1') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_uid) = $fdb->fetch_row($last_result);
		$ob['uid'] = ++$last_uid;
		$_SESSION['admin_id'] = $ob['uid'];
	}
*/
	$group_id = $ob['usergroup'];

	// Convert mybb group id to fluxbb group id
	if ($group_id == 1) 
		$group_id = PUN_GUEST;
	elseif ($group_id == 2) 
		$group_id = PUN_MEMBER;
	elseif ($group_id == 3 || $group_id == 6)
		$group_id = PUN_MOD;
	elseif ($group_id == 4) 
		$group_id = PUN_ADMIN;
	elseif ($group_id == 5)
		$group_id = -1;
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
		'id'				=>		$ob['uid'],
		'group_id'			=>		$group_id,
		'username'		=>		$ob['username'],
		'password'		=>		$ob['password'],
		'salt'			=>		$ob['salt'],
		'title'			=>		'',
		'url'				=>		$ob['website'],
		'icq'				=>		$ob['icq'],
		'msn'				=>		$ob['msn'],
		'aim'				=>		$ob['aim'],
		'yahoo'			=>		$ob['yahoo'],
		'num_posts'		=>		$ob['postnum'],
		'last_post'		=>		$last['dateline'],
		'location'		=>		'',
		'email_setting'=>		$ob['hideemail'],
		'timezone'		=>		$ob['timezone'],
		'last_visit'	=>		$ob['lastvisit'],
		'signature'		=>		convert_posts($ob['signature']),
		'email'			=>		$ob['email'],
		'registered'		=>  $ob['regdate'],
		'registration_ip'	=>  $ob['regip'],
	);

	// Handle the user registered date
//		$todb['registered'] = intval($ob['user_regdate']) > 0 ? $ob['user_regdate'] : strtotime($ob['user_regdate']);

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('uid', 'users', $last_id);
