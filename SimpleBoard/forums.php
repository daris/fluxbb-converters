<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'categories WHERE id>'.$start.' ORDER BY id LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['id'];
	echo '<br>'.htmlspecialchars($ob['name']).' ('.$ob['id'].")\n"; flush();

	// If parent is zero, it's a categorie
	if($ob['parent'] == 0){

		// Dataarray
		$todb = array(
			'id'					=>		$ob['id'],
			'cat_name'			=> 	$ob['name'],
			'disp_position'	=> 	$ob['description']
		);

		// Save data
		insertdata('categories', $todb, __FILE__, __LINE__);

	}

	// Its a forum!
	else{

		// Get last post information
		$post_result = $fdb->query('SELECT * FROM '.$fdb->prefix.'messages WHERE catid='.$ob['id'].' ORDER BY id DESC LIMIT 1') or myerror("Unable to fetch last post information", __FILE__, __LINE__, $fdb->error());
		$post = $fdb->fetch_assoc($post_result);

		// Get topic count
		$topic_result = $fdb->query('SELECT count(*) FROM '.$fdb->prefix.'messages WHERE catid='.$ob['id'].' AND parent=0 ORDER BY id DESC LIMIT 1') or myerror("Unable to fetch last post information", __FILE__, __LINE__, $fdb->error());
		$topics = $fdb->fetch_row($topic_result);
		$num_topics = $topics[0];

		// Get posts count
		$posts_result = $fdb->query('SELECT count(*) FROM '.$fdb->prefix.'messages WHERE catid='.$ob['id'].' ORDER BY id DESC LIMIT 1') or myerror("Unable to fetch last post information", __FILE__, __LINE__, $fdb->error());
		$posts = $fdb->fetch_row($posts_result);
		$num_posts = $posts[0];

		// Dataarray
		$todb = array(
			'id'					=>		$ob['id'],
			'forum_name'		=>		$ob['name'],
			'forum_desc'		=>		$ob['description'],
			'cat_id'				=>		$ob['parent'],
			'disp_position'	=>		$ob['ordering'],

			'num_topics'		=>		$num_topics,
			'num_posts'			=>		$num_posts - $num_topics,

			'last_poster'		=>		$post['name'],
			'last_post_id'		=>		$post['id'],
			'last_post'			=>		$post['time'],
		);

		// Save data
		insertdata('forums', $todb, __FILE__, __LINE__);
	}
}

convredirect('id', 'categories', $last_id);
