<?php
$cat_count = 0;
// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'forums WHERE type=\'c\'') or myerror('Unable to fetch categories', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result))
{
	echo htmlspecialchars($ob['name']).' ('.$ob['fid'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'					=>		$ob['fid'],
		'cat_name'			=>		$ob['name'],	
		'disp_position'	=> 	$ob['disporder'],
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
