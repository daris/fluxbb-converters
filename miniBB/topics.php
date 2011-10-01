<?php
// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'topics WHERE topic_id > '.$start.' ORDER BY topic_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: topics', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['topic_id'];
	echo htmlspecialchars($ob['topic_title']).' ('.$ob['topic_id'].")<br>\n"; flush();

	// Anonymous -> Guest
	$ob['topic_poster_name'] == 'Anonymous' ? $ob['topic_poster_name'] = 'Guest' : null;

	// Solves last-post-problem when there are no answers
	if($ob['topic_last_post_id'] != 0){
		$lastresult = $db->query('SELECT poster,posted FROM '.$db->prefix.'posts WHERE id='.$ob['topic_last_post_id']) or myerror("Unable to get user indo", __FILE__, __LINE__, $db->error());
		$last = $db->fetch_assoc($lastresult);
	}else{
		$last['posted'] = $ob['topic_time'];
		$last['poster'] = $ob['username'];
	}

	// Get topic post count
	$res = $fdb->query('SELECT count(*) AS count FROM '.$fdb->prefix.'posts WHERE topic_id='.$ob['topic_id']) or myerror('Unable to get post-count', __FILE__, __LINE__, $fdb->error());
	list($post_count) = $fdb->fetch_row($res);

	// Dataarray
	$todb = array(
		'id'				=>		$ob['topic_id'],
		'poster'			=>		$ob['topic_poster_name'],
		'subject'		=>		$ob['topic_title'],
		'posted'			=>		strtotime($ob['topic_time']),
		'num_views'		=>		$ob['topic_views'],
		'num_replies'	=>		--$post_count,
		'last_post'		=>		$last['posted'],
		'last_post_id'	=>		$ob['topic_last_post_id'],
		'last_poster'	=>		$last['poster'],
		'sticky'			=>		(int)($ob['topic_status'] == 9),
		'closed'			=>		(int)($ob['topic_status'] == 1),
		'forum_id'		=>		$ob['forum_id'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);

	// Moved topic
	if($ob['topic_status'] == 2)
		$db->query('UPDATE '.$db->prefix.'topics SET moved_to=\''.$ob['topic_moved_id'].'\' WHERE id='.$ob['topic_id']) or myerror("Unable to update modeved-topic", __FILE__, __LINE__, $db->error());
}

// More topics?
convredirect('topic_id', 'topics', $last_id);
