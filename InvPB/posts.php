<?php

$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts WHERE pid>'.$start.' ORDER BY pid LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['pid'];
	echo $ob['pid'].' ('.htmlspecialchars($ob['author_name']).")<br>\n"; flush();

	// Check id=1 collisions
	$ob['author_id'] == 1 ? $ob['author_id'] = $_SESSION['admin_id'] : null;
	
	// Guest id=0 -> id=1
	$ob['author_id'] == 0 ? $ob['author_id'] = 1 : null;

	// Dataarray
	$todb = array(
		'id'			=>		$ob['pid'],
		'poster'		=>		$ob['author_name'],
		'poster_id'	=>		$ob['author_id'],
		'posted'		=>		$ob['post_date'],
		'poster_ip'	=>		$ob['ip_address'],
		'message'	=>		convert_posts($ob['post']),
		'topic_id'	=>		$ob['topic_id'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('pid', 'posts', $last_id);
