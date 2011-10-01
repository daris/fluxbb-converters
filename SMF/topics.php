<?php

// Fetch topic info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'topics WHERE ID_TOPIC > '.$start.' ORDER BY ID_TOPIC LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['ID_TOPIC'];

	// Fetch last post info
	$posts_res = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE ID_MSG IN('.$ob['ID_FIRST_MSG'].','.$ob['ID_LAST_MSG'].')') or myerror('Unable to fetch last topic post info', __FILE__, __LINE__, $fdb->error());
	$ob['first'] = $fdb->fetch_assoc($posts_res);
	$ob['last'] = $fdb->fetch_assoc($posts_res);
	$ob['last'] == null ? $ob['last'] = $ob['first'] : null;

	echo htmlspecialchars($ob['first']['subject']).' ('.$ob['ID_TOPIC'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'				=>		$ob['ID_TOPIC'],
		'poster'			=>		$ob['first']['posterName'],
		'subject'		=>		$ob['first']['subject'],
		'posted'			=>		$ob['first']['posterTime'],
		'num_views'		=>		$ob['numViews'],
		'num_replies'	=>		$ob['numReplies'],
		'last_post'		=>		$ob['last']['posterTime'],
		'last_post_id'	=>		$ob['last']['ID_MSG'],
		'last_poster'	=>		$ob['last']['posterName'],
		'sticky'			=>		$ob['isSticky'],
		'closed'			=>		$ob['locked'],
		'forum_id'		=>		$ob['ID_BOARD'],
	);

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('ID_TOPIC', 'topics', $last_id);
