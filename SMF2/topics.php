<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'topics WHERE id_topic > '.$start.' ORDER BY id_topic LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['id_topic'];

	// Fetch last post info
	$posts_res = $fdb->query('SELECT subject, poster_time, id_msg, poster_name FROM '.$fdb->prefix.'messages WHERE id_msg IN('.$ob['id_first_msg'].','.$ob['id_last_msg'].')') or myerror('Unable to fetch last topic post info', __FILE__, __LINE__, $fdb->error());
	$ob['first'] = $fdb->fetch_assoc($posts_res);
	$ob['last'] = $fdb->fetch_assoc($posts_res);
	$ob['last'] == null ? $ob['last'] = $ob['first'] : null;

	echo htmlspecialchars($ob['first']['subject']).' ('.$ob['id_topic'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'				=>		$ob['id_topic'],
		'poster'			=>		$ob['first']['poster_name'],
		'subject'		=>		$ob['first']['subject'],
		'posted'			=>		$ob['first']['poster_time'],
		'num_views'		=>		$ob['num_views'],
		'num_replies'	=>		$ob['num_replies'],
		'last_post'		=>		$ob['last']['poster_time'],
		'last_post_id'	=>		$ob['last']['id_msg'],
		'last_poster'	=>		$ob['last']['poster_name'],
		'sticky'			=>		$ob['is_sticky'],
		'closed'			=>		$ob['locked'],
		'forum_id'		=>		$ob['id_board'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('id_topic', 'topics', $last_id);
