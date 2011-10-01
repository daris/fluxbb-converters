<?php

$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts AS p, '.$fdb->prefix.'users AS u WHERE p.post_author=u.user_id AND post_id>'.$start.' ORDER BY post_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result)){

	$last_id = $ob['post_id'];
	echo $ob['post_id'].' ('.htmlspecialchars($ob['user_name']).")<br>\n"; flush();

	// Check id=1 collisions
	$ob['post_author'] == 1 ? $ob['post_author'] = $_SESSION['admin_id'] : null;
	// Guest id=0 -> id=1
	$ob['post_author'] == 0 ? $ob['post_author'] = 1 : null;

	// Dataarray
	$todb = array(
		'id'			=>		$ob['post_id'],
		'poster'		=>		$ob['user_name'],
		'poster_id'	=>		$ob['post_author'],
		'posted'		=>		$ob['post_datestamp'],
		'message'	=>		convert_posts($ob['post_message']),
		'topic_id'	=>		$ob['thread_id'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('post_id', 'posts', $last_id);
