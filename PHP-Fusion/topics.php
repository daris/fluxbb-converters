<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'threads WHERE thread_id > '.$start.' ORDER BY thread_id LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['thread_id'];
	echo htmlspecialchars($ob['thread_subject']).' ('.$ob['thread_id'].")<br>\n"; flush();

	// Select post count
	$res = $fdb->query('SELECT count(*) as count FROM '.$fdb->prefix.'posts WHERE thread_id='.$ob['thread_id']) or myerror('Unable to fetch post count', __FILE__, __LINE__, $fdb->error());
	$item = $fdb->fetch_assoc($res);
	$post_count = $item['count'];
	
	// Select poster
	$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'users WHERE user_id='.$ob['thread_author']) or myerror('Unable to fetch topic author name', __FILE__, __LINE__, $fdb->error());
	$item = $fdb->fetch_assoc($res);
	$author_name = $item['user_name'];

	// Fetch last post info
	$res = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts AS p, '.$fdb->prefix.'users AS u WHERE p.post_author=u.user_id AND thread_id='.$ob['thread_id'].' ORDER BY post_datestamp DESC LIMIT 1') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	$last_post = $fdb->fetch_assoc($res);

	// Dataarray
	$todb = array(
		'id'				=>		$ob['thread_id'],
		'poster'			=>		$author_name,
		'subject'		=>		$ob['thread_subject'],
//			'posted'			=>		
		'num_views'		=>		$ob['thread_views'],
		'num_replies'	=>		$post_count - 1,
		'last_post'		=>		$last_post['post_datestamp'],
		'last_post_id'	=>		$last_post['post_id'],
		'last_poster'	=>		$last_post['user_name'],
		'sticky'			=>		$ob['thread_sticky'],
		'closed'			=>		$ob['thread_locked'],
		'forum_id'		=>		$ob['forum_id'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('thread_id', 'threads', $last_id);
