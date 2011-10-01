<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'vBulletin to FluxBB converter',
	'Forum'   => 'vBulletin',
	'Page'	 => '<b>vBulletin to FluxBB</b> converter at page: ',
	'db_def'  => 'vbulletin',
	'pre_def' => 'vb3_'
);

// List of pages to go through
$parts = array(
//		'start',
	'users',
	'forums',
	'topics',
	'posts',
//		'polls',
//		'messages',
	'bans',
	'end'
);

$tables = array(
	'Users'			=>	'user',
	'Categories'	=>	'',
	'Forums'			=>	'',
	'Topics'			=>	'thread',
	'Posts'			=>	'post',
	'Polls'			=>	'poll',
	'Messages'		=>	'pm',
);
/*
// Defined constants used for user field.
$_USEROPTIONS = array(
	'showsignatures'    => 1,
	'showavatars'       => 2,
	'showimages'        => 4,
	'coppauser'         => 8,
	'adminemail'        => 16,
	'showvcard'         => 32,
	'dstauto'           => 64,
	'dstonoff'          => 128,
	'showemail'         => 256,
	'invisible'         => 512,
	'showreputation'    => 1024,
	'receivepm'         => 2048,
	'emailonpm'         => 4096,
	'hasaccessmask'     => 8192,
	'postorder'         => 32768,
);
*/
// Convert posts BB-code
function convert_posts($message){
	
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
		'#\\[IMG\](.*?)\[/IMG\]#is',
		'#\\[IMG=(.*?)\](.*?)\[/IMG\]#is',
		'#\\[EMAIL\](.*?)\[/EMAIL\]#is',
		'#\\[EMAIL=(.*?)\](.*?)\[/EMAIL\]#is',
		'#\\[COLOR=(.*?)\](.*?)\[/COLOR\]#is',
		'#\\[INDENT\](.*?)\[/INDENT\]#is',
		
		// Removed tags
		'#\\W\[LIST=(.*?)\](.*?)\[/LIST\]\W#is',
		'#\\[\*\]#is',
		'#\\[FONT=(.*?)\](.*?)\[/FONT\]#is',
		'#\\[SIZE=(.*?)\](.*?)\[/SIZE\]#is',
		'#\\[LEFT\](.*?)\[/LEFT\]#is',
		'#\\[CENTER\](.*?)\[/CENTER\]#is',
		'#\\[RIGHT\](.*?)\[/RIGHT\]#is',
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
		'[img]$1[/img]',
		'[img=$1]$2[/img]',
		'[email]$1[/email]',
		'[email=$1]$2[/email]',
		'[color=$1]$2[/color]',
		'   $1',
		
		// Lists
		'$2',
		'* ',
		'$2',
		'$1',
		'$1',
		'$1',
		'$1',
	);

	// Convert some smileys
	$message = str_replace(':d', ':D', $message);
	$message = str_replace(':p', ':P', $message);

	return preg_replace($pattern, $replace, $message);
	
}
