<?php

// Fetch topic info
$result = $fdb->query('SELECT t.*,m.subject,m.posterTime,m.posterName FROM '.$fdb->prefix.'topics AS t,'.$fdb->prefix.'messages AS m WHERE t.ID_FIRST_MSG=m.ID_MSG AND t.ID_TOPIC > '.$start.' ORDER BY t.ID_TOPIC LIMIT '.$_SESSION['limit']) or myerror('Unable to get topic info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['ID_TOPIC'];
	echo '<br>'.htmlspecialchars($ob['subject']).' ('.$ob['ID_TOPIC'].")\n"; flush();

	// Fetch last post info
	$lastres = $fdb->query('SELECT posterName,posterTime FROM '.$fdb->prefix.'messages WHERE ID_MSG='.$ob['ID_LAST_MSG'].' LIMIT 1') or myerror('Unable to get last-post-id', __FILE__, __LINE__, $fdb->error());
	list($ob['post_posterName'], $ob['post_posterTime']) = $fdb->fetch_row($lastres);

	// Settings
	$ob['ID_MEMBER_STARTED'] == -1 ? $ob['ID_MEMBER_STARTED'] = 1 : null;
	$ob['ID_MEMBER_STARTED'] == 1 ? $ob['ID_MEMBER_STARTED'] = $_SESSION['admin_id'] : null;
	$ob['ID_POLL'] == -1 ? $ob['ID_POLL'] = 0 : null;

	// Dataarray
	$todb = array(
		'id'				=>		$ob['ID_TOPIC'],
		'poster'			=>		$ob['posterName'],
		'subject'		=>		$ob['subject'],
		'posted'			=>		$ob['posterTime'],
		'num_views'		=>		$ob['numViews'],
		'num_replies'	=>		$ob['numReplies'],
		'last_post'		=>		$ob['post_posterTime'],
		'last_post_id'	=>		$ob['ID_LAST_MSG'],
		'last_poster'	=>		$ob['post_posterName'],
		'sticky'			=>		$ob['isSticky'],
//			'moved_to'		=>		$ob['moved_to'],
		'closed'			=>		$ob['locked'],
		'forum_id'		=>		$ob['ID_BOARD'],
	);

	// Poll
	if( ($db->query('SELECT count(*) FROM '.$db->prefix.'polls')) )
		$todb['poll'] = $ob['ID_POLL'];

	// Save data
	insertdata('topics', $todb, __FILE__, __LINE__);
}

convredirect('ID_TOPIC', 'topics', $last_id);
