<?php

	$time = generatedtime($_SESSION['conv_start'], $_SESSION['conv_end']);
	include './'.$_SESSION['forum'].'/_config.php';

	// Should not be here... redirect to the init page	
	if($time == '0.00')	
		redirect('index.php?page=init', 'Please go to the init page to start the converter.');

	// Status settings
	$status = array();
	$status['adm'] = 'group_id=1';
	$status['!adm'] = 'group_id!=1';
	$status['mod'] = 'group_id=2';
	$status['!mod'] = 'group_id!=2';
	$status['usr'] = 'group_id=4';

	if(isset($_GET['remove']))
	{
		if($_GET['remove'] == 'a_all')
			$db->query('UPDATE '.$db->prefix.'users SET '.$status['usr'].' WHERE '.$status['adm']) or myerror('Unable to remove all admins', __FILE__, __LINE__, $db->query());

		else if($_GET['remove'] == 'm_all')
			$db->query('UPDATE '.$db->prefix.'users SET '.$status['usr'].' WHERE '.$status['mod']) or myerror('Unable to remove all moderators', __FILE__, __LINE__, $db->query());
	
		else
		{
			$uid = intval($_GET['remove']);
			$db->query('UPDATE '.$db->prefix.'users SET '.$status['usr'].' WHERE id='.$uid) or myerror('Unable to remove user status', __FILE__, __LINE__, $db->query());
		}
	}

	else if(isset($_POST['newadmin']))
	{
		$db->query('UPDATE '.$db->prefix.'users SET '.$status['adm'].' WHERE id='.intval($_POST['newadmin']).' AND id>1') or myerror('Unable to change user status to admin', __FILE__, __LINE__, $db->query());

	}

	else if(isset($_POST['newmoderator']))
	{
		$db->query('UPDATE '.$db->prefix.'users SET '.$status['mod'].' WHERE id='.intval($_POST['newmoderator']).' AND id>1') or myerror('Unable to change user status to moderator', __FILE__, __LINE__, $db->query());

	}

?>

		<tr class="punhead">
			<th class="punhead" colspan="2"><b><?php echo $settings['Title']; ?></b> conversion done</th>
		</tr>

		<tr>
			<td class="puncon1right">Info&nbsp;&nbsp;</td>
			<td class="puncon2">
				<p><b>Conversion done!</b></p>

				<p>Database converted in: <b><?php echo $time; ?>s</b></p>

				<p><strong>
<?php /*					<font color='red'>Note:</font> Don't forget to rebuild the search index!<br><br>
					The converter does not uppdate the search-index, so that
					must be done manually (<a href="../admin_maintenance.php">Administration->Maintenance->Rebuild index</a>).*/ ?>
					There's alot of settings that's not converted, so go
					through the admin-pages for additional settings.
				</strong></p>

				<p>Please also make sure you have at least one board administrator by using the list below, or else you won't be able to administrate your board!</p>
				
				<p style="color: red; font-weight: bold">If this tool does not convert passwords, you can install Password Converter Mod (file password_converter_mod.txt in converter directory).</p>
			</td>
		</tr>
<?php /*
		<tr>
			<td class="puncon1right">Donate&nbsp;&nbsp;</td>
			<td class="puncon2">
				<p>If you want to support the converter development, you can donate money using PayPal:</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<p><input type="submit" name="submit" value="Make a Donation"></p>
				</form>
			</td>
		</tr>
*/ ?>
		<tr>
			<td class="puncon1right">FluxBB administrators&nbsp;&nbsp;</td>
			<td class="puncon2">
				<form method="post" action="index.php?page=done">
<?php
	$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$status['adm'].' ORDER BY username ASC');
	$counter = 0;
	
	if($db->num_rows($result) == 0)
		echo 'None';
	else
		while($user = $db->fetch_assoc($result))
			echo "\t\t\t\t\t".++$counter.'. '.$user['username'].' <span style="float: right;">[<a href="index.php?page=done&remove='.$user['id'].'"> remove </a>]</span><br>'."\n";
?>
					<br><b>New administrator:</b><br>
					<select name="newadmin">
