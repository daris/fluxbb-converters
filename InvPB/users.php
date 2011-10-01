<?php

// Fetch user info	
$res = $fdb->query('SELECT e.*,m.* FROM '.$fdb->prefix.'members AS m LEFT JOIN '.$fdb->prefix.'member_extra AS e ON m.id=e.id WHERE m.id>'.$start.' ORDER BY m.id LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = 0;
while($ob = $fdb->fetch_assoc($res)){

	$last_id = $ob['id'];
	echo htmlspecialchars($ob['name']).' ('.$ob['id'].")<br>\n"; flush();

	// Check for user/guest collision
	if($ob['id'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT id FROM '.$fdb->prefix."members ORDER BY id DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['id'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['id'];
	}

	// Posts and topics settings
	list($ob['disp_posts'], $ob['disp_topics']) = explode("&", $ob['view_prefs']);
	$ob['disp_posts']  < 3 ? $ob['disp_posts']  = 'null' : null;
	$ob['disp_topics'] < 3 ? $ob['disp_topics'] = 'null' : null;

	// Last_post should be 'null' instead of 0
	$ob['last_post'] == null ? $ob['last_post'] = 'null' : null;

	// Ver 1.3 specific stuff
	if($_SESSION['ver'] == "13") {
		$ob['signature'] = preg_replace('#\<br>#i', "\r\n", $ob['signature']);
	}
	// Ver 1.3 specific stuff
	else {
		$ob['password'] = '';
	}

	// Dataarray
	$todb = array(
		'id'				=>		$ob['id'],
		'username'		=>		$ob['name'],
		'password'		=>		$ob['password'],
		'url'				=>		$ob['website'],
		'icq'				=>		$ob['icq_number'],
		'msn'				=>		$ob['msnname'],
		'aim'				=>		$ob['aim_name'],
		'yahoo'			=>		$ob['yahoo'],
		'signature'		=>		convert_posts($ob['signature']),
		'location'		=>		$ob['location'],
		'timezone'		=>		$ob['time_offset'],
		'num_posts'		=>		$ob['posts'],
		'last_post'		=>		$ob['last_post'],
		'show_img'		=>		$ob['view_img'],
		'show_avatars'	=>		$ob['view_avs'],
		'show_sig'		=>		$ob['view_sigs'],
		'registered'	=>		$ob['joined'],
		'last_visit'	=>		$ob['last_visit'],
		'email_setting'=>		(int)($ob['hide_email'] == "0"),
		'email'			=>		$ob['email'],
	);

	if($_SESSION['pun_version'] == '1.1')
		$todb['last_action'] = $ob['last_activity'];

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('id', 'members', $last_id);
