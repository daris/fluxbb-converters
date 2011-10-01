<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'boards') or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());

while($ob = $fdb->fetch_assoc($result)) {

	echo htmlspecialchars($ob['name']).' ('.$ob['id_board'].")<br>\n"; flush();

	// Fetch last post info
	$post_res = $fdb->query('SELECT m.poster_time, u.member_name FROM '.$fdb->prefix.'messages AS m,'.$fdb->prefix.'members AS u WHERE u.id_member=m.id_member AND id_msg='.$ob['id_last_msg']) or myerror('Unable to fetch last post info', __FILE__, __LINE__, $fdb->error());
	if( $fdb->num_rows($post_res) > 0 ) {
		$post_ob = $db->fetch_assoc($post_res);
		$ob['last_post'] = $post_ob['poster_time'];
		$ob['last_poster'] = $post_ob['member_name'];
	}
	else {
		$ob['last_post'] = 'null';
		$ob['last_poster'] = 'null';
	}

	// Dataarray
	$todb = array(
		'id'					=>		$ob['id_board'],
		'forum_name'		=>		$ob['name'],
		'forum_desc'		=>		$ob['description'],
		'num_topics'		=>		$ob['num_topics'],
		'num_posts'			=>		$ob['num_posts'],
		'disp_position'	=>		$ob['board_order'],
		'last_poster'		=>		$ob['last_poster'],
		'last_post'			=>		$ob['last_post'],
		'last_post_id'		=>		$ob['id_last_msg'],
		'cat_id'				=>		$ob['id_cat'],
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);

}
