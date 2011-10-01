<?php

	// Fetch forum info
	$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums WHERE forum_id>'.$start.' ORDER BY forum_id LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
	$last_id = -1;
	while($ob = $fdb->fetch_assoc($result))
	{
		$last_id = $ob['forum_id'];
		echo htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();

		$ob['forum_last_post_id'] == 0 ? $ob['forum_last_post_id'] = 'null' : null;
		$ob['forum_last_poster_id'] == 0 ? $ob['forum_last_poster_id'] = 'null' : null;
		$ob['forum_last_post_time'] == 0 ? $ob['forum_last_post_time'] = 'null' : null;
	
		if ($ob['forum_last_poster_id'] == 1 && $ob['forum_last_poster_name'] == '')
			$ob['forum_last_poster_name'] = $lang_common['Guest'];

		// Unset variables
		if(!isset($userinfo['username']))
			$userinfo['username'] = 'null';

		// It's category
		if ($ob['parent_id'] == 0)
		{
			// Dataarray
			$todb = array(
				'id'				=>		$ob['forum_id'],
				'cat_name'		=>		$ob['forum_name'],
				'disp_position'=>		$ob['left_id'],
			);

			// Save data
			insertdata('categories', $todb, __FILE__, __LINE__);
		}
		else
		{
			// If category does not exist
			$catres = $db->query('SELECT 1 FROM '.$db->prefix.'categories AS c WHERE id='.$ob['parent_id']) or myerror("Unable to fetch user info for forum conversion.", __FILE__, __LINE__, $fdb->error());
		//	$catinfo = $fdb->fetch_assoc($catres);
			if (!$db->num_rows($catres))
			{
				$newcatres = $db->query('SELECT c.id FROM '.$db->prefix.'categories AS c ORDER BY c.id ASC LIMIT 1') or myerror("Unable to fetch user info for forum conversion.", __FILE__, __LINE__, $fdb->error());
				$newcatinfo = $db->fetch_assoc($newcatres);
				
				// Set forum for the first category :)
				$ob['parent_id'] = $newcatinfo['id'];
			}

		
			// Dataarray
			$todb = array(
				'id'				=>		$ob['forum_id'],
				'forum_name'	=>		$ob['forum_name'],
				'forum_desc'	=>		$ob['forum_desc'],
				'num_topics'	=>		$ob['forum_topics'],
				'num_posts'		=>		$ob['forum_posts'],
				'disp_position'=>		$ob['left_id'],
				'last_poster'	=>		$ob['forum_last_poster_name'],
				'last_post_id'	=>		$ob['forum_last_post_id'],
				'last_post'		=>		$ob['forum_last_post_time'],
				'cat_id'			=>		$ob['parent_id'],
			);

			// Save data
			insertdata('forums', $todb, __FILE__, __LINE__);
		}
	}
//exit;
	convredirect('forum_id', 'forums', $last_id);

?>