<?php

$group_ids = array(
	1 => PUN_UNVERIFIED,
	2 => PUN_GUEST,
	3 => PUN_MEMBER,
	4 => PUN_ADMIN,
	5 => PUN_UNVERIFIED,
	6 => PUN_MOD
);

if ($db->field_exists('users', 'salt'))
	$db->alter_field('users', 'salt', 'varchar(5)', false, '');
else
	$db->add_field('users', 'salt', 'varchar(5)', false, '', 'password');

// Fetch user info	
$res = $fdb->query('SELECT m.*, f.* FROM '.$fdb->prefix.'members AS m LEFT JOIN '.$fdb->prefix.'pfields_content AS f ON (f.member_id=m.member_id) WHERE m.member_id>'.$start.' ORDER BY m.member_id LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = 0;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['member_id'];
	echo htmlspecialchars($ob['name']).' ('.$ob['member_id'].")<br>\n"; flush();

	// Check for user/guest collision
/*		if($ob['member_id'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT member_id FROM '.$fdb->prefix."members ORDER BY member_id DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['member_id'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['member_id'];
	}*/

	// Posts and topics settings
	list($ob['disp_posts'], $ob['disp_topics']) = explode("&", $ob['view_prefs']);
	$ob['disp_posts']  < 3 ? $ob['disp_posts']  = 'null' : null;
	$ob['disp_topics'] < 3 ? $ob['disp_topics'] = 'null' : null;

	// Last_post should be 'null' instead of 0
	$ob['last_post'] == null ? $ob['last_post'] = 'null' : null;

/*		// Ver 1.3 specific stuff
	if($_SESSION['ver'] == "13") {
		$ob['signature'] = preg_replace('#\<br>#i', "\r\n", $ob['signature']);
	}
	// Ver 1.3 specific stuff
	else {
		$ob['password'] = '';
	}*/

	// Dataarray
	$todb = array(
		'id'				=>		++$ob['member_id'], // first user id is reserved for guest
		'group_id'			=>		$group_ids[$ob['member_group_id']],
		'username'		=>		$ob['name'],
		'password'		=>		$ob['members_pass_hash'],
		'salt'			=>		$ob['members_pass_salt'],
		'title'			=>		$ob['title'],
		'url'				=>		$ob['field_3'],
		'icq'				=>		$ob['field_4'],
		'msn'				=>		$ob['field_2'],
		'aim'				=>		$ob['field_1'],
		'yahoo'			=>		$ob['field_8'],
//		'signature'		=>		convert_posts($ob['signature']),
		'location'		=>		$ob['field_6'],
		'timezone'		=>		$ob['time_offset'],
		'num_posts'		=>		$ob['posts'],
		'last_post'		=>		$ob['last_post'],
		'show_img'		=>		$ob['view_img'],
		'show_avatars'	=>		$ob['view_avs'],
		'show_sig'		=>		$ob['view_sigs'],
		'registered'	=>		$ob['joined'],
		'registration_ip' =>	$ob['ip_address'],
		'last_visit'	=>		$ob['last_visit'],
		'email_setting'=>		(int)($ob['hide_email'] == "0"),
		'email'			=>		$ob['email'],
	);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['last_activity'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('member_id', 'members', $last_id);
