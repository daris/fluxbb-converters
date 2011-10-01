<?php

// Fetch posts info
$result = $fdb->query('SELECT post_id, poster_name, post_time, poster_id, poster_ip, topic_id, post_text FROM '.$fdb->prefix.'posts WHERE post_id>'.$start.' ORDER BY post_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['post_id'];
	echo htmlspecialchars($ob['post_id']).' ('.$ob['poster_name'].")<br>\n"; flush();
	
	// Change guest and admin user id
	$ob['poster_id'] == 1 ? $ob['poster_id'] = $_SESSION['admin_id'] : null;
	if($ob['poster_id'] == 0){
		$ob['poster_id'] = 1;
	}

	// Dataarray
	$todb = array(
		'id'			=>		$ob['post_id'],
		'poster'		=>		$ob['poster_name'],
		'poster_id'	=>		$ob['poster_id'],
		'posted'		=>		strtotime($ob['post_time']),
		'poster_ip'	=>		$ob['poster_ip'],
		'message'	=>		convert_posts($ob['post_text']),
		'topic_id'	=>		$ob['topic_id'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('post_id', 'posts', $last_id);
