<?php

// Decide if the forum is 1.3 or 2.0
$result = @$fdb->query('SELECT count(*) FROM '.$fdb->prefix.'banfilters');
if($fdb->result($result, 0) == null)
	$_SESSION['ver'] = '13';
else
	$_SESSION['ver'] = '20';
