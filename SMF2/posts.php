<?php

$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE id_msg>'.$start.' ORDER BY id_msg LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['id_msg'];
	echo $ob['id_msg'].' ('.htmlspecialchars($ob['poster_name']).")<br>\n"; flush();

	// Check id=1 collisions
	$ob['id_member'] == 1 ? $ob['id_member'] = $_SESSION['admin_id'] : null;
	// Guest id=0 -> id=1
	$ob['id_member'] == 0 ? $ob['id_member'] = 1 : null;

	// Dataarray
	$todb = array(
		'id'			=>		$ob['id_msg'],
		'poster'		=>		$ob['poster_name'],
		'poster_id'	=>		$ob['id_member'],
		'posted'		=>		$ob['poster_time'],
		'poster_ip'	=>		$ob['poster_ip'],
		'message'	=>		convert_posts($ob['body']),
		'topic_id'	=>		$ob['id_topic'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('id_msg', 'messages', $last_id);
