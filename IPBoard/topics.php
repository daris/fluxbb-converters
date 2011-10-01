<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'topics WHERE tid > '.$start.' ORDER BY tid LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['tid'];
	echo htmlspecialchars($ob['title']).' ('.$ob['tid'].")<br>\n"; flush();

	// Check id=1 collisions
	$ob['starter_id'] == 1 ? $ob['starter_id'] = $_SESSION['admin_id'] : null;
	$ob['last_poster_id'] == 1 ? $ob['last_poster_id'] = $_SESSION['admin_id'] : null;

	($ob['last_poster_name'] == '' || $ob['last_poster_name'] == null) ? $ob['last_poster_name'] = 'null' : null;

	// Fetch last_post_id
	$res = $fdb->query('SELECT pid FROM '.$fdb->prefix.'posts WHERE topic_id='.$ob['tid'].' ORDER BY pid DESC LIMIT 1') or myerror('Unable to get last_post_id', __FILE__, __LINE__, $fdb->error());
	$ob['last_post_id'] = $fdb->num_rows($res) > 0 ? $fdb->result($res, 0) : null;

	// Dataarray
	$todb = array(
		'id'				=>		$ob['tid'],
		'poster'			=>		$ob['starter_name'],
		'subject'		=>		$ob['title'],
		'posted'			=>		$ob['start_date'],
		'num_views'		=>		$ob['views'],
		'num_replies'	=>		$ob['posts'],
		'last_post'		=>		$ob['last_post'],
		'last_post_id'	=>		$ob['last_post_id'],
		'last_poster'	=>		$ob['last_poster_name'],
		'sticky'			=>		$ob['pinned'],
		'forum_id'		=>		$ob['forum_id'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('tid', 'topics', $last_id);
