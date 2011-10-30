<?php

// Get CB config
echo "\n<br>Updating FluxBB settings<br/>"; flush();
$phpconfig = array();

$result = $fdb->query('SELECT cf_field, cf_value FROM '.$fdb->prefix.'config') or myerror('Unable to get forum info', __FILE__, __LINE__, $fdb->error());
while(list($name, $var) = $fdb->fetch_row($result))
	$phpconfig[$name] = $var;

// Save fluxbb config
$config = array(
	'o_board_title'			=> $phpconfig['forumname'],
	'o_board_desc'			=> 'Sample board description',
	'o_webmaster_email'		=> $phpconfig['supportmail'],
);
if (isset($phpconfig['defaulttimezone']))
	$config['o_server_timezone'] = $phpconfig['defaulttimezone'];

while (list($conf_name, $conf_value) = @each($config))
	$db->query('UPDATE '.$db->prefix.'config SET conf_value=\''.$db->escape($conf_value).'\' WHERE conf_name=\''.$conf_name.'\'') or myerror('Unable to save config: '.$conf_name.'='.$conf_value, __FILE__, __LINE__, $db->error());

// Load global 'end' file
require './end.php';
