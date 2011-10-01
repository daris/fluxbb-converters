<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums') or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result)) {

	// Category
	if( $ob['forum_cat'] == 0 )
	{
		echo 'Category: '.htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();

		// Dataarray
		$todb = array(
			'id'					=>		$ob['forum_id'],
			'cat_name'			=>		$ob['forum_name'],
			'disp_position'	=>		$ob['forum_order'],
		);
	
		// Save data
		insertdata('categories', $todb, __FILE__, __LINE__);
	}
	
	// Forum
	else
	{
		echo 'Forum: '.htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();
		
		// Fetch forum topic count
		$tresult = $fdb->query('SELECT count(*) as count FROM '.$fdb->prefix.'threads WHERE forum_id='.$ob['forum_id']) or myerror('Unable to fetch topic count', __FILE__, __LINE__, $fdb->error());
		$count = $fdb->fetch_assoc($tresult);
		$topic_count = $count['count'];

		// Fetch forum post count
		$presult = $fdb->query('SELECT count(*) as count FROM '.$fdb->prefix.'posts WHERE forum_id='.$ob['forum_id']) or myerror('Unable to fetch topic count', __FILE__, __LINE__, $fdb->error());
		$count = $fdb->fetch_assoc($presult);
		$post_count = $count['count'];

		// Fetch last post info
		$post_res = $fdb->query('SELECT * FROM '.$fdb->prefix.'posts AS p,'.$fdb->prefix.'users AS u WHERE u.user_id=p.post_author AND forum_id='.$ob['forum_id'].' ORDER BY p.post_datestamp DESC LIMIT 1') or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
		if( $fdb->num_rows($post_res) > 0 ) {
			$post_ob = $db->fetch_assoc($post_res);
			$ob['last_post_id'] = $post_ob['post_id'];
			$ob['last_post'] = $post_ob['post_datestamp'];
			$ob['last_poster'] = $post_ob['user_name'];
		}
		else {
			$ob['last_post_id'] = 'null';
			$ob['last_post'] = 'null';
			$ob['last_poster'] = 'null';
		}

		// Dataarray
		$todb = array(
			'id'					=>		$ob['forum_id'],
			'forum_name'		=>		$ob['forum_name'],
			'forum_desc'		=>		$ob['forum_description'],
			'num_topics'		=>		$topic_count,
			'num_posts'			=>		$post_count,
			'disp_position'	=>		$ob['forum_order'],
			'last_poster'		=>		$ob['last_poster'],
			'last_post'			=>		$ob['last_post'],
			'last_post_id'		=>		$ob['last_post_id'],
			'cat_id'				=>		$ob['forum_cat'],
		);
	
		// Save data
		insertdata('forums', $todb, __FILE__, __LINE__);
	}
}
