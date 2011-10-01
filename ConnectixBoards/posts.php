<?php

$result = $fdb->query('SELECT m.msg_id, m.msg_timestamp, m.msg_userid, m.msg_guest, m.msg_userip, m.msg_topicid, m.msg_message, m.msg_modified, m.msg_modifieduser, u.usr_id, u.usr_name, um.usr_name AS modified_usr_name FROM '.$fdb->prefix.'messages AS m LEFT JOIN '.$fdb->prefix.'users AS u ON m.msg_userid=u.usr_id LEFT JOIN '.$fdb->prefix.'users AS um ON m.msg_modifieduser=um.usr_id WHERE m.msg_id>'.$start.' ORDER BY m.msg_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while ($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['msg_id'];
	echo $ob['msg_id'].' ('.htmlspecialchars($ob['usr_name']).")<br>\n"; flush();

	// Check for anonymous poster id problem
	if ($ob['msg_userid'] == 0)
	{
		$ob['msg_userid'] = 1;
		$ob['usr_name'] = $ob['msg_guest'];
		if ($ob['usr_name'] == '')
			$ob['usr_name'] = $lang_common['Guest'];
	}
	else
		$ob['msg_userid']++; 

	// Dataarray
	$todb = array(
		'id'		=>		$ob['msg_id'],
		'poster'	=>		$ob['usr_name'],
		'poster_id'	=>		$ob['msg_userid'],
		'posted'	=>		$ob['msg_timestamp'],
		'poster_ip'	=>		long2ip($ob['msg_userip']),
		'message'	=>		convert_posts($ob['msg_message']),
		'topic_id'	=>		$ob['msg_topicid'],
		'edited'	=>		($ob['msg_modified'] > 0) ? $ob['msg_modified'] : '',
		'edited_by' =>		isset($ob['modified_usr_name']) ? $ob['modified_usr_name'] : ''
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('msg_id', 'messages', $last_id);
