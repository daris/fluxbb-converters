<?php

// Check if New PMS mod is installed
if ($start == 0)
{
	if (!$db->table_exists('pms_new_posts'))
		next_step();
}

$result = $fdb->query('SELECT m.mp_id, m.mp_subj, m.mp_content, m.mp_read, m.mp_to, m.mp_from, m.mp_timestamp, u.usr_name, ur.usr_name AS receiver FROM '.$fdb->prefix.'mp AS m LEFT JOIN '.$fdb->prefix.'users AS u ON u.usr_id=m.mp_from LEFT JOIN '.$fdb->prefix.'users AS ur ON ur.usr_id=m.mp_to WHERE m.mp_id>'.$start.' ORDER BY m.mp_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get message list", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while ($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['mp_id'];
	echo htmlspecialchars($ob['usr_name']).' ('.$ob['mp_id'].")<br>\n"; flush();
	++$ob['mp_from'];
	$ob['mp_subj'] = convert_posts($ob['mp_subj']);

	// Topic already exist?
	$result_tid = $db->query('SELECT t.id FROM '.$db->prefix.'pms_new_topics AS t WHERE t.topic=\''.$db->escape(str_replace('Re : ', '', $ob['mp_subj'])).'\' ORDER BY t.id LIMIT 1') or myerror("Unable to get topic id", __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result_tid))
	{
		$topic_id = $db->result($result_tid);
		
		// Check if there are more same messages, if yes, it is a message to all users
		$result_all = $fdb->query('SELECT 1 FROM '.$fdb->prefix.'mp AS m WHERE m.mp_id<>'.$ob['mp_id'].' AND m.mp_content=\''.$fdb->escape($ob['mp_content']).'\' LIMIT 1') or myerror("Unable to check message", __FILE__, __LINE__, $fdb->error());
		if ($fdb->num_rows($result_all))
		{
			$db->query('UPDATE '.$db->prefix.'pms_new_topics SET to_user=\'all\', to_id=1 WHERE id='.$topic_id) or myerror('Unable to update topics', __FILE__, __LINE__, $db->error());
			continue;
		}
	}

	else // topic does not exist, create it
	{
		$todb = array(
			'topic'			=> $ob['mp_subj'],
			'starter'		=> $ob['usr_name'],
			'starter_id'	=> $ob['mp_from'],
			'to_user'		=> $ob['receiver'],
			'to_id'			=> ++$ob['mp_to'],
			'replies'		=> 1,
			'last_posted'	=> $ob['mp_timestamp'], // temp
		);

		// Save data
		insertdata('pms_new_topics', $todb, __FILE__, __LINE__);
		$topic_id = $db->insert_id();
	}

	// Dataarray
	$todb = array(
		'poster'		=> $ob['usr_name'],
		'poster_id'		=> $ob['mp_from'],
		'message'		=> convert_posts($ob['mp_content']),
		'posted'		=> $ob['mp_timestamp'],
		'post_new'		=> $ob['mp_read'], // read/unread
		'topic_id'		=> $topic_id,
	);

	// Save data
	insertdata('pms_new_posts', $todb, __FILE__, __LINE__);
}

convredirect('mp_id', 'mp', $last_id);
