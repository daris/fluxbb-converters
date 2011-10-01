<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'boards') or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());

while($ob = $fdb->fetch_assoc($result)) {

	echo htmlspecialchars($ob['name']).' ('.$ob['ID_BOARD'].")<br>\n"; flush();

	// Fetch last post info
	$post_res = $fdb->query('SELECT m.posterTime,u.memberName FROM '.$fdb->prefix.'messages AS m,'.$fdb->prefix.'members AS u WHERE u.ID_MEMBER=m.ID_MEMBER AND ID_MSG='.$ob['ID_LAST_MSG']) or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	if( $fdb->num_rows($post_res) > 0 ) {
		$post_ob = $db->fetch_assoc($post_res);
		$ob['last_post'] = $post_ob['posterTime'];
		$ob['last_poster'] = $post_ob['memberName'];
	}
	else {
		$ob['last_post'] = 'null';
		$ob['last_poster'] = 'null';
	}

	// Dataarray
	$todb = array(
		'id'					=>		$ob['ID_BOARD'],
		'forum_name'		=>		$ob['name'],
		'forum_desc'		=>		$ob['description'],
		'num_topics'		=>		$ob['numTopics'],
		'num_posts'			=>		$ob['numPosts'],
		'disp_position'	=>		$ob['boardOrder'],
		'last_poster'		=>		$ob['last_poster'],
		'last_post'			=>		$ob['last_post'],
		'last_post_id'		=>		$ob['ID_LAST_MSG'],
		'cat_id'				=>		$ob['ID_CAT'],
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);

}
