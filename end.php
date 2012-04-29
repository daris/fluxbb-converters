<?php

	if ($db->table_exists('pms_new_topics'))
	{
		echo "\n<br>Synchronizing pms_new_topics table<br/>"; flush();
		$result = $db->query('SELECT id FROM '.$db->prefix.'pms_new_topics ORDER BY id') or myerror("Unable to get topic list", __FILE__, __LINE__, $db->error());
		while ($cur_topic = $fdb->fetch_assoc($result))
		{
			// Fetch last post
			$result_last = $db->query('SELECT posted, poster FROM '.$db->prefix.'pms_new_posts WHERE topic_id='.$cur_topic['id'].' ORDER BY id DESC LIMIT 1') or myerror("Unable to get last post", __FILE__, __LINE__, $db->error());
			$last_post = $db->fetch_assoc($result_last);
			
			// Fetch num replies
			$result_replies = $db->query('SELECT COUNT(id)-1 FROM '.$db->prefix.'pms_new_posts WHERE topic_id='.$cur_topic['id']) or myerror("Unable to get num replies", __FILE__, __LINE__, $db->error());
			$num_replies = $db->result($result_replies);
			
			$db->query('UPDATE '.$db->prefix.'pms_new_topics SET last_posted='.$last_post['posted'].', last_poster=\''.$db->escape($last_post['poster']).'\', replies='.$num_replies.' WHERE id='.$cur_topic['id']) or myerror('Unable to update topics', __FILE__, __LINE__, $db->error());
		}
	}

	// Add indexes
/*	echo "Adding database indexes...<br>\n"; flush();
	add_indexes();
*/
	// Regenerate the cache files
	echo "Regenerating caches files...<br>\n"; flush();
/*	$old_prefix = $db->prefix;
	$db->prefix = $_SESSION['pun'];*/
	require_once PUN_ROOT.'include/cache.php';
	generate_bans_cache();
	generate_quickjump_cache();
	generate_config_cache();
	if (function_exists('generate_ranks_cache')) // fluxbb 1.5 has dropped ranks table
		generate_ranks_cache();
	if (function_exists('generate_users_info_cache')) // fluxbb > 1.4.4
		generate_users_info_cache();
	if (function_exists('generate_colorize_groups_cache')) // colorize groups mod
		generate_colorize_groups_cache();
//	$db->prefix = $old_prefix;

	// End the timer
	$_SESSION['conv_end'] = microtime();

	// Create lock file
	echo "Closing the converter...<br>\n"; flush();
	if( !@file_exists('DEBUG') )
		@touch('LOCKED');

?>
