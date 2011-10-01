<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'SMF to FluxBB converter',
	'Forum'   => 'SMF',
	'Page'	 => '<b>SMF to FluxBB</b> converter at page: ',
	'db_def'  => 'smf',
	'pre_def' => 'smf_'
);

// List of pages to go through
$parts = array(
	'users',
	'categories',
	'forums',
	'topics',
	'posts',
	'end'
);

$tables = array(
	'Users'			=>	'members',
	'Categories'	=>	'categories',
	'Forums'			=>	'boards',
	'Topics'			=>	'topics',
	'Posts'			=>	'messages',
);

// Convert posts BB-code
function convert_posts($message){
	
/*	
[quote author=Shadow link=topic=2.msg3#msg3 date=1124894525]
I want some news...
[/quote]
No news... and so no forum either.
*/		
	
	
	
	
	$pattern = array(
		// Other
		'#\\[quote author=(.*?) link(.*?)\](.*?)\[/QUOTE\]#is',
		'#\\[flash=(.*?)\](.*?)\[/flash\]#is',
		'#\\[ftp=(.*?)\](.*?)\[/ftp\]#is',
		'#\\[font=(.*?)\](.*?)\[/font\]#is',
		'#\\[size=(.*?)\](.*?)\[/size\]#is',
		'#\\[list\](.*?)\[/list\]#is',
		'#\\[li\](.*?)\[/li\]#is',
		
		// Table
		'#\\[table\](.*?)\[/table\]#is',
		'#\\[tr\]#is',
		'#\\[/tr\]#is',
		'#\\[td\](.*?)\[/td\]#is',

		// Removed tags
		'#\\[glow=(.*?)\](.*?)\[/glow\]#is',
		'#\\[s\](.*?)\[/s\]#is',
		'#\\[shadow=(.*?)\](.*?)\[/shadow\]#is',
		'#\\[move\](.*?)\[/move\]#is',
		'#\\[pre\](.*?)\[/pre\]#is',

		'#\\[left\](.*?)\[/left\]#is',
		'#\\[right\](.*?)\[/right\]#is',
		'#\\[center\](.*?)\[/center\]#is',
		'#\\[sup\](.*?)\[/sup\]#is',
		'#\\[sub\](.*?)\[/sub\]#is',

		'#\\[hr\]#is',
		'#\\[tt\](.*?)\[/tt\]#is',
	);
	
	$replace = array(
		// Other
		'[quote=$1]$3[/quote]',
		'Flash: $2',
		'[url=$1]$2[/url]',
		'$2',
		'$2',
		'[b]List:[/b]$1'."\n",
		'·$1'."\n",
		
		// Table
		'$1',
		'------------------------------------------------------------------'."\n",
		'------------------------------------------------------------------'."\n",
		"* $1\n",
		
		// Removed tags
		'$2',
		'$1',
		'$2',
		'$1',
		'$1',

		'$1',
		'$1',
		'$1',
		'$1',
		'$1',

		'$1'."\n",
		'$1',
	);

	$message = str_replace('<br />', "\n", $message);
	$message = str_replace("&gt;:(", ':x', $message);
	$message = str_replace('::)', ':rolleyes:', $message);
	$message = str_replace('&nbsp;', ' ', $message);

	return preg_replace($pattern, $replace, $message);
	
}
