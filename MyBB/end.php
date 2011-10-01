<?php

// Get phpBB config
echo "\n<br>Updating FluxBB settings"; flush();
$phpconfig = array();
$result = $fdb->query('SELECT name, value FROM '.$fdb->prefix.$_SESSION['phpnuke'].'settings') or myerror('Unable to get forum info', __FILE__, __LINE__, $fdb->error());
while (list($name, $var) = $fdb->fetch_row($result))
	$phpconfig[$name] = $var;


// Save fluxbb config
$config = array(
	'o_board_title'			=> '\''.$db->escape($phpconfig['bbname']).'\'',
	'o_webmaster_email'		=> '\''.$db->escape($phpconfig['adminemail']).'\'',
	'o_smtp_host'				=> '\''.$db->escape($phpconfig['smtp_host']).'\'',
	'o_smtp_user'				=> '\''.$db->escape($phpconfig['smtp_user']).'\'',
	'o_smtp_pass'				=> '\''.$db->escape($phpconfig['smtp_pass']).'\''
);
foreach($config as $conf_name => $conf_value)
	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.($conf_value).' WHERE conf_name=\''.$conf_name.'\'') or myerror('Unable to save config: '.$conf_name.'='.$conf_value, __FILE__, __LINE__, $db->error());


// Load global 'end' file
require './end.php';
