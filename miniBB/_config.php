<?php

$settings = array(
	'Title'   => 'MiniBB to FluxBB',
	'Forum'   => 'miniBB',
	'db_def'  => 'minibb',
	'pre_def' => 'minibb_'
);

$parts = array(
	'start',
	'users',
	'posts',
	'topics',
	'forums',
	'end'
);

$tables = array(
	'Users'			=>	'users',
	'Categories'	=>	'',
	'Forums'			=>	'forums',
	'Topics'			=>	'topics',
	'Posts'			=>	'posts',
	'Polls'			=>	'',
	'Messages'		=>	'',
);

function convert_posts($message)
{
	$pattern = array(
		// <b> <i> <u>
		'#\<b>#i', '#\</b>#i',
		'#\<i>#i', '#\</i>#i',
		'#\<u>#i', '#\</u>#i',

		// Image -> <img src="http://www.etek.chalmers.se/punbb/img/Gold_new.png" border="0" align="" alt="">
		'#\<img src="(.*?)" border="0" align="" alt="">#i',

		// Quote
		'#\<div class="quote"><div class="quoting">(.*?)</div>(.*?)</div>#i',

		// Mail -> <a href="mailto:chacmool@spray.se">chacmool.spray.se</a>
		'#\<a href="mailto:(.*?)">(.*?)</a>#i',
		
		// Url -> <a href="http://www.garamonpatrimoine.org" target="_new">http://www.garamonpatrimoine.org</a>
		'#\<a href="(.*?)" target="_new">(.*?)</a>#i',
		'#\<a href="(.*?)" target="_blank">(.*?)</a>#i',

		// <br>
		'#\<br>#i'
		
	);
	
	$replace = array(
		// <b> <i> <u>
		'[b]', '[/b]',
		'[i]', '[/i]',
		'[u]', '[/u]',
		
		// Iamge -> [img]...[/img]
		'[img]$1[/img]', 
		
		// Quote
		'[quote=$1]$2[/quote]',
		
		// Mail -> [email]myname@mydomain.com[/email]
		'[email=$1]$2[/email]', 
		
		// Url -> [url]myurl.com[/url]
		'[url=$1]$2[/url]',
		'[url=$1]$2[/url]',
		
		// <br>
		"\r\n"
	);

	return preg_replace($pattern, $replace, $message);
}
