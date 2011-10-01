<?php

if ($db->field_exists('users', 'salt'))
	$db->alter_field('users', 'salt', 'char(3)', false, '');
else
	$db->add_field('users', 'salt', 'char(3)', false, '', 'password');

// Fetch user info
$res = $fdb->query('SELECT u.*,t.signature,f.field2 AS location FROM '.$fdb->prefix.'user AS u, '.$fdb->prefix.'usertextfield AS t, '.$fdb->prefix.'userfield AS f WHERE t.userid=u.userid AND f.userid=u.userid AND u.userid>'.$start.' ORDER BY u.userid LIMIT '.ceil($_SESSION['limit']/5)) or myerror('Unable to fetch user info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($res))
{
	$last_id = $ob['userid'];
	echo '<br>'.htmlspecialchars($ob['username']).' ('.$ob['userid'].")\n"; flush();

	// Settings
	$ob['status'] = 0;
	$ob['usergroupid'] == 6 ? $ob['status'] = 2 : null;
	$ob['usergroupid'] == 7 ? $ob['status'] = 1 : null;

	// Fetch last_post_time
	$result = $fdb->query('SELECT dateline FROM '.$fdb->prefix.'post WHERE userid='.$ob['userid'].' ORDER BY postid DESC LIMIT 1') or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
	$ob['dateline'] = $fdb->num_rows($result) > 0 ? $fdb->result($result, 0) : 'null';

	// Check for user/guest collision
	if( $ob['userid'] == 1 )
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT userid FROM '.$fdb->prefix."user ORDER BY userid DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['userid'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['userid'];
	}

	// Dataarray
	$todb = array(
		'id'					=>		$ob['userid'],
		'username'			=> 	$ob['username'],
		'password'			=> 	$ob['password'],
		'salt'				=> 	$ob['salt'],
		'url'					=> 	$ob['homepage'],
		'icq'					=> 	$ob['icq'],
		'msn'					=> 	$ob['msn'],
		'aim'					=> 	$ob['aim'],
		'yahoo'				=> 	$ob['yahoo'],
		'signature'			=> 	convert_posts($ob['signature']),
		'timezone'			=> 	$ob['timezoneoffset'],
		'num_posts'			=> 	$ob['posts'],
		'last_post'			=> 	$ob['dateline'],
		'registered'		=> 	$ob['joindate'],
		'last_visit'		=> 	$ob['lastvisit'],
		'location'			=> 	$ob['location'],
		'email'				=> 	$ob['email'],
	);

	if($_SESSION['pun_version'] == '1.1') {
		$todb['status'] = $ob['status'];
	}
	else {
		if($ob['status'] == 2)
			$todb['group_id'] = 1;
	}

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}

convredirect('userid', 'user', $last_id);
