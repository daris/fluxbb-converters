<?php

// Fetch topic info
$result = $fdb->query('SELECT t.*, u.'.($_SESSION['phpnuke'] == 'bb_' ? 'uname as username' : 'username').' FROM '.$fdb->prefix.$_SESSION['phpnuke'].'topics AS t, '.$fdb->prefix.'users AS u WHERE t.topic_id > '.$start.' AND t.topic_poster=u.'.($_SESSION['phpnuke'] == 'bb_' ? 'uid' : 'user_id').' ORDER BY t.topic_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: topics', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['topic_id'];
	echo htmlspecialchars($ob['topic_title']).' ('.$ob['topic_id'].")<br>\n"; flush();

	// Solves last-post-problem when there are no answers
	if( $ob['topic_last_post_id'] != '' )
	{
		// Xoops with newbb module
		if ($_SESSION['phpnuke'] == 'bb_')
			$sql = 'SELECT u.uname as username, p.poster_name as post_username, p.post_time, p.uid as poster_id FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p LEFT JOIN '.$fdb->prefix.'users AS u ON p.uid=u.uid WHERE post_id='.$ob['topic_last_post_id'];
		else
			$sql = 'SELECT u.username, p.post_username, p.post_time, p.poster_id FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p LEFT JOIN '.$fdb->prefix.'users AS u ON p.poster_id=u.user_id WHERE post_id='.$ob['topic_last_post_id'];
			
		$lastresult = $fdb->query($sql) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
		
		list($last['poster'], $last['guestname'], $last['posted'], $last['poster_id']) = $fdb->fetch_row($lastresult);
		
		if ($last['poster_id'] == -1)
			$last['poster'] = $last['guestname'];
		if ($last['poster'] == '')
			$last['poster'] = $lang_common['Guest'];
	}

	// Check for anonymous poster id problem
	if ($ob['topic_poster'] == -1)
	{
		$firstresult = $fdb->query('SELECT p.'.($_SESSION['phpnuke'] == 'bb_' ? 'poster_name' : 'post_username').' FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p WHERE post_id='.$ob['topic_first_post_id']) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
		list($ob['username']) = $fdb->fetch_row($firstresult);
		if ($ob['username'] == '')
			$ob['username'] = $lang_common['Guest'];
	}

	// Dataarray
	$todb = array(
		'id'				=>		$ob['topic_id'],
		'poster'			=>		$ob['username'],
		'subject'		=>		$ob['topic_title'],
		'posted'			=>		$ob['topic_time'],
		'num_views'		=>		$ob['topic_views'],
		'num_replies'	=>		$ob['topic_replies'],
		'last_post'		=>		$last['posted'],
		'last_post_id'	=>		$ob['topic_last_post_id'],
		'last_poster'	=>		$last['poster'],
		'sticky'			=>		isset($ob['topic_sticky']) ? $ob['topic_sticky'] : (int)($ob['topic_type'] > 0),
		'closed'			=>		(int)($ob['topic_status'] == 1),
		'forum_id'		=>		$ob['forum_id'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);

	// Moved topic
	if($ob['topic_status'] == 2)
		$db->query('UPDATE '.$db->prefix.'topics SET moved_to=\''.$ob['topic_moved_id'].'\' WHERE id='.$ob['topic_id']) or myerror("Unable to update modeved-topic", __FILE__, __LINE__, $db->error());
}

convredirect('topic_id', $_SESSION['phpnuke'].'topics', $last_id);
