<?php

	// Clear fluxbb database
	truncate_tables();

	// Remove all indexes
//	remove_indexes();

	// Add guest account
	if($_SESSION['pun_version'] == '1.1')
		$db->query('INSERT INTO '.$db->prefix."users (username, password, email) VALUES('Guest', 'Guest', 'Guest')") or myerror('Unable to add guest user', __FILE__, __LINE__, $db->error());
	else
		$db->query('INSERT INTO '.$db->prefix."users (group_id, username, password, email) VALUES(3, 'Guest', 'Guest', 'Guest')") or myerror('Unable to add guest user', __FILE__, __LINE__, $db->error());

	// Insert the four preset groups
	$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood) VALUES('.($db_type != 'pgsql' ? '1, ' : '')."'Administrators', 'Administrator', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0)") or myerror('Unable to add group', __FILE__, __LINE__, $db->error());

	$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood) VALUES('.($db_type != 'pgsql' ? '2, ' : '')."'Moderators', 'Moderator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0)") or myerror('Unable to add group', __FILE__, __LINE__, $db->error());

	$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood) VALUES('.($db_type != 'pgsql' ? '3, ' : '')."'Guest', NULL, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 60, 30, 0)") or myerror('Unable to add group', __FILE__, __LINE__, $db->error());

	$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood) VALUES('.($db_type != 'pgsql' ? '4, ' : '')."'Members', NULL, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 60, 30, 60)") or myerror('Unable to add group', __FILE__, __LINE__, $db->error());
?>