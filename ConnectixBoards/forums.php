<?php

// Fetch forum info
$cat_result = $db->query('SELECT c.id FROM '.$db->prefix.'categories AS c ORDER BY c.id') or myerror('FluxBB: Unable to get table: categories', __FILE__, __LINE__, $db->error());
$categories = array();
while($cur_cat = $db->fetch_assoc($cat_result))
	$categories[] = $cur_cat['id'];

if (!$db->field_exists('forums', 'parent_forum_id'))
	$db->add_field('forums', 'parent_forum_id', 'INT', true, 0);

// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'topicgroups WHERE tg_id>'.$start.' ORDER BY tg_id LIMIT '.$_SESSION['limit']) or myerror('Connectix Boards: Unable to get table: forums', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['tg_id'];
	echo htmlspecialchars($ob['tg_name']).' ('.$ob['tg_id'].")<br>\n"; flush();

	if ($ob['tg_lasttopic'] > 0)
	{
		$lastpostres = $fdb->query('SELECT t.topic_lastmessage, m.msg_id, m.msg_timestamp, m.msg_guest, m.msg_userid, u.usr_id, u.usr_name FROM '.$fdb->prefix.'topics AS t LEFT JOIN '.$fdb->prefix.'messages AS m ON t.topic_lastmessage=m.msg_id LEFT JOIN '.$fdb->prefix.'users AS u ON m.msg_userid=u.usr_id WHERE t.topic_id='.$ob['tg_lasttopic'].' ') or myerror("Unable to fetch forum last post infos for forum conversion.", __FILE__,__LINE__, $fdb->error());
		$last_post = $fdb->fetch_assoc($lastpostres);

		if ($last_post['msg_userid'] == 0)
			$last_post['usr_name'] = $last_post['msg_guest'];
		if ($last_post['usr_name'] == '')
			$last_post['usr_name'] = $lang_common['Guest'];

		// Change last_post = 0 to null to prevent the time-bug.
		if (!isset($last_post['msg_timestamp']) || $last_post['msg_timestamp'] == 0)
			$last_post['msg_timestamp'] = 'null';
		if ($ob['tg_lasttopic'] == 0)
			$ob['tg_lasttopic'] = 'null';

		// Unset variables
		if (!isset($last_post['usr_name']))
			$last_post['usr_name'] = 'null';
	}

	// Dataarray
	$todb = array(
		'id'			=>		$ob['tg_id'],
		'forum_name'	=>		html_entity_decode(htmlspecialchars_decode($ob['tg_name']), ENT_QUOTES, 'UTF-8'),
		'forum_desc'	=>		$ob['tg_comment'],
		'num_topics'	=>		$ob['tg_nbtopics'],
		'num_posts'		=>		$ob['tg_nbmess'],
		'disp_position'	=>		$ob['tg_order'],
		'last_post_id'	=>		isset($last_post['topic_lastmessage']) ? $last_post['topic_lastmessage'] : 0,
		'last_poster'	=>		isset($last_post['usr_name']) ? $last_post['usr_name'] : '',
		'last_post'		=>		isset($last_post['msg_timestamp']) ? $last_post['msg_timestamp'] : 0,
		'cat_id'		=>		$ob['tg_fromforum'],
		'parent_forum_id'=>		$ob['tg_fromtopicgroup'],
		'redirect_url'	=>		$ob['tg_link'],
	);

	// Save data
	insertdata('forums', $todb, __FILE__, __LINE__);
}

convredirect('forum_id', 'forums', $last_id);
