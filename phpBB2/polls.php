<?php

// Check if AutoPoll is installed
if ($start == 0 && !$db->table_exists('polls'))
	next_step();

$result = $fdb->query('SELECT vote_id,topic_id,vote_text,vote_start FROM '.$fdb->prefix.$_SESSION['phpnuke'].'vote_desc WHERE vote_id>'.$start.' ORDER BY vote_id LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch poll info', __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['vote_id'];
	echo htmlspecialchars($ob['vote_text']).' ('.$ob['vote_id'].")<br>\n"; flush();

	$answers = null;
	$results = null;
	$vote_results = $fdb->query('SELECT vote_option_text,vote_result FROM '.$fdb->prefix.$_SESSION['phpnuke'].'vote_results WHERE vote_id='.$ob['vote_id'].' ORDER BY vote_option_id') or myerror("Unable to get poll answers.", __FILE__, __LINE__, $fdb->error());
	while(list($vote_option_text, $vote_result) = $fdb->fetch_row($vote_results)){
		$answers[] = htmlspecialchars($vote_option_text, ENT_QUOTES);
		$results[] = $vote_result;
	}

	$voter_ids = null;
	$vote_results = $fdb->query('SELECT vote_user_id FROM '.$fdb->prefix.$_SESSION['phpnuke'].'vote_voters WHERE vote_id='.$ob['vote_id']) or myerror("PhpBB: Unable to get poll voters.", __FILE__, __LINE__, $fdb->error());
	while(list($voters) = $fdb->fetch_row($vote_results)){
		$voter_ids[] = $voters;
	}
	
	$db->query('UPDATE '.$db->prefix.'topics SET question=\''.$db->escape(convert_to_utf8($ob['vote_text'])).'\' WHERE id='.$ob['topic_id']) or myerror("PhpBB: Unable to get poll voters.", __FILE__, __LINE__, $fdb->error());

	// Dataarray
	$todb = array(
		'id'				=>		$ob['vote_id'],
		'pollid'		=>		$ob['topic_id'],
		'ptype'			=>		1,
		'options'		=>		serialize($answers),
		'voters'			=>		serialize($voter_ids),
		'votes'			=>		serialize($results),
		'created'		=>		$ob['vote_start'],
	);

	// Save data
	insertdata('polls', $todb, __FILE__, __LINE__);
}

convredirect('vote_id', $_SESSION['phpnuke'].'vote_desc', $last_id);
