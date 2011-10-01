<?php

// Fetch user info
$result = $fdb->query('SELECT userid,name,email FROM '.$fdb->prefix.'messages GROUP BY name ORDER BY userid') or myerror('phpBB: Unable to get table: users', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['userid'];
	echo '<br>'.htmlspecialchars($ob['name']).' ('.$ob['userid'].")\n"; flush();

	// Last post
	$res = $fdb->query('SELECT time FROM '.$fdb->prefix.'messages WHERE userid ='.$ob['userid'].' ORDER BY time DESC LIMIT 1') or myerror("Unable to get user indo", __FILE__, __LINE__, $db->error());
	$last_post = $fdb->result($res, 0);

	// Post count
	$res = $fdb->query('SELECT count(*) AS count FROM '.$fdb->prefix.'messages WHERE userid ='.$ob['userid']) or myerror("Unable to get post count", __FILE__, __LINE__, $fdb->error());
	list($post_count) = $fdb->fetch_row($res);

	// "Registered" (first post)
	$res = $fdb->query('SELECT time FROM '.$fdb->prefix.'messages WHERE userid ='.$ob['userid'].' ORDER BY time ASC LIMIT 1') or myerror("Unable to get post count", __FILE__, __LINE__, $fdb->error());
	list($registered) = $fdb->fetch_row($res);

	// Check for user/guest collision
	if($ob['userid'] == 1)
	{
		// Fetch last user id
		$last_result = $fdb->query('SELECT userid FROM '.$fdb->prefix."messages ORDER BY userid DESC LIMIT 1") or myerror('Unable to fetch last user id', __FILE__, __LINE__, $fdb->error());
		list($last_user_id) = $fdb->fetch_row($last_result);
		$ob['userid'] = ++$last_user_id;
		$_SESSION['admin_id'] = $ob['userid'];
	}

	// Dataarray
	$todb = array(
		'id'					=>		$ob['userid'],
		'username'			=>		$ob['name'],
		'email'				=>		$ob['email'],
//			'password'			=>		$ob['user_password'],
//			'url'					=>		$ob['user_website'],
//			'icq'					=>		$ob['user_icq'],
		'num_posts'			=>		$post_count,
		'last_post'			=>		$last_post,
		'registered'		=>		$registered,
//			'location'			=>		$ob['user_from'],
	);

	// Save data
	insertdata('users', $todb, __FILE__, __LINE__);
}
/*
// More rows in database?
$result = $fdb->query('SELECT id FROM '.$fdb->prefix.'messages WHERE id>'.$last_id.' GROUP BY name ORDER BY id') or error('Unable to get count value for table: '.$name, __FILE__, __LINE__, $fdb->error());
if(@$fdb->num_rows($result))
	echo '<script type="text/javascript">window.location="index.php?page='.$_GET['page'].'&start='.$last_id.'"</script>';
else
	echo '<script type="text/javascript">window.location="index.php?page='.++$_GET['page'].'"</script>';
*/