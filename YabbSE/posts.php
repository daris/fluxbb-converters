<?php

// Fetch posts info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE ID_MSG>'.$start.' ORDER BY ID_MSG LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['ID_MSG'];
	echo '<br>'.$ob['ID_MSG'].' ('.htmlspecialchars($ob['posterName']).")\n"; flush();

	// Settings
	$ob['ID_MEMBER'] == -1 ? $ob['ID_MEMBER'] = 1 : null;
	$ob['ID_MEMBER'] == 1 ? $ob['ID_MEMBER'] = $_SESSION['admin_id'] : null;

	// Dataarray
	$todb = array(
		'id'				=>		$ob['ID_MSG'],
		'poster'			=>		$ob['posterName'],
		'poster_id'		=>		$ob['ID_MEMBER'],
		'posted'			=>		$ob['posterTime'],
		'poster_ip'		=>		$ob['posterIP'],
		'message'		=>		convert_posts($ob['body']),
		'topic_id'		=>		$ob['ID_TOPIC']
	);

	if($_SESSION['pun_version'] == '1.1')
		$todb['smilies'] = $ob['smiliesEnabled'];
	else
		$todb['hide_smilies'] = !$ob['smiliesEnabled'];

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('ID_MSG', 'messages	', $last_id);
