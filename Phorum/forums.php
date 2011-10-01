<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums WHERE forum_id>'.$start.' ORDER BY forum_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['forum_id'];
	echo htmlspecialchars($ob['name']).' ('.$ob['forum_id'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'				=>		$ob['forum_id'],
		'forum_name'	=>		$ob['name'],
		'forum_desc'	=>		$ob['description'],
		'num_topics'	=>		$ob['thread_count'],
		'num_posts'		=>		$ob['message_count'],
		'disp_position'=>		$ob['display_order'],
		'cat_id'			=>		'1',
	);

	// Fetch last message-id
	$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE forum_id='.$ob['forum_id'].' ORDER BY datestamp DESC LIMIT 1') or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
	if($db->num_rows($result) > 0)
	{
		$message = $db->fetch_assoc($result);
		
		$todb['last_poster'] = $message['author'];
		$todb['last_post_id'] = $message['message_id'];
		$todb['last_post'] = $message['datestamp'];
	}

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('forum_id', 'forums', $last_id);
