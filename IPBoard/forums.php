<?php

// Fetch forum info
$result = $fdb->query('SELECT f.* FROM '.$fdb->prefix.'forums AS f WHERE f.id>'.$start.' ORDER BY f.id LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['id'];
	echo htmlspecialchars($ob['name']).' ('.$ob['id'].")<br>\n"; flush();

	// Check id=1 collisions
	$ob['last_poster_id'] == 1 ? $ob['last_poster_id'] = $_SESSION['admin_id'] : null;

	// Forum IS v2.0 category, insert it into the database
	if($_SESSION['ver'] == "20" && $ob['parent_id'] == -1){

		// Dataarray
		$todb = array(
			'id'					=>		$ob['id'],
			'cat_name'			=>		$ob['name'],
			'disp_position'	=>		$ob['description'],
		);

		// Save data
		insertdata('categories', $todb, __FILE__, __LINE__);
	}

	// Its a forum!
	else
	{
		// Change info when differences between 1.3 and 2.0
		if($_SESSION['ver'] == "13"){
			$ob['parent_id'] = $ob['category'];
		}

		// Fetch last_post_id
		$res = $fdb->query('SELECT p.pid FROM '.$fdb->prefix.'posts AS p, '.$fdb->prefix.'topics AS t WHERE p.topic_id=t.tid AND t.forum_id='.$ob['id'].' ORDER BY p.pid DESC LIMIT 1') or error('Unable to fetch last_post_id', __FILE__, __LINE__, $fdb->error());
		$ob['last_post_id'] = $fdb->num_rows($res) != 0 ? $fdb->result($res, 0) : null;

		// Change last_post time = 0 to null to prevent the time-bug.
		$ob['last_post'] == 0 ? $ob['last_post'] = 'null' : null;

		// Dataarray
		$todb = array(
			'id'					=>		$ob['id'],
			'forum_name'		=>		$ob['name'],
			'forum_desc'		=>		$ob['description'],
			'num_topics'		=>		$ob['topics'],
			'num_posts'			=>		$ob['posts'],
			'disp_position'	=>		$ob['position'],
			'last_poster'		=>		$ob['last_poster_name'],
			'last_post_id'		=>		$ob['last_post_id'],
			'last_post'			=>		$ob['last_post'],
			'cat_id'				=>		$ob['parent_id'],
		);
	
		// Save data
		insertdata('forums', $todb, __FILE__, __LINE__);
	}
}

convredirect('id', 'forums', $last_id);
