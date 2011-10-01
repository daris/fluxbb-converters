<?php

// Get phpBB config
echo "\n<br>Updating FluxBB settings"; flush();
$phpconfig = array();

// Xoops with newbb module
if ($_SESSION['phpnuke'] == 'bb_')
	$sql = 'SELECT conf_name, conf_value FROM '.$fdb->prefix.'config';
else
	$sql = 'SELECT config_name, config_value FROM '.$fdb->prefix.$_SESSION['phpnuke'].'config';

$result = $fdb->query($sql) or myerror('Unable to get forum info', __FILE__, __LINE__, $fdb->error());
while(list($name, $var) = $fdb->fetch_row($result)){
	$phpconfig[$name] = $var;
}

if (!isset($phpconfig['site_desc']))
	$phpconfig['site_desc'] = $phpconfig['slogan'];

// Save fluxbb config
$config = array(
	'o_board_title'			=> "'".$db->escape($phpconfig['sitename'])."'",
	'o_board_desc'				=> "'".$db->escape($phpconfig['site_desc'])."'",
	'o_server_timezone'		=> "'".$phpconfig['board_timezone']."'",
	'o_disp_topics_default'	=> "'".intval($phpconfig['topics_per_page'])."'",
	'o_disp_posts_default'	=> "'".intval($phpconfig['posts_per_page'])."'",
	'o_webmaster_email'		=> "'".$db->escape($phpconfig['board_email'])."'",
	'o_smtp_host'				=> "'".$db->escape($phpconfig['smtp_host'])."'",
	'o_smtp_user'				=> "'".$db->escape($phpconfig['smtp_username'])."'",
	'o_smtp_pass'				=> "'".$db->escape($phpconfig['smtp_password'])."'"
);
$config['o_disp_topics_default'] == 0 ? $config['o_disp_topics_default'] = 30 : null;
$config['o_disp_posts_default'] == 0 ? $config['o_disp_posts_default'] = 25 : null;
while (list($conf_name, $conf_value) = @each($config)){
	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.($conf_value).' WHERE conf_name=\''.$conf_name.'\'') or myerror('Unable to save config: '.$conf_name.'='.$conf_value, __FILE__, __LINE__, $db->error());
}

// Load global 'end' file
require './end.php';
