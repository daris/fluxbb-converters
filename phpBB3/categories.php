<?php

	// Fetch forum info
	$result = $fdb->query('SELECT * FROM '.$fdb->prefix.'categories') or myerror('Unable to fetch categories', __FILE__, __LINE__, $fdb->error());
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
	}

	// Redirect, don't check for more categories
	echo '<script type="text/javascript">window.location="index.php?page='.++$_GET['page'].'"</script>';

?>