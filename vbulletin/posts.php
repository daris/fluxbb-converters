<?php

// Fetch posts info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'post WHERE postid>'.$start.' ORDER BY postid LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['postid'];
	echo '<br>'.$ob['postid'].' ('.htmlspecialchars($ob['username']).")\n"; flush();

	// Settings
	// --> Check id=1 collisions
	$ob['userid'] == 1 ? $ob['userid'] = $_SESSION['admin_id'] : null;

	// Dataarray
	$todb = array(
		'id'				=>		$ob['postid'],
		'poster'			=>		$ob['username'],
		'poster_id'		=>		$ob['userid'],
		'posted'			=>		$ob['dateline'],
		'poster_ip'		=>		$ob['ipaddress'],
		'message'		=>		convert_posts($ob['pagetext']),
		'topic_id'		=>		$ob['threadid']
	);
	
	if($_SESSION['pun_version'] == '1.1')
		$todb['smilies'] = $ob['allowsmilie'];
	else
		$todb['hide_smilies'] = !$ob['allowsmilie'];

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('postid', 'post', $last_id);
