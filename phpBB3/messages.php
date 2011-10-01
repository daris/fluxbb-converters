<?php

// Check if New PMS mod is installed
if ($start == 0 && !$db->table_exists('pms_new_posts'))
	next_step();

$result = $fdb->query('SELECT m.*, u.username FROM '.$fdb->prefix.'privmsgs AS m LEFT JOIN '.$fdb->prefix.'users AS u ON u.user_id=m.author_id WHERE m.msg_id>'.$start.' ORDER BY m.msg_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get message list", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['msg_id'];
	echo $ob['msg_id']."<br>\n"; flush();
	
	$result_msg = $fdb->query('SELECT t.*, u.username FROM '.$fdb->prefix.'privmsgs_to AS t, '.$fdb->prefix.'users AS u WHERE t.user_id=u.user_id AND t.msg_id='.$ob['msg_id'].' LIMIT 1') or myerror("Unable to get message list", __FILE__, __LINE__, $fdb->error());
	$msg = $fdb->fetch_assoc($result_msg);

	// Is topic message?
	if ($ob['root_level'] != 0)
		$topic_id = $ob['root_level'];

	else // else, create topic
	{
		$to_user = intval(str_replace('u_', '', $ob['to_address']));
		$result_username = $fdb->query('SELECT username FROM '.$fdb->prefix.'users WHERE user_id='.$to_user) or myerror('Unable to get username', __FILE__, __LINE__, $fdb->error());
		$to_username = $fdb->result($result_username);

		$todb = array(
			'id'			=> $ob['msg_id'],
			'topic'			=> $ob['message_subject'],
			'starter'		=> $ob['username'],
			'starter_id'	=> $ob['author_id'],
			'to_user'		=> $to_username,
			'to_id'			=> $to_user,
			'replies'		=> 1,
			'last_posted'	=> $ob['message_time'], // temp
		);

		// Save data
		insertdata('pms_new_topics', $todb, __FILE__, __LINE__);
		$topic_id = $db->insert_id();
	}

	// Dataarray
	$todb = array(
		'poster'		=> $ob['username'],
		'poster_id'		=> $ob['author_id'],
		'message'		=> convert_posts($ob['message_text']),
		'posted'		=> $ob['message_time'],
		'post_new'		=> $msg['pm_unread'], // read/unread
		'topic_id'		=> $topic_id,
	);

	// Save data
	insertdata('pms_new_posts', $todb, __FILE__, __LINE__);

}

convredirect('msg_id', 'privmsgs', $last_id);
