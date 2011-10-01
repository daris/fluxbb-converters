<?php

// Fetch forum info
$result = $fdb->query('SELECT b.*,m.posterName,m.ID_MSG,m.posterTime FROM '.$fdb->prefix.'boards AS b LEFT JOIN '.$fdb->prefix.'messages AS m ON b.ID_LAST_TOPIC=ID_MSG WHERE ID_BOARD>'.$start.' ORDER BY ID_BOARD LIMIT '.$_SESSION['limit']) or myerror('phpBB: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['ID_BOARD'];
	echo htmlspecialchars($ob['name']).' ('.$ob['ID_BOARD'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'				=>		$ob['ID_BOARD'],
		'forum_name'	=>		$ob['name'],
		'forum_desc'	=>		$ob['description'],
		'num_topics'	=>		$ob['numTopics'],
		'num_posts'		=>		$ob['numPosts'],
		'disp_position'=>		$ob['boardOrder'],
		'last_poster'	=>		$ob['posterName'],
		'last_post_id'	=>		$ob['ID_MSG'],
		'last_post'		=>		$ob['posterTime'],
		'cat_id'			=>		$ob['ID_CAT'],
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('ID_BOARD', 'boards', $last_id);
