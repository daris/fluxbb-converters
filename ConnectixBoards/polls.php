<?php

// Check if AutoPoll is installed
if ($start == 0 && !$db->table_exists('polls'))
	next_step();

//Look for poll infos
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'polls WHERE poll_id>'.$start.' ORDER BY poll_id LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch poll info', __FILE__, __LINE__, $fdb->error());

$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['poll_id'];
	echo htmlspecialchars($ob['poll_question']).' ('.$ob['poll_id'].")<br>\n"; flush();

	$answers = null;

	//Look for poll answers
	$vote_results = $fdb->query('SELECT poss_name FROM '.$fdb->prefix.'pollpossibilities WHERE poss_pollid='.$ob['poll_id'].' ORDER BY poss_id') or myerror("Unable to get poll answers.", __FILE__, __LINE__, $fdb->error());

	while (list($poss_name) = $fdb->fetch_row($vote_results))
	{
		$answers[] = convert_to_utf8($poss_name);
	}

	//Look for the topic associated to the poll
	$topic_result = $fdb->query('SELECT topic_id FROM '.$fdb->prefix.'topics WHERE topic_poll='.$ob['poll_id']) or myerror('Unable to get poll topic', __FILE__, __LINE__, $fdb->error());

	list($topic_id) = $fdb->fetch_row($topic_result);
	$db->query('UPDATE '.$db->prefix.'topics SET question=\''.$db->escape(convert_to_utf8($ob['poll_question'])).'\' WHERE id='.$topic_id) or myerror("Connectix Boards: Unable to set poll question.", __FILE__, __LINE__, $fdb->error());

	//Look for the poll start time
	$time_result = $fdb->query('SELECT msg_timestamp FROM '.$fdb->prefix.'messages WHERE msg_topicid='.$topic_id.' ORDER BY msg_id LIMIT 0,1') or myerror('Unable to get topic start time', __FILE__, __LINE__, $fdb->error());
	list($poll_start) = $fdb->fetch_row($time_result);

	// Dataarray
	$todb = array(
		'id'		=>	$ob['poll_id'],
		'pollid'	=>	$topic_id,
		'ptype'		=>	1,
		'options'	=>	serialize($answers),
		'created'	=>	$poll_start,
	);

	// Save data
	insertdata('polls', $todb, __FILE__, __LINE__);
}

convredirect('poll_id', 'polls', $last_id);
