<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'MyBB to FluxBB converter',
	'Forum'   => 'MyBB',
	'Page'	 => '<b>MyBB to FluxBB</b> converter at page: ',
	'db_def'  => 'MyBB',
	'pre_def' => 'mybb_'
);

// List of pages to go through
$parts = array(
//	'groups',
	'users',
	'categories',
	'forums',
	'topics',
	'posts',
/*	'bans',
	'messages',
	'polls',*/
	'end'
);

$tables = array(
	'Users'			=>	'users',
	'Categories'	=>	'categories',
	'Forums'			=>	'forums',
	'Topics'			=>	'topics',
	'Posts'			=>	'posts',
/*		'Polls'			=>	'vote_desc',
	'Messages'		=>	'privmsgs',*/
);
$tablerem = array('Users' => 1);

include_once PUN_ROOT.'include/parser.php';

// Convert posts BB-code
function convert_posts($message)
{
//		return $message;
	$pattern = array(
		// b, i och u
		'#\[quote=\'(.*?)\'.*?\]\s*#si',
	);
	$replace = array(
		// b, i och u
		'[quote=$1]',
	);

	$errors = array();
	return /*preparse_bbcode(*/preg_replace($pattern, $replace, $message)/*, $errors)*/;
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}