<?php

	$f1 = '';
	$res = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$status['!adm'].' AND id!=1 ORDER by username ASC');
	while($user = $db->fetch_assoc($res)){
		$f2 = substr($user['username'], 0, 1);
		
		if(strtolower($f2) != $f1)
		{
			echo $f1 != '' ? "\t\t\t\t\t\t</optgroup>\n" : '';
			echo "\t\t\t\t\t\t".'<optgroup label="'.strtoupper($f2).'">'."\n";
		}

		echo "\t\t\t\t\t\t\t".'<option value="'.$user['id'].'">'.$user['username'].'</option>'."\n";
		$f1 = strtolower($f2);
	}
?>
						</optgroup>
					</select>
					<input type="submit" name="submit" value="Add">
<?php if($db->num_rows($result) > 0): ?>
					<span style="float: right;">[<a href="index.php?page=done&remove=a_all"> remove all </a>]</span>
<?php endif; ?>
				</form>
			</td>
		</tr>

		<tr>
			<td class="puncon1right">FluxBB moderators&nbsp;&nbsp;</td>
			<td class="puncon2">
				<form method="post" action="index.php?page=done">
<?php
	$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$status['mod'].' ORDER BY username ASC');
	$counter = 0;

	if($db->num_rows($result) == 0)
		echo 'None';
	else
		while($user = $db->fetch_assoc($result))
			echo "\t\t\t\t\t".++$counter.'. '.$user['username'].'<span style="float: right;">[<a href="index.php?page=done&remove='.$user['id'].'"> remove </a>]</span><br>'."\n";	
?>
					<br><b>New moderator:</b><br>
					<select name="newmoderator">
<?php
	while($user = $db->fetch_assoc($res)){
		echo "\t\t\t\t\t\t".'<option value="'.$user['id'].'">'.$user['username'].'</option>'."\n";
	}

	$f1 = '';
	$res = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$status['!mod'].' AND id!=1 ORDER by username ASC');
	while($user = $db->fetch_assoc($res)){
		$f2 = substr($user['username'], 0, 1);
		
		if(strtolower($f2) != $f1)
		{
			echo $f1 != '' ? "\t\t\t\t\t\t</optgroup>\n" : '';
			echo "\t\t\t\t\t\t".'<optgroup label="'.strtoupper($f2).'">'."\n";
		}

		echo "\t\t\t\t\t\t\t".'<option value="'.$user['id'].'">'.$user['username'].'</option>'."\n";
		$f1 = strtolower($f2);
	}
?>
						</optgroup>
					</select>
					<input type="submit" name="submit" value="Add">
<?php if($db->num_rows($result) > 0): ?>
					<span style="float: right;">[<a href="index.php?page=done&remove=m_all"> remove all </a>]</span>
<?php endif; ?>
				</form>
			</td>
		</tr>

		<tr>
			<td class="puncon1right">FluxBB status&nbsp;&nbsp;</td>
			<td class="puncon2">
<?php
	echo "\t\t\t\t<br><b><u>FluxBB contains</u></b> (".$settings['Forum']." contains):";

	// Step through the tables
	foreach($tables AS $name => $value)
	{
		// Show it or not?
		if($value != ''){

			// FluxBB
			$rem = $name == 'Users' ? '-1' : ''; // Remove guest user
			$res1 = $db->query('SELECT count(*)'.$rem.' AS count FROM '.$db->prefix.$name.'');

			// Save FluxBB post count
			if($name == 'Posts')
				$_SESSION['posts'] = $db->result($res1, 0);

			// Converted forum
			$rem = isset($tablerem[$name]) ? '-'.$tablerem[$name] : '';
			$res2 = $fdb->query('SELECT count(*)'.$rem.' AS count FROM '.$fdb->prefix.$value.'');

			// Line
			echo "\n\t\t\t\t<br><b>".$name.":</b> ".@$db->result($res1, 0).' ('.@$fdb->result($res2, 0).')';
		}
	}

	echo '<br><br>'."\n";
?>
			</td>
		</tr>

		<tr>
			<td class="puncon1right" style="width: 140px; white-space: nowrap">Action&nbsp;&nbsp;</td>
			<td class="puncon2" style="white-space: nowrap">
				<form method="post" action="index.php?page=settings">
					<br>&nbsp;&nbsp;<input type="submit" name="submit" value="Restart"><br><br>
				</form>
			</td>
		</tr>

		</form>