<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums WHERE forum_id>'.$start.' ORDER BY forum_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['forum_id'];
	echo htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();

	$res = $fdb->query('SELECT post_id,post_time,poster_id FROM '.$fdb->prefix.'posts WHERE forum_id='.$ob['forum_id'].' ORDER BY post_id DESC LIMIT 1') or myerror('Unable to get last-post-id', __FILE__, __LINE__, $fdb->error());
	$post = $fdb->fetch_assoc($res);
	
	$post['poster_id'] == 0 ? $post['poster_id'] = 1 : null;
	if($post['poster_id'] == 0)
		$post['username'] = "Guest";
	else{
		$res = $fdb->query('SELECT username FROM '.$fdb->prefix.'users WHERE user_id='.$post['poster_id']) or myerror('Unable to get last-poster-name', __FILE__, __LINE__, $fdb->error());	
		list($post['username']) = $fdb->fetch_row($res);
	}
	$post['poster_id'] == 1 ? $post['poster_id'] = $_SESSION['admin_id'] : null;

	$res = $fdb->query('SELECT count(*) AS count FROM '.$fdb->prefix.'posts WHERE forum_id='.$ob['forum_id']) or myerror('Unable to get post-count', __FILE__, __LINE__, $fdb->error());	
	list($post_count) = $fdb->fetch_row($res);

	$res = $fdb->query('SELECT count(*) AS count FROM '.$fdb->prefix.'topics WHERE forum_id='.$ob['forum_id']) or myerror('Unable to get post-count', __FILE__, __LINE__, $fdb->error());	
	list($topic_count) = $fdb->fetch_row($res);

	// Dataarray
	$todb = array(
		'id'				=>		$ob['forum_id'],
		'forum_name'	=>		$ob['forum_name'],
		'forum_desc'	=>		$ob['forum_desc'],
		'num_topics'	=>		$topic_count,
		'num_posts'		=>		$post_count,
		'disp_position'=>		$ob['forum_order'],
		'last_poster'	=>		$post['username'],
		'last_post_id'	=>		$post['post_id'],
		'last_post'		=>		strtotime($post['post_time']),
		'cat_id'			=>		1,
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('forum_id', 'forums', $last_id);
