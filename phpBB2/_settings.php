<?php

// Check if it's PhpBB or PhpNuke
$result = $fdb->query('SELECT count(*) FROM '.$fdb->prefix.'bbforums');
if( $fdb->result($result, 0) == null )
	$_SESSION['phpnuke'] = '';
else
	$_SESSION['phpnuke'] = 'bb';
	
// Or maybe xoops with newbb
if ($fdb->table_exists($_SESSION['php_prefix'].'bb_forums', true))
	$_SESSION['phpnuke'] = 'bb_';

