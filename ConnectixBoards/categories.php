<?php

$cat_count = 0;
// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums') or myerror('Unable to fetch categories', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result))
{
	echo htmlspecialchars($ob['forum_name']).' ('.$ob['forum_id'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'			=>		$ob['forum_id'],
		'cat_name'		=>		html_entity_decode(htmlspecialchars_decode($ob['forum_name']), ENT_QUOTES, 'UTF-8'),	
		'disp_position'	=>		$ob['forum_order'],
	);

	// Save data
	insertdata('categories', $todb, __FILE__, __LINE__);

	$cat_count++;
}

// If there are not categories, add default
if ($cat_count == 0)
{
	$todb = array(
		'id'			=> 1,
		'cat_name'		=> 'Default category',
		'disp_position'	=> 1,
	);

	// Save data
	insertdata('categories', $todb, __FILE__, __LINE__);
}
