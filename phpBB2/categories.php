<?php
$cat_count = 0;
// Fetch forum info
$result = $fdb->query('SELECT * FROM '.$fdb->prefix.$_SESSION['phpnuke'].'categories') or myerror('Unable to fetch categories', __FILE__, __LINE__, $fdb->error());
while($ob = $fdb->fetch_assoc($result))
{
	echo htmlspecialchars($ob['cat_title']).' ('.$ob['cat_id'].")<br>\n"; flush();

	// Dataarray
	$todb = array(
		'id'					=>		$ob['cat_id'],
		'cat_name'			=>		$ob['cat_title'],	
		'disp_position'	=> 	$ob['cat_order'],
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
