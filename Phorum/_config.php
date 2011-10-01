<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'Phorum to FluxBB converter',
	'Forum'   => 'Phorum',
	'Page'	 => '<b>Phorum to FluxBB</b> converter at page: ',
	'db_def'  => 'phorum',
	'pre_def' => 'phorum_'
);

// List of pages to go through
$parts = array(
	'users',
	'categories',
	'forums',
	'posts',
	'end'
);

$tables = array(
	'Users'			=>	'users',
	'Forums'			=>	'forums',
	'Posts'			=>	'messages',
);

// Convert posts BB-code
function convert_posts($message){
/*		
	$pattern = array(
		// <b> <i> <u>
		'#\\[B\](.*?)\[/B\]#is',
		'#\\[I\](.*?)\[/I\]#is',
		'#\\[U\](.*?)\[/U\]#is',

		// Other
		'#\\[QUOTE\](.*?)\[/QUOTE\]#is',
		'#\\[QUOTE=(.*?)\](.*?)\[/QUOTE\]#is',
		'#\\[CODE\](.*?)\[/CODE\]#is',
		'#\\[PHP\](.*?)\[/PHP\]#is',
		'#\\[URL\](.*?)\[/URL\]#is',
		'#\\[URL=(.*?)\](.*?)\[/URL\]#is',
		'#\\[FTP=(.*?)\](.*?)\[/FTP\]#is',
		'#\\[IMG\](.*?)\[/IMG\]#is',
		'#\\[IMG=(.*?)\](.*?)\[/IMG\]#is',
		'#\\[EMAIL\](.*?)\[/EMAIL\]#is',
		'#\\[EMAIL=(.*?)\](.*?)\[/EMAIL\]#is',
		'#\\[COLOR=(.*?)\](.*?)\[/COLOR\]#is',
		'#\\[INDENT\](.*?)\[/INDENT\]#is',
		
		// Table
		'#\\[TABLE\](.*?)\[/TABLE\]\W#is',
		'#\\[TR\]#is',
		'#\\[/TR\]#is',
		'#\\[TD\](.*?)\[/TD\]\W#is',
		
		// Removed tags
		'#\\W\[GLOW=(.*?)\](.*?)\[/GLOW\]\W#is',
		'#\\W\[SHADOW=(.*?)\](.*?)\[/SHADOW\]\W#is',
		'#\\W\[LIST=(.*?)\](.*?)\[/LIST\]\W#is',
		'#\\W\[LIST\](.*?)\[/LIST\]\W#is',
		'#\\[\*\]#is',
		'#\\[HR\]#is',
		'#\\[FONT=(.*?)\](.*?)\[/FONT\]#is',
		'#\\[FLASH=(.*?)\](.*?)\[/FLASH\]#is',
		'#\\[SIZE=(.*?)\](.*?)\[/SIZE\]#is',
		'#\\[PRE\](.*?)\[/PRE\]#is',
		'#\\[LEFT\](.*?)\[/LEFT\]#is',
		'#\\[CENTER\](.*?)\[/CENTER\]#is',
		'#\\[RIGHT\](.*?)\[/RIGHT\]#is',
		'#\\[SUP\](.*?)\[/SUP\]#is',
		'#\\[SUB\](.*?)\[/SUB\]#is',
		'#\\[TT\](.*?)\[/TT\]#is',
		'#\\[S\](.*?)\[/S\]#is',
		'#\\[MOVE\](.*?)\[/MOVE\]#is',
	);
	
	$replace = array(
		// <b> <i> <u>
		'[b]$1[/b]',
		'[i]$1[/i]',
		'[u]$1[/u]',
		
		// Other
		'[quote]$1[/quote]',
		'[quote=$1]$2[/quote]',
		'[code]$1[/code]',
		'[code]$1[/code]',
		'[url]$1[/url]',
		'[url=$1]$2[/url]',
		'[url=$1]$2[/url]',
		'[img]$1[/img]',
		'[img=$1]$2[/img]',
		'[email]$1[/email]',
		'[email=$1]$2[/email]',
		'[color=$1]$2[/color]',
		'   $1',
		
		// Table
		'[b]Table:[/b] $1',
		'------------------------------------------------------------------',
		'------------------------------------------------------------------',
		"* $1\n",
		
		// Removed tags
		'$2',
		'$2',
		'$2',
		'$1',
		'* ',
		'------------------------------------------------------------------',
		'$2',
		'$2',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
		'$1',
	);
*/
	$message = str_replace('<br />', "\n", $message);
	$message = str_replace("&gt;:(", ':x', $message);
	$message = str_replace('::)', ':rolleyes:', $message);

//		return preg_replace($pattern, $replace, $message);
	return $message;
	
}
