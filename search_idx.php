<?php
	$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

?>
			<tr class="punhead">
				<th class="punhead" colspan="1">Updating search index <i><?php echo' ('.$start.')'; ?></i></th>
			</tr>
			<tr>
				<td class="puncon2">
<?php

	require PUN_ROOT.'include/search_idx.php';

	// Fetch posts to process this cycle
	$result = $db->query('SELECT p.id, p.message, t.subject, t.first_post_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.id > '.$start.' ORDER BY p.id ASC LIMIT '.$_SESSION['limit']) or myerror('Unable to fetch posts', __FILE__, __LINE__, $db->error());

	$last_id = 0;
	while ($cur_item = $db->fetch_assoc($result))
	{
		$last_id = $cur_item['id'];
		echo $cur_item['subject'].' ('.$cur_item['id'].")<br>\n"; flush();

		if ($cur_item['id'] == $cur_item['first_post_id'])
			update_search_index('post', $cur_item['id'], $cur_item['message'], $cur_item['subject']);
		else
			update_search_index('post', $cur_item['id'], $cur_item['message']);
	}

?>
				</td>
			</tr>
<?php

	// Check if there is more work to do
	if ($last_id > 0)
	{
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id > '.$last_id.' ORDER BY id ASC LIMIT 1') or myerror('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

		if ($db->num_rows($result) > 0)
		{
			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=search_idx&start='.$last_id.'" />';
			exit;
		}
	}

	echo '<tr><th>Done</th></tr>';
?>