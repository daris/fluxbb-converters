<?php

/**************
	Functions coppied from FluxBB db_update.php file :)
**************/

//
// Determines whether $str is UTF-8 encoded or not
//
function seems_utf8($str)
{
	$str_len = strlen($str);
	for ($i = 0; $i < $str_len; ++$i)
	{
		if (ord($str[$i]) < 0x80) continue; # 0bbbbbbb
		else if ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
		else if ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
		else if ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
		else if ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
		else if ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model

		for ($j = 0; $j < $n; ++$j) # n bytes matching 10bbbbbb follow ?
		{
			if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}

	return true;
}


//
// Translates the number from a HTML numeric entity into an UTF-8 character
//
function dcr2utf8($src)
{
	$dest = '';
	if ($src < 0)
		return false;
	else if ($src <= 0x007f)
		$dest .= chr($src);
	else if ($src <= 0x07ff)
	{
		$dest .= chr(0xc0 | ($src >> 6));
		$dest .= chr(0x80 | ($src & 0x003f));
	}
	else if ($src == 0xFEFF)
	{
		// nop -- zap the BOM
	}
	else if ($src >= 0xD800 && $src <= 0xDFFF)
	{
		// found a surrogate
		return false;
	}
	else if ($src <= 0xffff)
	{
		$dest .= chr(0xe0 | ($src >> 12));
		$dest .= chr(0x80 | (($src >> 6) & 0x003f));
		$dest .= chr(0x80 | ($src & 0x003f));
	}
	else if ($src <= 0x10ffff)
	{
		$dest .= chr(0xf0 | ($src >> 18));
		$dest .= chr(0x80 | (($src >> 12) & 0x3f));
		$dest .= chr(0x80 | (($src >> 6) & 0x3f));
		$dest .= chr(0x80 | ($src & 0x3f));
	}
	else
	{
		// out of range
		return false;
	}

	return $dest;
}


//
// Attempts to convert $str from $old_charset to UTF-8. Also converts HTML entities (including numeric entities) to UTF-8 characters
//
function convert_to_utf8($str)
{
	$old_charset = $_SESSION['old_charset'];
	
	if ($str === null || $str == '' || $old_charset == 'UTF-8')
		return $str;

	$save = $str;

	// Replace literal entities (for non-UTF-8 compliant html_entity_encode)
	if (version_compare(PHP_VERSION, '5.0.0', '<') && $old_charset == 'ISO-8859-1' || $old_charset == 'ISO-8859-15')
		$str = html_entity_decode($str, ENT_QUOTES, $old_charset);

	if ($old_charset != 'UTF-8' && !seems_utf8($str))
	{
		if (function_exists('iconv'))
			$str = iconv($old_charset == 'ISO-8859-1' ? 'WINDOWS-1252' : $old_charset, 'UTF-8', $str);
		else if (function_exists('mb_convert_encoding'))
			$str = mb_convert_encoding($str, 'UTF-8', $old_charset == 'ISO-8859-1' ? 'WINDOWS-1252' : 'ISO-8859-1');
		else if ($old_charset == 'ISO-8859-1')
			$str = utf8_encode($str);
	}

	// Replace literal entities (for UTF-8 compliant html_entity_encode)
	if (version_compare(PHP_VERSION, '5.0.0', '>='))
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

	// Replace numeric entities
	$str = preg_replace_callback('/&#([0-9]+);/', 'utf8_callback_1', $str);
	$str = preg_replace_callback('/&#x([a-f0-9]+);/i', 'utf8_callback_2', $str);

	// Remove "bad" characters
	$str = remove_bad_characters($str);

	return $str;//($save != $str);
}


function utf8_callback_1($matches)
{
	return dcr2utf8($matches[1]);
}


function utf8_callback_2($matches)
{
	return dcr2utf8(hexdec($matches[1]));
}


/***********************************/

// Redirect a page
function convredirect($id, $name, $last)
{
	global $fdb, $parts, $step;
	$num_sec = 0;
//exit('conv_red');
	if (!in_array($step, $parts))
	{
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?page=done" />';
		exit;
	}

	$cur_key = array_search($step, $parts);
	if ($cur_key < count($parts))
		$next_step = $parts[$cur_key + 1];
	else
	{
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?page=done" />';
		exit;
	}

		
	// Have no id
	if ($last == '')
	{
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?step='.$next_step.'" />';
		exit;
	}

	// More rows in database?
	$result = $fdb->query('SELECT '.$id.' FROM '.$fdb->prefix.$name.' WHERE '.$id.' >'.$last) or error('Unable to get count value for table: '.$name, __FILE__, __LINE__, $fdb->error());
	if (@$fdb->num_rows($result))
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?step='.$step.'&start='.$last.'" />';
	else
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?step='.$next_step.'" />';
	exit;
}

// Redirect a page
function next_step()
{
	global $parts, $step;
	
	$num_sec = 0;
	if (!in_array($step, $parts))
	{
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?page=done" />';
		exit;
	}

	$cur_key = array_search($step, $parts);
	if ($cur_key + 1 < count($parts))
	{
		$next_step = $parts[$cur_key + 1];

		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?step='.$next_step.'" />';
	}
	else
		echo '<meta http-equiv="refresh" content="'.$num_sec.'; URL=index.php?page=done" />';

	exit;
}

function insertdata($table, $todb, $file = __FILE__, $line = __LINE__)
{
	global $db;

	// Put together the query
	$names = $vars = array();
	foreach ($todb AS $name => $var)
	{
		if ($var != '')
		{
			$names[] = $name;
			$vars[] = ($var == 'null') ? $var : '\''.$db->escape(convert_to_utf8($var)).'\'';
		}
	}
	$db->query('INSERT INTO '.$db->prefix.$table.' ('.implode(',', $names).') VALUES('.implode(',', $vars).')') or myerror('Unable to save to database.<br><br><b>Query:</b> '.$query.'<br>', $file, $line, $db->error());
}

// Check settings
function checkInputValues()
{
	global $fdb, $parts;
	
	// Check connection
	$conn = @mysql_connect($_SESSION['hostname'], $_SESSION['username'], $_SESSION['password']);
	if(!$conn)
		myerror('Unable to connect to MySQL server. Please check your settings again.<br><br><a href="?page=settings">Go back to settings</a>');

	// Check databases	
	if(!@mysql_select_db($_SESSION['php_db'], $conn))
	{
		// Fetch database list
		$list = '';
		$result = @mysql_query('SHOW databases', $conn);
		while($ob = mysql_fetch_row($result))
			$list .= ' &nbsp <a href="?page=settings&newdb='.$ob[0].'">'.$ob[0].'</a><br>'."\n";

		// Close connection and show message
		mysql_close($conn);
		myerror(
			'Unable to select database.'
			.'<br><br>Found these databases:<br><font color="gray">'.$list.'</font>'
			.'<br><a href="?page=settings">Go back to settings</a>'
		);
	}
	mysql_close($conn);

	// Include FORUM's config file
	include './'.$_SESSION['forum'].'/_config.php';

	// Check prefix
	$fdb = new DBLayer($_SESSION['hostname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['php_db'], $_SESSION['php_prefix'], false);
	$res = $fdb->query('SELECT count(*) FROM '.$fdb->prefix.$tables['Users']);
	if( intval($fdb->result($res, 0)) == 0)
	{
		// Select a list of tables
		$list = array();
		$res = $fdb->query('SHOW TABLES');
		while($ob = $fdb->fetch_row($res))
			$list[] = $ob[0];

		// check list size
		sizeof($list) == 0 ? $list[] = 'None' : null;

		// Get list of "proabable" prefixes
		$prefix_list = '';
		$res = $fdb->query('SHOW TABLES FROM '.$_SESSION['php_db'].' LIKE \'%'.$tables['Posts'].'\'') or myerror('Unable to fetch table list', __FILE__, __LINE__, $fdb->error());
//			$res = $fdb->query('SHOW TABLES FROM '.$_SESSION['php_db'].' LIKE \'%'.$tables['Users'].'\'') or myerror('Unable to fetch table list', __FILE__, __LINE__, $fdb->error());
		while($ob = $fdb->fetch_row($res))
		{
			$prefix = substr($ob[0], 0, strlen($ob[0]) - strlen($tables['Users']));
			$prefix_list .= ' &nbsp; <a href="?page=settings&newprefix='.$prefix.'">'.$prefix.'</a><br>'."\n";
		}
		
		// Print message
		$prefix = $_SESSION['php_prefix'] == '' ? 'no' : '\''.$_SESSION['php_prefix'].'\'';
		myerror(
			'Unable to find '.$_SESSION['forum'].' tables! (using prefix: <i>'.$prefix.'</i>)'
			.'<br><br>Go back to settings and choose another prefix, or select one of these prefixes:<br><font color="gray">'.$prefix_list.'</font>'
			.'<br>These are the tables in the selected database:<br><font color="gray"> &nbsp; '.implode("<br> &nbsp; ", $list).'</font>'
			.'<br><br><a href="?page=settings">Go back to settings</a>'
		);
	}
}

// Print an array
function mydump($array, $exit = false)
{

	echo '<pre>';
	print_r($array);
	echo '</pre>';
	
	if($exit)
		exit;

}

// Calculate time
function generatedtime($start, $finish)
{
	
	list( $start1, $start2 ) = explode( ' ', $start );
	list( $finish1, $finish2 ) = explode( ' ', $finish );

	return sprintf( "%.2f", ($finish1 + $finish2) - ($start1 + $start2) );

}

// HTML
function html($message)
{
	
	$pattern = array(
		'/&gt;/i',
		'/&lt;/i',
		'/&amp;/i',
		'/&quot;/i',
		'/&#039;/i'
	);

	$replace = array(
		'>',
		'<',
		'&',
		'"',
		"'"
	);
	
	return preg_replace($pattern, $replace, $message);
}


//
// Display a simple error message
//
function myerror($message, $file = null, $line = null, $db_error = false)
{
	global $pun_config, $lang_common;

	// Set some default settings if the script failed before $pun_config could be populated
	if (empty($pun_config))
	{
		$pun_config = array(
			'o_board_title'	=> 'FluxBB',
			'o_gzip'		=> '0'
		);
	}

	// Set some default translations if the script failed before $lang_common could be populated
	if (empty($lang_common))
	{
		$lang_common = array(
			'Title separator'	=> ' / ',
			'Page'				=> 'Page %s'
		);
	}

	// Empty all output buffers and stop buffering
	while (@ob_end_clean());

	// "Restart" output buffering if we are using ob_gzhandler (since the gzip header is already sent)
	if ($pun_config['o_gzip'] && extension_loaded('zlib'))
		ob_start('ob_gzhandler');

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache'); // For HTTP/1.0 compatibility

	// Send the Content-type header in case the web server is setup to send something else
	header('Content-type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), 'Error') ?>
<title><?php echo generate_page_title($page_title) ?></title>
<style type="text/css">
<!--
BODY {MARGIN: 10% 20% auto 20%; font: 10px Verdana, Arial, Helvetica, sans-serif}
#errorbox {BORDER: 1px solid #B84623}
H2 {MARGIN: 0; COLOR: #FFFFFF; BACKGROUND-COLOR: #B84623; FONT-SIZE: 1.1em; PADDING: 5px 4px}
#errorbox DIV {PADDING: 6px 5px; BACKGROUND-COLOR: #F1F1F1}
-->
</style>
</head>
<body>

<div id="errorbox">
	<h2>An error was encountered</h2>
	<div>
<?php

	if (defined('PUN_DEBUG') && $file !== null && $line !== null)
	{
		echo "\t\t".'<strong>File:</strong> '.$file.'<br />'."\n\t\t".'<strong>Line:</strong> '.$line.'<br /><br />'."\n\t\t".'<strong>FluxBB reported</strong>: '.$message."\n";

		if ($db_error)
		{
			echo "\t\t".'<br /><br /><strong>Database reported:</strong> '.pun_htmlspecialchars($db_error['error_msg']).(($db_error['error_no']) ? ' (Errno: '.$db_error['error_no'].')' : '')."\n";

			if ($db_error['error_sql'] != '')
				echo "\t\t".'<br /><br /><strong>Failed query:</strong> '.pun_htmlspecialchars($db_error['error_sql'])."\n";
		}
	}
	else
		echo "\t\t".'Error: <strong>'.$message.'.</strong>'."\n";

	if (!@file_exists('DEBUG') && strpos($message, 'These are the tables in the selected database') === false)
		echo '<img src="http://fluxbb.orge.pl/conv/error.php?version='.CONV_VERSION.'&forum='.$_SESSION['forum'].'&error='.urlencode($message).'&file='.urlencode($file).'>&line='.urlencode($line).(isset($db_error['error']) ? '&dberror='.urlencode($db_error['error']) : '').(isset($db_error['errno']) ? '&dberrorno='.urlencode($db_error['errno']) : '').(isset($db_error['error_sql']) ? '&error_sql='.urlencode($db_error['error_sql']) : '').'" alt="" border="0" width="1" height="1" /><br />Report complete error message above in <a href="http://fluxbb.org/forums/viewtopic.php?id=4579">topic on FluxBB forums</a>.';

?>
	</div>
</div>

</body>
</html>
<?php

	// If a database connection was established (before this error) we close it
	if ($db_error)
		$GLOBALS['db']->close();

	exit;
}

function conv_message($message, $no_back_link = false)
{
	global $lang_common;
?>
<div id="msg" class="block">
	<h2><span><?php echo $lang_common['Info'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $message ?></p>
<?php if (!$no_back_link): ?>			<p><a href="javascript: history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
<?php endif; ?>		</div>
	</div>
</div>
<?php

}


function update_forum_info()
{
	global $db;

	// Stats: posts
	$restul = $db->query('SELECT count(id) FROM '.$db->prefix.'posts') or error('Unable to fetch stat count', __FILE__, __LINE__, $db->error());
	$num_posts = $db->result($result, 0);
	// Stats: posts
	$restul = $db->query('SELECT count(id) FROM '.$db->prefix.'topics') or error('Unable to fetch stat count', __FILE__, __LINE__, $db->error());
	$num_topics = $db->result($result, 0);
	// Stats: users
	$restul = $db->query('SELECT count(id) FROM '.$db->prefix.'users') or error('Unable to fetch stat count', __FILE__, __LINE__, $db->error());
	$num_users = $db->result($result, 0);

}
/*
function remove_indexes()
{
	global $db;

	// Removing indexes
	$drop = array();
	if($_SESSION['pun_version'] == '1.1')
	{
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts DROP INDEX '.$_SESSION['pun_prefix'].'posts_topic_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts DROP INDEX '.$_SESSION['pun_prefix'].'posts_poster_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'reports DROP INDEX '.$_SESSION['pun_prefix'].'reports_zapped_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches DROP INDEX '.$_SESSION['pun_prefix'].'search_matches_word_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches DROP INDEX '.$_SESSION['pun_prefix'].'search_matches_post_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_results DROP INDEX '.$_SESSION['pun_prefix'].'search_results_ident_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'subscriptions DROP INDEX '.$_SESSION['pun_prefix'].'subscriptions_user_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'subscriptions DROP INDEX '.$_SESSION['pun_prefix'].'subscriptions_topic_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics DROP INDEX '.$_SESSION['pun_prefix'].'topics_forum_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users DROP INDEX '.$_SESSION['pun_prefix'].'users_registered_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users DROP INDEX '.$_SESSION['pun_prefix'].'users_username_idx';
	}
	else
	{
		$queries[] = 'ALTER TABLE '.$db->prefix.'online DROP INDEX '.$_SESSION['pun_prefix'].'online_user_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts DROP INDEX '.$_SESSION['pun_prefix'].'posts_topic_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts DROP INDEX '.$_SESSION['pun_prefix'].'posts_multi_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'reports DROP INDEX '.$_SESSION['pun_prefix'].'reports_zapped_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches DROP INDEX '.$_SESSION['pun_prefix'].'search_matches_word_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches DROP INDEX '.$_SESSION['pun_prefix'].'search_matches_post_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics DROP INDEX '.$_SESSION['pun_prefix'].'topics_forum_id_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics DROP INDEX '.$_SESSION['pun_prefix'].'topics_moved_to_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users DROP INDEX '.$_SESSION['pun_prefix'].'users_registered_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_cache DROP INDEX '.$_SESSION['pun_prefix'].'search_cache_ident_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users DROP INDEX '.$_SESSION['pun_prefix'].'users_username_idx';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_words DROP INDEX '.$_SESSION['pun_prefix'].'search_words_id_idx';
	}

	@reset($queries);
	while (list(, $sql) = @each($queries))
		$db->query($sql);// or myerror('Unable to create index', __FILE__, __LINE__, $db->error());

}

function add_indexes()
{
	global $db;
	$queries = array();

	// PunBB 1.1
	if($_SESSION['pun_version'] == '1.1')
	{
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts ADD INDEX '.$_SESSION['pun_prefix'].'posts_topic_id_idx(topic_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts ADD INDEX '.$_SESSION['pun_prefix'].'posts_poster_id_idx(poster_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'reports ADD INDEX '.$_SESSION['pun_prefix'].'reports_zapped_idx(zapped)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches ADD INDEX '.$_SESSION['pun_prefix'].'search_matches_word_id_idx(word_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches ADD INDEX '.$_SESSION['pun_prefix'].'search_matches_post_id_idx(post_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_results ADD INDEX '.$_SESSION['pun_prefix'].'search_results_ident_idx(ident)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'subscriptions ADD INDEX '.$_SESSION['pun_prefix'].'subscriptions_user_id_idx(user_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'subscriptions ADD INDEX '.$_SESSION['pun_prefix'].'subscriptions_topic_id_idx(topic_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics ADD INDEX '.$_SESSION['pun_prefix'].'topics_forum_id_idx(forum_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users ADD INDEX '.$_SESSION['pun_prefix'].'users_registered_idx(registered)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users ADD INDEX '.$_SESSION['pun_prefix'].'users_username_idx(username(3))';
	}

	// PunBB 1.2
	else
	{
		$queries[] = 'ALTER TABLE '.$db->prefix.'online ADD INDEX '.$_SESSION['pun_prefix'].'online_user_id_idx(user_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts ADD INDEX '.$_SESSION['pun_prefix'].'posts_topic_id_idx(topic_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'posts ADD INDEX '.$_SESSION['pun_prefix'].'posts_multi_idx(poster_id, topic_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'reports ADD INDEX '.$_SESSION['pun_prefix'].'reports_zapped_idx(zapped)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches ADD INDEX '.$_SESSION['pun_prefix'].'search_matches_word_id_idx(word_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_matches ADD INDEX '.$_SESSION['pun_prefix'].'search_matches_post_id_idx(post_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics ADD INDEX '.$_SESSION['pun_prefix'].'topics_forum_id_idx(forum_id)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'topics ADD INDEX '.$_SESSION['pun_prefix'].'topics_moved_to_idx(moved_to)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users ADD INDEX '.$_SESSION['pun_prefix'].'users_registered_idx(registered)';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_cache ADD INDEX '.$_SESSION['pun_prefix'].'search_cache_ident_idx(ident(8))';
		$queries[] = 'ALTER TABLE '.$db->prefix.'users ADD INDEX '.$_SESSION['pun_prefix'].'users_username_idx(username(8))';
		$queries[] = 'ALTER TABLE '.$db->prefix.'search_words ADD INDEX '.$_SESSION['pun_prefix'].'search_words_id_idx(id)';
	}

	@reset($queries);
	while (list(, $sql) = @each($queries))
		$db->query($sql);// or myerror('Unable to create index', __FILE__, __LINE__, $db->error());

}
*/
function truncate_tables()
{
	global $db;
	
	// Truncate the tables just in-case we didn't already (if we are coming directly here without converting the tables)
	$db->truncate_table('categories');
	$db->truncate_table('censoring');
	$db->truncate_table('posts');
	$db->truncate_table('forums');
	$db->truncate_table('groups');
	$db->truncate_table('ranks');
	$db->truncate_table('search_matches');
	$db->truncate_table('search_results');
	$db->truncate_table('search_words');
	$db->truncate_table('search_cache');
	$db->truncate_table('search_matches');
	$db->truncate_table('topics');
	$db->truncate_table('users');
	$db->truncate_table('bans');
	$db->truncate_table('polls'); // AutoPoll
	$db->truncate_table('pms_new_posts'); // New PMS
	$db->truncate_table('pms_new_topics'); // New PMS
	$db->truncate_table('pms_new_block'); // New PMS

	$db->query('ALTER TABLE '.$db->prefix.'search_words auto_increment=1');
}

?>