<?php

// Fetch posts info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages AS m, '.$fdb->prefix.'messages_text AS t WHERE t.mesid=m.id AND id>'.$start.' ORDER BY id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['id'];
	echo '<br>'.$ob['id'].' ('.htmlspecialchars($ob['name']).")\n"; flush();

	// Create topic
	if($ob['parent'] == 0)
	{
		// Number of replies
		$rep_result = $fdb->query('SELECT count(*) FROM '.$fdb->prefix.'messages WHERE thread='.$ob['id']) or myerror("Unable to count posts", __FILE__, __LINE__, $fdb->error());
		$num_result = $fdb->fetch_row($rep_result);
		$num_replies = $num_result[0];

		// Get last post information
		$post_result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE thread='.$ob['id'].' ORDER BY id DESC LIMIT 1') or myerror("Unable to fetch last post information", __FILE__, __LINE__, $fdb->error());
		$post = $fdb->fetch_assoc($post_result);

		// Dataarray
		$todb = array(
			'id'				=>		$ob['id'],
			'poster'			=>		$ob['name'],
			'subject'		=>		$ob['subject'],
			'posted'			=>		$ob['time'],
			'forum_id'		=>		$ob['catid'],
			'num_replies'	=>		$num_replies - 1,
			'num_views'		=>		$ob['hits'],
			'closed'			=>		$ob['locked'],
			'sticky'			=>		$ob['hold'],
			'last_post'		=>		$post['time'],
			'last_post_id'	=>		$post['userid'],
			'last_poster'	=>		$post['name'],
		);

		// Save data
		insertdata('topics', $todb, __FILE__, __LINE__);
	}

	// Dataarray
	$todb = array(
		'id'				=>		$ob['id'],
		'poster'			=>		$ob['name'],
		'poster_id'		=>		$ob['userid'],
		'posted'			=>		$ob['time'],
		'poster_ip'		=>		$ob['ip'],
		'message'		=>		$ob['message'],
		'topic_id'		=>		$ob['thread']
	);
	
	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);
}

convredirect('id', 'messages', $last_id);
