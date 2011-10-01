<?php
// Fetch forum info
$cat_result = $db->query('SELECT c.id FROM '.$db->prefix.'categories AS c ORDER BY c.id') or myerror('FluxBB: Unable to get table: categories', __FILE__, __LINE__, $db->error());
$categories = array();
while($cur_cat = $db->fetch_assoc($cat_result))
	$categories[] = $cur_cat['id'];

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums WHERE fid>'.$start.' AND type=\'f\' ORDER BY fid LIMIT '.$_SESSION['limit']) or myerror('Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['fid'];
	echo htmlspecialchars($ob['name']).' ('.$ob['fid'].")<br>\n"; flush();

	// Check for anonymous poster id problem
//		$ob['forum_last_post_id'] == -1 ? $ob['forum_last_post_id'] = 1 : null;
	$last_result = $fdb->query('SELECT pid FROM '.$fdb->prefix.'posts WHERE tid='.$ob['lastposttid'].' ORDER BY dateline DESC LIMIT 1') or myerror("Unable to fetch user info for forum conversion.", __FILE__, __LINE__, $fdb->error());
	if ($fdb->num_rows($last_result))
		$last_post_id = $fdb->result($last_result);
	else
		$last_post_id = 0;

	$parentlist = explode(',', $ob['parentlist']);
	$cat_id = trim($parentlist[0]);
	
	// Category does not exist?
	if (!in_array($cat_id, $categories))
		$cat_id = $categories[0];

	// Dataarray
	$todb = array(
		'id'			=>		$ob['fid'],
		'forum_name'	=>		$ob['name'],
		'forum_desc'	=>		$ob['description'],
		'num_topics'	=>		$ob['threads'],
		'num_posts'		=>		$ob['posts'],
		'disp_position'	=>		$ob['disporder'],
		'last_poster'	=>		$ob['lastposter'],
		'last_post_id'	=>		$last_post_id,
		'last_post'		=>		$ob['lastpost'],
		'cat_id'		=>		$cat_id,
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('fid', 'forums', $last_id);
