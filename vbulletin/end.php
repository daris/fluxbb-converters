<?php

// Settings
$info = array();
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'setting');
while($ob = $fdb->fetch_assoc($result))
	$info[$ob['varname']] = $ob['value'];

// Dataarray
$conf = array(
	'o_board_title'		=>	$info['bbtitle'],
	'o_board_desc'			=>	$info['hometitle'],
	'o_server_timezone'	=>	$info['timeoffset'],
	'o_date_format'		=> $info['dateformat'],
	'o_time_format'		=>	$info['timeformat'],
	'o_mailing_list'		=>	$info['webmasteremail']
);

// Save settings
foreach($conf AS $var => $value)
	$db->query('UPDATE '.$db->prefix.'config SET conf_value=\''.addslashes($value).'\' WHERE conf_name=\''.$var.'\'') or myerror('Unable to update config', __FILE__, __LINE__, $db->error());

// Load gloval 'end' file
require './end.php';
