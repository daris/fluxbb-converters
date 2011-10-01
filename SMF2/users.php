<?php

if ($db->field_exists('users', 'salt'))
	$db->alter_field('users', 'salt', 'varchar(255)', false, '');
else
	$db->add_field('users', 'salt', 'varchar(255)', false, '', 'password');

// Fetch user info	
$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'members WHERE id_member>'.$start.' ORDER BY id_member LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = 0;
while ($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['id_member'];
	echo htmlspecialchars($ob['member_name']).' ('.$ob['id_member'].")<br>\n"; flush();

	// Fetch last post info
	$post_res = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE id_member='.$ob['id_member'].' ORDER BY poster_time DESC LIMIT 1') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	if ($fdb->num_rows($post_res) > 0)
	{
		$last_ob = $fdb->fetch_assoc($post_res);
		$ob['last_post_time'] = $last_ob['poster_time'];
	}
	else
		$ob['last_post_time'] = 'null';

	// Check for user/guest collision
	if ($ob['id_member'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT id_member FROM '.$fdb->prefix."members ORDER BY id_member DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['id_member'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['id_member'];
	}
	
	if ($ob['id_group'] == 1)
		$ob['id_group'] = PUN_ADMIN;
	elseif ($ob['id_group'] == 2 || $ob['id_group'] == 3)
		$ob['id_group'] = PUN_MOD;
	else
		$ob['id_group'] = PUN_MEMBER;
	
	// Dataarray
	$todb = array(
		'id'					=>		$ob['id_member'],
		'group_id'				=>		$ob['id_group'],
		'username'			=>		$ob['member_name'],
		'password'			=>		$ob['passwd'],
		'salt'				=>		$ob['password_salt'],
		'url'					=>		$ob['website_url'],
		'icq'					=>		$ob['icq'],
		'msn'					=>		$ob['msn'],
		'aim'					=>		$ob['aim'],
		'yahoo'				=>		$ob['yim'],
		'signature'			=>		$ob['signature'],
		'timezone'			=>		$ob['time_offset'],
		'num_posts'			=>		$ob['posts'],
		'last_post'			=>		$ob['last_post_time'],
		'registered'		=>		$ob['date_registered'],
		'last_visit'		=>		$ob['last_login'],
		'location'			=>		$ob['location'],
		'email'				=>		$ob['email_address'],
	);

	if ($_SESSION['pun_version'] == '1.1') 
		$todb['last_action'] = $ob['lastLogin'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('id_member', 'members', $last_id);
