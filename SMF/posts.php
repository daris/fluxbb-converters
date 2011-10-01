<?php

$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE ID_MSG>'.$start.' ORDER BY ID_MSG LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['ID_MSG'];
	echo $ob['ID_MSG'].' ('.htmlspecialchars($ob['posterName']).")<br>\n"; flush();

	// Check id=1 collisions
	$ob['ID_MEMBER'] == 1 ? $ob['ID_MEMBER'] = $_SESSION['admin_id'] : null;
	// Guest id=0 -> id=1
	$ob['ID_MEMBER'] == 0 ? $ob['ID_MEMBER'] = 1 : null;

	// Dataarray
	$todb = array(
		'id'			=>		$ob['ID_MSG'],
		'poster'		=>		$ob['posterName'],
		'poster_id'	=>		$ob['ID_MEMBER'],
		'posted'		=>		$ob['posterTime'],
		'poster_ip'	=>		$ob['posterIP'],
		'message'	=>		convert_posts($ob['body']),
		'topic_id'	=>		$ob['ID_TOPIC'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('ID_MSG', 'messages', $last_id);
