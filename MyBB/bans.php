<?php

if($start == 0)
	next_step();

$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'banlist WHERE ban_id>'.$start.' ORDER BY ban_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['ban_id'];

	$username = '';
	if( $ob['ban_userid'] != 0 ){
		$res = $db->query('SELECT username FROM '.$db->prefix.'users WHERE id='.$ob['ban_userid']) or myerror("Unable to get userinfo for ban", __FILE__, __LINE__, $db->error());		
		list($username) = $db->fetch_row($res);
	}

	$ob['ban_ip'] == '' ? $ip = 'null' : $ip = decode_ip($ob['ban_ip']);
	$ob['ban_email'] == '' ? $ob['ban_email'] = 'null' : null;

	// Dataarray
	$todb = array(
		'username'	=>		$username,
		'ip'			=> 	$ip,
		'email'		=> 	$ob['ban_email'],
	);

	// Save data
	insertdata('bans', $todb, __FILE__, __LINE__);		
}

convredirect('ban_id', 'banlist', $last_id);
