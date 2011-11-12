<?php

$result = $fdb->query('SELECT '.$fdb->prefix.'ban_items.id_ban, '.$fdb->prefix.'members.member_name,
						ip_low1, ip_low2, ip_low3, ip_low4,
						'.$fdb->prefix.'ban_items.email_address, '.$fdb->prefix.'ban_groups.reason, '.$fdb->prefix.'ban_groups.expire_time
						FROM '.$fdb->prefix.'ban_items
						INNER JOIN '.$fdb->prefix.'ban_groups ON '.$fdb->prefix.'ban_groups.id_ban_group = '.$fdb->prefix.'ban_items.id_ban_group
						LEFT JOIN '.$fdb->prefix.'members ON '.$fdb->prefix.'members.id_member = '.$fdb->prefix.'ban_items.id_member
						WHERE id_ban>'.$start.' ORDER BY id_ban LIMIT '.ceil($_SESSION['limit']/5)) or myerror("Unable to get bans", __FILE__, __LINE__, $fdb->error());

$last_id = -1;
while ($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['id_ban'];

	$ob['member_name'] == '' ? $username = 'null' : $username = $ob['member_name'];
	$ob['ip_low1'] == '0' ? $ip = 'null' : $ip = trim($ob['ip_low1'].'.'.$ob['ip_low2'].'.'.$ob['ip_low3'].'.'.$ob['ip_low4'],'.');
	$ob['email_address'] == '' ? $email = 'null' : $email = $ob['email_address'];
	$ob['reason'] == '' ? $message = 'null' : $message = $ob['reason'];
	$ob['expire_time'] == '' ? $expire = 'null' : $expire = strtotime(date('Y-m-d', $ob['expire_time']).' GMT');

	echo $ob['id_ban'].' - '.($username == 'null' ? ($email == 'null' ? $ip : $email) : $username)."<br>\n"; flush();

	// Dataarray
	$todb = array(
		'username'	=>	$username,
		'ip'		=> 	$ip,
		'email'		=> 	$email,
		'message'	=> 	$message,
		'expire'	=> 	$expire,
	);

	// Save data
	insertdata('bans', $todb, __FILE__, __LINE__);
}

convredirect('id_ban', 'ban_items', $last_id);
