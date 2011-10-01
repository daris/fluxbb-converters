<?php

// Xoops with newbb module
if ($_SESSION['phpnuke'] == 'bb_')
	$sql = 'SELECT p.post_id, p.post_time, p.poster_name as post_username, p.uid as poster_id, p.poster_ip, p.topic_id, p.subject as post_subject, t.post_text, u.uname as username FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p LEFT JOIN '.$fdb->prefix.$_SESSION['phpnuke'].'posts_text AS t ON (p.post_id=t.post_id) LEFT JOIN '.$fdb->prefix.'users AS u ON (u.uid=p.uid) WHERE p.post_id>'.$start.' ORDER BY p.post_id LIMIT '.$_SESSION['limit'];
else
	$sql = 'SELECT p.post_id, p.post_time, p.post_username, p.poster_id, p.poster_ip, p.topic_id, t.post_subject, t.post_text, u.username FROM '.$fdb->prefix.$_SESSION['phpnuke'].'posts AS p, '.$fdb->prefix.$_SESSION['phpnuke'].'posts_text AS t, '.$fdb->prefix.'users AS u WHERE p.post_id>'.$start.' AND p.post_id=t.post_id AND u.user_id=p.poster_id ORDER BY p.post_id LIMIT '.$_SESSION['limit'];

$result = $fdb->query($sql) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['post_id'];
	echo $ob['post_id'].' ('.htmlspecialchars($ob['username']).")<br>\n"; flush();

	// Check for anonymous poster id problem
	if($ob['poster_id'] == -1)
	{
		$ob['poster_id'] = 1;
		$ob['username'] = $ob['post_username'];
		if ($ob['username'] == '')
			$ob['username'] = $lang_common['Guest'];
	}

	// Dataarray
	$todb = array(
		'id'			=>		$ob['post_id'],
		'poster'		=>		$ob['username'],
		'poster_id'	=>		++$ob['poster_id'],
		'posted'		=>		$ob['post_time'],
		'poster_ip'	=>		decode_ip($ob['poster_ip']),
		'message'	=>		convert_posts($ob['post_text']),
		'topic_id'	=>		$ob['topic_id'],
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('post_id', $_SESSION['phpnuke'].'posts', $last_id);
