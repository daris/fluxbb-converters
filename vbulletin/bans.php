<?php

$result = $fdb->query('SELECT b.*,u.username FROM '.$fdb->prefix.'userban AS b INNER JOIN '.$fdb->prefix.'user AS u ON u.userid=b.userid WHERE b.userid>'.$start.' ORDER BY userid LIMIT '.$_SESSION['limit']) or myerror("Unable to get posts", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['userid'];
	echo '<br>'.$ob['username']."\n"; flush();

	// Settings
	$ob['liftdate'] == 0 ? $ob['liftdate'] = 'null' : null;

	// Dataarray
	$todb = array(
		'username'	=>		$ob['username'],
		'expire'		=>		$ob['liftdate'],
	);

	// Save data
	insertdata('bans', $todb, __FILE__, __LINE__);
}

convredirect('userid', 'userban', $last_id);
