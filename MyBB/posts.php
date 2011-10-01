<?php
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts WHERE pid>'.$start.' ORDER BY pid LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while ($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['pid'];
	echo $ob['pid'].' ('.htmlspecialchars($ob['username']).")<br>\n"; flush();

	// Check for anonymous poster id problem
/*		if($ob['poster_id'] == -1)
	{
		$ob['poster_id'] = 1;
		$ob['username'] = $ob['post_username'];
		if ($ob['username'] == '')
			$ob['username'] = $lang_common['Guest'];
	}*/

	// Dataarray
	$todb = array(
		'id'			=>		$ob['pid'],
		'poster'		=>		$ob['username'],
		'poster_id'	=>		++$ob['uid'],
		'posted'		=>		$ob['dateline'],
		'poster_ip'	=>		$ob['ipaddress'],
		'message'	=>		convert_posts($ob['message']),
		'topic_id'	=>		$ob['tid'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('pid', 'posts', $last_id);
