<?php

// Settings, such as page title...
$settings = array(
	'Title'   => 'ConnectixBoards to FluxBB converter',
	'Forum'   => 'ConnectixBoards',
	'Page'	 => '<b>ConnectixBoards to FluxBB</b> converter at page: ',
	'db_def'  => 'connectixboards',
	'pre_def' => 'cb_'
);

// List of pages to go through
$parts = array(
	'groups',
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
	'Groups'		=>	'groups',
	'Users'			=>	'users',
	'Categories'	=>	'forums',
	'Forums'		=>	'topicgroups',
	'Topics'		=>	'topics',
	'Posts'			=>	'messages',
	'Polls'			=>	'polls',
	'Messages'		=>	'mp',
);
$tablerem = array('Users' => 1);

include_once PUN_ROOT.'include/parser.php';

// Convert posts BB-code
function convert_posts($message)
{
	$message = convert_to_utf8($message);

	$message = str_replace("\n", '', $message);
	$pattern = array(
		// b, i, u, s and center
		'#<!--b--><span class="b">#i',
		'#</span><!--/b-->#i',
		'#<!--i--><span class="i">#i',
		'#</span><!--/i-->#i',
		'#<!--u--><span class="u">#i',
		'#</span><!--/u-->#i',
		'#<!--s--><span class="s">#i',
		'#</span><!--/u-->#i',
		'#<!--center--><span class="center">#i',
		'#</span><!--/center-->#i',

		// Lists
		'#<!--list(num)?--><(ul|ol)>#i',
		'#</(ul|ol)><!--/list(num)?-->#i',
		'#<li><span class="nodisplay">\[\*\]</span>#i',
		'#</li>#i',

		// Colors
		'#<!--color=--><span style="color:\s*(.*?);?">#i',
		'#</span><!--/color=-->#i',

		// Images
		'#<!--img--><img src="#i',
		'#" alt="Posted Image" />(<!--/img-->)?#i',

		// Smileys ans stuff
		'#<img src="smileys\/.*?" alt="(.*?)" class="smiley" />#i',
		
		//Links and emails
		'#<!--url=--><a href="(.*?)">#i',
		'#(<!--url-->)?<a href="(.*?)">#i',
		'#<!--email--><a href="mailto:(.*?)">#i',
		'#</a>(<!--/url=?-->)?#i',
		'#</a><!--/email-->#i',

		//Videos (ex: Youtube, Dailymotion)
		'#<!--flash--><object type="application/x-shockwave-flash" data="(.*?)" width="560" height="436"><param name="quality" value="high" /><param name="movie" value="(.*?)" /></object><span class="nodisplay">\[flash\]\[/flash\]</span><!--/flash-->#i',
		'#<!--youtube--><object width="425" height="350"><param name="movie" value="(.*?)"></param><param name="wmode" value="transparent"></param><embed src="(.*?)" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object><!--/youtube-->#i',

		// Sizes
		'#<!--size=--><span style="font-size:[0-9]{1,2}px;?">#i',
		'#</span><!--/(size|font)=-->#i',
		'#<!--font=--><span style="font-family:(.*?)">#i',

		// Quotes och Code
		'#<!--quote=?--><blockquote class="citationb?">(<p><span class="u">(.*?)</span></p>)?<p>#i',
		'#</p></blockquote><!--/quote=?-->#i',
		'#<!--(code|php)--><span class="code">(<p>)?<code>#i',
		'#</code>(</p>)?</span><!--/(code|php)-->#i',

		//Spoilers
		'#<!--spoil--><span class="spoil"><span class="spoil_info" onclick="hideAndShow\(\'spoil[0-9a-f]{32}\'\);">Spoiler</span><span class="spoil_spoiler" id="spoil[0-9a-f]{32}">#i',
		'#</span></span><script type="text/javascript">hideAndShow\(\'spoil[0-9a-f]{32}\'\);</script><!--/spoil-->#i',

		'#<br />#i'
	);
	$replace = array(
		// b, i, u, s and center
		'[b]',
		'[/b]',
		'[i]',
		'[/i]',
		'[u]',
		'[/u]',
		'[s]',
		'[/s]',
		'',
		'',

		// Lists
		'[list]',
		'[/list]',
		'[*]',
		'[/*]',

		// Colors
		'[color=$1]',
		'[/color]',
		
		// Images
		'[img]',
		'[/img]',

		// Smileys and stuff
		'$1',

		//Links and emails
		'[url=$1]',
		'[url]',
		'[email=$1]',
		'[/url]',
		'[/email]',

		//Videos (ex: youtube and dailymotion)
		'[url]$1[/url]',
		'[url]$1[/url]',

		// Sizes and font family
		'',
		'',
		'',

		// Quotes och Code
		'[quote]',
		'[/quote]',
		'[code]',
		'[/code]',
		
		//Spoilers
		'[quote]',
		'[/quote]',

		"\n"
	);
	$errors = array();
	return preparse_bbcode(preg_replace($pattern, $replace, $message), $errors);
}

function decode_ip($int_ip){
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}
