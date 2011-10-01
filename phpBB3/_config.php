<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'PhpBB3 to FluxBB converter',
	'Forum'   => 'PhpBB3',
	'Page'	 => '<b>PhpBB3 to FluxBB</b> converter at page: ',
	'db_def'  => 'phpbb',
	'pre_def' => 'phpbb_'
);

// List of pages to go through
$parts = array(
	'groups',
	'users',
	'forums',
//		'categories',
	'topics',
	'posts',
	'bans',
	'messages',
	'end'
);

$tables = array(
	'Users'			=>	'users',
//		'Categories'	=>	'categories',
	'Forums'		=>	'forums',
	'Topics'		=>	'topics',
	'Posts'			=>	'posts',
	'Polls'			=>	'vote_desc',
	'Messages'		=>	'privmsgs',
);
$tablerem = array('Users' => 1);

// Convert posts BB-code
function convert_posts($message)
{
	$pattern = array(
		// b, i och u
		'#\[b:[a-z0-9]{8}\]#i',
		'#\[/b:[a-z0-9]{8}\]#i',
		'#\[i:[a-z0-9]{8}\]#i',
		'#\[/i:[a-z0-9]{8}\]#i',
		'#\[u:[a-z0-9]{8}\]#i',
		'#\[/u:[a-z0-9]{8}\]#i',

		// Lists
		'#\[list=[a-z0-9]:[a-z0-9]{8}\]#i',
		'#\[list:[a-z0-9]{8}\]#i',
		'#\[/list:[a-z0-9]:[a-z0-9]{8}\]#i',
		'#\[\*:[a-z0-9]{8}\]#i',
		'#\[/\*:m:[a-z0-9]{8}\]#i',

		// Colors
		'#\[color=(.*?):[a-z0-9]{8}\]#i',
		'#\[/color:[a-z0-9]{8}\]#i',

		// Smileys ans stuff
		'#:roll:#i',
		'#:wink:#i',
		'#<!-- s.*? --><img src=".*?" alt="(.*?)" title=".*?" \/><!-- s.*? -->#i',

		// Images
		'#\[img:[a-z0-9]{8}\]#i',
		'#\[/img:[a-z0-9]{8}\]#i',

		// Sizes
		'#\[size=[0-9]{1}:[a-z0-9]{8}\]#i',
		'#\[size=[0-9]{2}:[a-z0-9]{8}\]#i',
		'#\[/size:[a-z0-9]{8}\]#i',

		// Quotes och Code
		'#\[quote="(.*?)":[a-z0-9]{8}\]#i',
		'#\[quote=(.*?):[a-z0-9]{8}\]#i',
		'#\[quote:(.*?)\]#i', // Tar dock bort vem som är quotad.
		'#\[/quote:[a-z0-9]{8}\]#i',
		'#\[code:[a-z0-9]{8}\]#i',
		'#\[/code:[a-z0-9]{8}\]#i',
		
		// Links
		'#<!-- m --><a class="postlink" href="(.*?)">(.*?)</a><!-- m -->#i',
		'#\[url=(.*?):[a-zA-Z0-9]{8}\](.*?)\[\/url:[a-zA-Z0-9]{8}\]#si',
		'#\[url:[a-zA-Z0-9]{8}\](.*?)\[\/url:[a-zA-Z0-9]{8}\]#si',
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
		'[list=]',
		'[list]',
		'[/list]',
		'[*]',
		'[/*]',

		// Colors
		'[color=$1]',
		'[/color]',

		// Smileys and stuff
		':rolleyes:',
		';)',
		'$1',

		// Images
		'[img]',
		'[/img]',

		// Sizes
		'',
		'',
		'',

		// Quotes och Code
		'[quote=$1]',
		'[quote=$1]',
		'[quote]',
		'[/quote]',
		'[code]',
		'[/code]',
		
		// Links
		'[url=$1]$2[/url]',
		'[url=$1]$2[/url]',
		'[url]$1[/url]',
	);

	return preg_replace($pattern, $replace, $message);
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

