<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'thread WHERE threadid > '.$start.' ORDER BY threadid LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['threadid'];
	echo '<br>'.htmlspecialchars($ob['title']).' ('.$ob['threadid'].")\n"; flush();

	// Fetch last post id
	$lastres = $fdb->query('SELECT postid,userid FROM '.$fdb->prefix.'post WHERE threadid='.$ob['threadid'].' ORDER BY postid DESC LIMIT 1') or myerror('Unable to get last-post-id', __FILE__, __LINE__, $fdb->error());
	list($ob['last_post_id'], $ob['last_poster_id']) = $fdb->fetch_row($lastres);
	
	// Settings
	// --> Check id=1 collisions
	$ob['last_poster_id'] == 1 ? $ob['last_poster_id'] = $_SESSION['admin_id'] : null;

	// Dataarray
	$todb = array(
		'id'				=>		$ob['threadid'],
		'poster'			=>		$ob['postusername'],
		'subject'		=>		$ob['title'],
		'posted'			=>		$ob['dateline'],
		'num_views'		=>		$ob['views'],
		'num_replies'	=>		$ob['replycount'],
		'last_post'		=>		$ob['lastpost'],
		'last_post_id'	=>		$ob['last_post_id'],
		'last_poster'	=>		$ob['lastposter'],
		'sticky'			=>		$ob['sticky'],
		'closed'			=>		!$ob['open'],
		'forum_id'		=>		$ob['forumid']
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('threadid', 'thread', $last_id);
