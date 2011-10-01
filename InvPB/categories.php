<?php

// Check if ver is 1.3 or not
if($_SESSION['ver'] == "13")
{
	// Fetch category info
	$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'categories WHERE id > 0') or myerror('Unable to fetch categories', __FILE__, __LINE__, $fdb->error());
	while($ob = $fdb->fetch_assoc($result)){

		echo htmlspecialchars($ob['name']).' ('.$ob['id'].")<br>\n"; flush();

		// Dataarray
		$todb = array(
			'id'					=>		$ob['id'],
			'cat_name'			=>		$ob['name'],
			'disp_position'	=>		$ob['position'],
		);
	
		// Save data
		insertdata('categories', $todb, __FILE__, __LINE__);
	}
}
