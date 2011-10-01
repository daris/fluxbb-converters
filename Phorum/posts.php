<?php

// Fetch posts info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE message_id>'.$start.' ORDER BY message_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['message_id'];
	echo '<br>'.htmlspecialchars($ob['message_id']).' ('.$ob['author'].")\n"; flush();

	// Settings
	$ob['user_id'] < 1 ? $ob['user_id'] = 1 : null;
	$ob['user_id'] == 1 ? $ob['user_id'] = $_SESSION['admin_id'] : null;

	// Create topic
	if($ob['message_id'] == $ob['thread'])
	{
		// Unserialize meta data
		$meta = unserialize($ob['meta']);

		// Dataarray
		$todb = array(
			'id'				=>		$ob['message_id'],
			'poster'			=>		$ob['author'],
			'subject'		=>		$ob['subject'],
			'posted'			=>		$ob['datestamp'],
			'forum_id'		=>		$ob['forum_id'],
			'num_replies'	=>		$ob['thread_count'] - 1,
			'closed'			=>		$ob['closed'],
			'last_post'		=>		$ob['datestamp'],
		);
		
		// Recent post
		if(isset($meta['recent_post']))
		{
			$todb['last_post_id'] = $meta['recent_post']['message_id'];
			$todb['last_poster'] = $meta['recent_post']['author'];
		}
		
		// Number of views
		if(isset($meta['mod_viewcount']))
			$todb['num_views'] = $meta['mod_viewcount'][$ob['message_id']];

		// Save data
		insertdata('topics', $todb, __FILE__, __LINE__);
	}

	// Dataarray
	$todb = array(
		'id'				=>		$ob['message_id'],
		'poster'			=>		$ob['author'],
		'poster_id'		=>		$ob['user_id'],
		'posted'			=>		$ob['datestamp'],
		'poster_ip'		=>		$ob['ip'],
		'message'		=>		convert_posts($ob['body']),
		'topic_id'		=>		$ob['thread']
	);

	// Save data
	insertdata('posts', $todb, __FILE__, __LINE__);

}

convredirect('message_id', 'messages', $last_id);
