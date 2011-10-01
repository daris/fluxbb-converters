<?php
// Xoops with newbb module
if ($_SESSION['phpnuke'] == 'bb_')
{
	require 'users_bb.php';
	return;
}

$groups = array();
$group_result = $db->query('SELECT g_id FROM '.$db->prefix.'groups') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $db->error());
while ($cur_group = $db->fetch_assoc($group_result))
	$groups[] = $cur_group['g_id'];

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE user_id>'.$start.' ORDER BY user_id LIMIT '.$_SESSION['limit']) or myerror('Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['user_id'];
	echo htmlspecialchars($ob['username']).' ('.$ob['user_id'].")<br>\n"; flush();

	// Rank -> Title
	$title = null;
	if($ob['user_rank'] != 0)
	{
		if ($fdb->table_exists($fdb->prefix.$_SESSION['phpnuke'].'ranks', true))
		{
			$res = $fdb->query('SELECT rank_title FROM '.$fdb->prefix.$_SESSION['phpnuke'].'ranks WHERE rank_id ='.$ob['user_rank'].' LIMIT 1') or myerror("Unable to get user rank", __FILE__, __LINE__, $fdb->error());
			list($title) = $fdb->fetch_row($res);
		}
		
		if ($title == 'Site Admin')
			$title = null;
	}

	// Last post
	$lastresult = $fdb->query('SELECT post_time FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts WHERE poster_id ='.$ob['user_id'].' ORDER BY post_id DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $fdb->error());
	$last = $fdb->fetch_assoc($lastresult);
	$last['post_time'] == 0 ? $last['post_time'] = 'null' : null;

/*		// Check for user/guest collision
	if($ob['user_id'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT user_id FROM '.$fdb->prefix.$_SESSION['phpnuke']."users ORDER BY user_id DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['user_id'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['user_id'];
	}*/

	// Group id
	$group_result = $fdb->query('SELECT group_id FROM '.$fdb->prefix.$_SESSION['phpnuke'].'user_group WHERE user_id='.$ob['user_id'].' AND group_id!=3') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
	if ($fdb->num_rows($group_result))
		list($group_id) = $fdb->fetch_row($group_result);
	else
		$group_id = 3;

	// Convert phpbb group id to fluxbb group id
	if ($group_id == 1) // guest
		$group_id = PUN_GUEST;
	elseif ($group_id == 2) // admin
		$group_id = PUN_ADMIN;
	elseif ($group_id == 3) // user
		$group_id = PUN_MEMBER;
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
		'id'				=>		++$ob['user_id'],
		'group_id'			=>		$group_id,
		'username'		=>		$ob['username'],
		'password'		=>		$ob['user_password'],
		'title'			=>		$title,
		'url'				=>		$ob['user_website'],
		'icq'				=>		$ob['user_icq'],
		'msn'				=>		$ob['user_msnm'],
		'aim'				=>		$ob['user_aim'],
		'yahoo'			=>		$ob['user_yim'],
		'num_posts'		=>		$ob['user_posts'],
		'last_post'		=>		$last['post_time'],
		'location'		=>		$ob['user_from'],
		'email_setting'=>		!$ob['user_viewemail'],
		'timezone'		=>		(int)$ob['user_timezone'],
		'last_visit'	=>		$ob['user_lastvisit'],
		'signature'		=>		convert_posts($ob['user_sig']),
		'email'			=>		$ob['user_email'],
	);

	// Handle the user registered date
	$todb['registered'] = intval($ob['user_regdate']) > 0 ? $ob['user_regdate'] : strtotime($ob['user_regdate']);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['user_lastvisit'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('user_id', 'users', $last_id);
