<?php

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forum WHERE forumid>'.$start.' ORDER BY forumid LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch forum info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['forumid'];
	echo '<br>'.htmlspecialchars($ob['title']).' ('.$ob['forumid'].")\n"; flush();

	// Forum IS v2.0 category, insert it into the database
	if($ob['parentid'] == -1){

		// Dataarray
		$todb = array(
			'id'					=>		$ob['forumid'],
			'cat_name'			=> 	$ob['title'],
			'disp_position'	=> 	$ob['displayorder']
		);

		// Save data
		insertdata('categories', $todb, __FILE__, __LINE__);

	}

	// Its a forum!
	else {
		// Fetch last-post-id
		$res = $fdb->query('SELECT postid FROM '.$fdb->prefix.'post WHERE threadid='.$ob['lastthreadid'].' ORDER BY postid DESC') or myerror('Unable to fetch last-post-info', __FILE__, __LINE__, $fdb->error());
		$ob['lastpostid'] = $fdb->result($res, 0);

		// Settings
		//	--> Parent
		$parentlist = explode(',', $ob['parentlist']);
		$ob['parentid'] = $parentlist[sizeof($parentlist) - 2];
		//	--> Lastpost & Lastpostid
		$ob['lastpost'] == 0 ? $ob['lastpost'] = 'null' : null;
		$ob['lastpostid'] == 0 ? $ob['lastpostid'] = 'null' : null;

		// Dataarray
		$todb = array(
			'id'					=>		$ob['forumid'],
			'forum_name'		=>		$ob['title'],
			'forum_desc'		=>		$ob['description'],
			'num_topics'		=>		$ob['threadcount'],
			'num_posts'			=>		$ob['replycount'],
			'disp_position'	=>		$ob['displayorder'],
			'last_poster'		=>		$ob['lastposter'],
			'last_post_id'		=>		$ob['lastpostid'],
			'last_post'			=>		$ob['lastpost'],
			'cat_id'				=>		$ob['parentid']
		);

		// Save data
		insertdata('forums', $todb, __FILE__, __LINE__);
	}
}

convredirect('forumid', 'forum', $last_id);
