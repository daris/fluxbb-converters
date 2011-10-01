<?php
	$result = $fdb->query('SELECT p.*, u.* FROM '.$fdb->prefix.'posts AS p LEFT JOIN '.$fdb->prefix.'users AS u ON p.poster_id=u.user_id WHERE p.post_id>'.$start.' ORDER BY p.post_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
	$last_id = -1;
	while($ob = $fdb->fetch_assoc($result))
	{
		$last_id = $ob['post_id'];
		echo htmlspecialchars($ob['post_id']).' ('.$ob['username'].")<br>\n"; flush();

		// Check for anonymous poster id problem
		if($ob['poster_id'] == 1)
		{
			$ob['username'] = $ob['post_username'];
			if ($ob['username'] == '')
				$ob['username'] = $lang_common['Guest'];
		}

		// Dataarray
		$todb = array(
			'id'			=>		$ob['post_id'],
			'poster'		=>		$ob['username'],
			'poster_id'	=>		$ob['poster_id'],
			'posted'		=>		$ob['post_time'],
			'poster_ip'	=>		$ob['poster_ip'],
			'message'	=>		convert_posts(html_entity_decode($ob['post_text'])),
			'topic_id'	=>		$ob['topic_id'],
		);

		// Save data
		insertdata('posts', $todb, __FILE__, __LINE__);
	}

	convredirect('post_id', 'posts', $last_id);

?>