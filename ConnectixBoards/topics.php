<?php

// Fetch topic info
$result = $fdb->query('SELECT t.*, u.usr_name FROM '.$fdb->prefix.'topics AS t, '.$fdb->prefix.'users AS u WHERE topic_id > '.$start.' AND t.topic_starter=u.usr_id ORDER BY topic_id LIMIT '.$_SESSION['limit']) or myerror('Connectix Boards: Unable to get table: topics', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['topic_id'];
	echo htmlspecialchars($ob['topic_name']).' ('.$ob['topic_id'].")<br>\n"; flush();

	// Solves last-post-problem when there are no answers
	if ($ob['topic_lastmessage'] != '')
	{
		$lastresult = $fdb->query('SELECT u.usr_name, m.msg_guest, m.msg_timestamp, m.msg_userid FROM '.$fdb->prefix.'messages AS m LEFT JOIN '.$fdb->prefix.'users AS u ON u.usr_id=m.msg_userid WHERE m.msg_id='.$ob['topic_lastmessage']) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
		$last_post = $fdb->fetch_assoc($lastresult);
//			$last['poster'] == '' ? $last['poster'] = $last['guestname'] : null;
		if ($last_post['msg_userid'] == 0)
			$last_post['usr_name'] = ($last_post['msg_guest'] != '') ? $last_post['msg_guest'] : $lang_common['Guest'];
	}

	//Look for first post ID, as CB doesn't have an entry for topics' first post id
	$sql2 = $fdb->query('SELECT msg_id, msg_timestamp FROM '.$fdb->prefix.'messages WHERE msg_topicid='.$ob['topic_id'].' ORDER BY msg_id DESC LIMIT 1') or myerror("Unable to get first post info", __FILE__, __LINE__, $fdb->error());
	$first = $fdb->fetch_assoc($sql2);

	// Check for anonymous poster id problem
	if ($ob['topic_starter'] == 0)
	{
		$firstresult = $fdb->query('SELECT m.msg_userid FROM '.$fdb->prefix.'messages AS m WHERE msg_id='.$first['msg_id']) or myerror("Unable to get user info", __FILE__, __LINE__, $fdb->error());
		list($ob['usr_name']) = $fdb->fetch_row($firstresult);
		if ($ob['usr_name'] == '')
			$ob['usr_name'] = $lang_common['Guest'];
	}

	// Dataarray
	$todb = array(
		'id'			=>	$ob['topic_id'],
		'poster'		=>	$ob['usr_name'],
		'subject'		=>	html_entity_decode(htmlspecialchars_decode($ob['topic_name']), ENT_QUOTES, 'UTF-8'),
		'posted'		=>	$first['msg_timestamp'],
		'first_post_id'	=>	$first['msg_id'],
		'num_views'		=>	$ob['topic_views'],
		'num_replies'	=>	$ob['topic_nbreply'],
		'last_post'		=>	$last_post['msg_timestamp'],
		'last_post_id'	=>	$ob['topic_lastmessage'],
		'last_poster'	=>	$last_post['usr_name'],
		'sticky'		=>	(int)($ob['topic_type'] > 0),
		'closed'		=>	(int)($ob['topic_status'] == 1),
		'forum_id'		=>	$ob['topic_fromtopicgroup'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);

	// Moved topic
	if($ob['topic_status'] == 2)
		$db->query('UPDATE '.$db->prefix.'topics SET moved_to=\''.$ob['topic_displaced'].'\' WHERE id='.$ob['topic_id']) or myerror("Unable to update moved-topic", __FILE__, __LINE__, $db->error());
}

convredirect('topic_id', 'topics', $last_id);
