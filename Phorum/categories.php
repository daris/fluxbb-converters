<?php

// Dataarray
$todb = array(
	'id'					=>		'1',
	'cat_name'			=>		'Forums',
	'disp_position'	=> 	'0',
);

// Save data
insertdata('categories', $todb, __FILE__, __LINE__);
