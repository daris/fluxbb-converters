<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'threads WHERE tid > '.$start.' ORDER BY tid LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: topics', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['tid'];
	echo htmlspecialchars($ob['subject']).' ('.$ob['tid'].")<br>\n"; flush();

	$sql = 'SELECT pid FROM '.$fdb->prefix.'posts WHERE tid='.$ob['tid'].' ORDER BY pid LIMIT 1';
	$lastresult = $fdb->query($sql) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
	
	$last = $fdb->result($lastresult);
	

	// Check for anonymous poster id problem
/*		if ($ob['topic_poster'] == -1)
	{
		$firstresult = $fdb->query('SELECT p.post_username FROM '.$fdb->prefix.'posts AS p WHERE post_id='.$ob['topic_first_post_id']) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
		list($ob['username']) = $fdb->fetch_row($firstresult);
		if ($ob['username'] == '')
			$ob['username'] = $lang_common['Guest'];
	}
*/
	// Dataarray
	$todb = array(
		'id'				=>		$ob['tid'],
		'poster'			=>		$ob['username'],
		'subject'		=>		$ob['subject'],
		'posted'			=>		$ob['dateline'],
		'num_views'		=>		$ob['views'],
		'num_replies'	=>		$ob['replies'],
		'first_post_id'	=> 		$ob['firstpost'],
		'last_post'		=>		$ob['lastpost'],
		'last_post_id'	=>		$last['pid'],
		'last_poster'	=>		$ob['lastposter'],
		'sticky'			=>		$ob['sticky'],
		'closed'			=>		$ob['closed'],
		'forum_id'		=>		$ob['fid'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);

	// Moved topic
/*	if($ob['topic_status'] == 2)
		$db->query('UPDATE '.$db->prefix.'topics SET moved_to=\''.$ob['topic_moved_id'].'\' WHERE id='.$ob['tid']) or myerror("Unable to update modeved-topic", __FILE__, __LINE__, $db->error());
*/	}

convredirect('tid', 'threads', $last_id);
