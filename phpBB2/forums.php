<?php
// Fetch forum info
$cat_result = $db->query('SELECT c.id FROM '.$db->prefix.'categories AS c ORDER BY c.id') or myerror('FluxBB: Unable to get table: categories', __FILE__, __LINE__, $db->error());
$categories = array();
while($cur_cat = $db->fetch_assoc($cat_result))
	$categories[] = $cur_cat['id'];

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.$_SESSION['phpnuke'].'forums WHERE forum_id>'.$start.' ORDER BY forum_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['forum_id'];
	echo htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();

	// Check for anonymous poster id problem
//		$ob['forum_last_post_id'] == -1 ? $ob['forum_last_post_id'] = 1 : null;

	// Xoops with newbb module
	if ($_SESSION['phpnuke'] == 'bb_')
		$sql = 'SELECT u.uname as username, p.post_time, p.poster_name as post_username, p.uid as poster_id FROM '.$fdb->prefix.'users AS u, '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p WHERE u.uid=p.uid AND p.post_id='.$ob['forum_last_post_id'];
	else
		$sql = 'SELECT u.username, p.post_time, p.post_username, p.poster_id FROM '.$fdb->prefix.'users AS u, '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p WHERE u.user_id=p.poster_id AND p.post_id='.$ob['forum_last_post_id'];
		
	$userres = $fdb->query($sql) or myerror("Unable to fetch user info for forum conversion.", __FILE__, __LINE__, $fdb->error());
	$userinfo = $fdb->fetch_assoc($userres);
		
	if ($userinfo['poster_id'] == -1)
		$userinfo['username'] = $userinfo['post_username'];
	if ($userinfo['username'] == '')
		$userinfo['username'] = $lang_common['Guest'];

	// Change last_post = 0 to null to prevent the time-bug.
	if (!isset($userinfo['post_time']) || $userinfo['post_time'] == 0)
		$userinfo['post_time'] = 'null';
		
	if ($ob['forum_last_post_id'] == 0)
		$ob['forum_last_post_id'] = 'null';

	// Unset variables
	if(!isset($userinfo['username']))
		$userinfo['username'] = 'null';
		
	// Category does not exist?
	if (!in_array($ob['cat_id'], $categories))
		$ob['cat_id'] = $categories[0];

	// Dataarray
	$todb = array(
		'id'			=>		$ob['forum_id'],
		'forum_name'	=>		$ob['forum_name'],
		'forum_desc'	=>		$ob['forum_desc'],
		'num_topics'	=>		$ob['forum_topics'],
		'num_posts'		=>		$ob['forum_posts'],
		'disp_position'	=>		$ob['forum_order'],
		'last_poster'	=>		$userinfo['username'],
		'last_post_id'	=>		$ob['forum_last_post_id'],
		'last_post'		=>		$userinfo['post_time'],
		'cat_id'		=>		$ob['cat_id'],
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('forum_id', $_SESSION['phpnuke'].'forums', $last_id);
