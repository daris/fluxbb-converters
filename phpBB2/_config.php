<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'PhpBB to FluxBB converter',
	'Forum'   => 'PhpBB',
	'Page'	 => '<b>PhpBB to FluxBB</b> converter at page: ',
	'db_def'  => 'phpbb',
	'pre_def' => 'phpbb_'
);

// List of pages to go through
$parts = array(
//		'groups',
	'users',
	'categories',
	'forums',
	'topics',
	'posts',
	'bans',
	'messages',
	'polls',
	'end'
);

$tables = array(
	'Users'			=>	'users',
	'Categories'	=>	$_SESSION['phpnuke'].'categories',
	'Forums'			=>	$_SESSION['phpnuke'].'forums',
	'Topics'			=>	$_SESSION['phpnuke'].'topics',
	'Posts'			=>	$_SESSION['phpnuke'].'posts',
	'Polls'			=>	$_SESSION['phpnuke'].'vote_desc',
	'Messages'		=>	$_SESSION['phpnuke'].'privmsgs',
);
$tablerem = array('Users' => 1);

include_once PUN_ROOT.'include/parser.php';

// Convert posts BB-code
function convert_posts($message){
	$pattern = array(
		// b, i och u
		'#\[b:[a-z0-9]{10}\]#i',
		'#\[/b:[a-z0-9]{10}\]#i',
		'#\[i:[a-z0-9]{10}\]#i',
		'#\[/i:[a-z0-9]{10}\]#i',
		'#\[u:[a-z0-9]{10}\]#i',
		'#\[/u:[a-z0-9]{10}\]#i',

		// Lists
		'#\[list=([a-z0-9]):[a-z0-9]{10}\]#i',
		'#\[list:[a-z0-9]{10}\]#i',
		'#\[/list:[a-z0-9]:[a-z0-9]{10}\]#i',
		'#\[\*:[a-z0-9]{10}\]#i',

		// Colors
		'#\[color=(.*?):[a-z0-9]{10}\]#i',
		'#\[/color:[a-z0-9]{10}\]#i',

		// Smileys ans stuff
		'#:roll:#i',
		'#:wink:#i',

		// Images
		'#\[img:[a-z0-9]{10}\]#i',
		'#\[/img:[a-z0-9]{10}\]#i',

		// Sizes
		'#\[size=[0-9]{1}:[a-z0-9]{10}\]#i',
		'#\[size=[0-9]{2}:[a-z0-9]{10}\]#i',
		'#\[/size:[a-z0-9]{10}\]#i',

		// Quotes och Code
		'#\[quote:(.*?)\]#i', // Tar dock bort vem som är quotad.
		'#\[/quote:[a-z0-9]{10}\]#i',
		'#\[code:[0-9]:[a-z0-9]{10}\]#i',
		'#\[/code:[0-9]:[a-z0-9]{10}\]#i'
	);
	$replace = array(
		// b, i och u
		'[b]',
		'[/b]',
		'[i]',
		'[/i]',
		'[u]',
		'[/u]',

		// Lists
		'[list=$1]',
		'[list]',
		'[/list]',
		'[*]',

		// Colors
		'[color=$1]',
		'[/color]',

		// Smileys and stuff
		':rolleyes:',
		';)',

		// Images
		'[img]',
		'[/img]',

		// Sizes
		'',
		'',
		'',

		// Quotes och Code
		'[quote]',
		'[/quote]',
		'[code]',
		'[/code]'
	);

	$errors = array();
	return preparse_bbcode(preg_replace($pattern, $replace, $message), $errors);
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}
