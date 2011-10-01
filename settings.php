<?php

	$type = intval($_SESSION['type']);
	$same = $type == 1 ? 'table-row' : 'none';
	$diff = $type == 2 ? 'table-row' : 'none';

?>

		<form method="post" action="index.php">
			<input type="hidden" name="punbb" value="<?php echo $db_name; ?>">
			<input type="hidden" name="punpre" value="<?php echo $db_prefix; ?>">

		<tr class="punhead">
			<th class="punhead" colspan="2"><b>FluxBB Migration Tool - Settings</b></th>
		</tr>

		<tr>
			<td class="puncon1right">Host:&nbsp</td>
			<td class="puncon2"><?php echo $db_host; ?></td>
		</tr>

		<tr>
			<td class="puncon1right">FluxBB database:&nbsp</td>
			<td class="puncon2"><?php echo $db_name; ?></td>
		</tr>

		<tr>
			<td class="puncon1right">FluxBB prefix:&nbsp</td>
			<td class="puncon2"><?php echo $db_prefix; ?></td>
		</tr>

		<tr>
			<td class="puncon1right">Hostname/user:&nbsp</td>
			<td class="puncon2">
				<label><input type="radio" name="sameordiff" onClick="duffusers('same');" value="same" <?php if($type == 1) echo 'checked'; ?>>Same host/user</label>
				<label><input type="radio" name="sameordiff" onClick="duffusers('diff');" value="diff" <?php if($type == 2) echo 'checked'; ?>>Different host/user</label>
			</td>
		</tr>

		<tr>
			<td class="puncon1right">Convert forum:&nbsp</td>
			<td class="puncon2">
<?php
	if(isset($_GET['forum']))
		echo "\t\t\t\t".'<span class="red">You must specify a forum!</span><br>'."\n";
?>
				<select style="width: 150px;" name="forum">
					<option value=""> - Select forum - </option>
<?php
	if ($handle = opendir('./'))
	{
		while (false !== ($file = readdir($handle)))
		{
			if (substr($file, 0, 1) != '.' && @$dir = opendir('./'.$file))
			{
				$selected = $_SESSION['forum'] == $file ? ' selected' : '';
				echo "\t\t\t\t\t".'<option value="'.$file.'"'.$selected.'>'.$file.'</option>'."\n";
				closedir($dir);
			}
		}
	   closedir($handle); 
	}
?>
				</select>
			</td>
		</tr>

		<tr id="diff1" style="display: <?php echo $diff; ?>;">
			<td class="puncon1right">Hostname:&nbsp</td>
			<td class="puncon2">
<?php
	if(isset($_GET['hostname']))
		echo "\t\t\t\t".'<span class="red">You must specify a hostname!</span><br>'."\n";
?>
				<input type="text" name="diff_host" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['hostname'])) echo $_SESSION['hostname']; ?>">
			</td>
		</tr>

		<tr id="diff2" style="display: <?php echo $diff; ?>;">
			<td class="puncon1right">Username:&nbsp</td>
			<td class="puncon2">
<?php
	if(isset($_GET['username']))
		echo "\t\t\t\t".'<span class="red">You must specify a username!</span><br>'."\n";
?>
				<input type="text" name="diff_un" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?>">
			</td>
		</tr>

		<tr id="diff3" style="display: <?php echo $diff; ?>;">
			<td class="puncon1right">Password:&nbsp</td>
			<td class="puncon2"><input type="text" name="diff_pass" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['password'])) echo $_SESSION['password']; ?>"></td>
		</tr>

		<tr id="diff4" style="display: <?php echo $diff; ?>;">
			<td class="puncon1right">Database:&nbsp</td>
			<td class="puncon2">
<?php
	if(isset($_GET['database']))
		echo "\t\t\t\t".'<span class="red">You must specify a database!</span><br>'."\n";
?>
				<input type="text" name="diff_db" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['php_db'])) echo $_SESSION['php_db']; ?>">
			</td>

		<tr id="same" style="display: <?php echo $same; ?>;">
			<td class="puncon1right">Database:&nbsp</td>
			<td class="puncon2">
<?php
	if(isset($_GET['database']))
		echo "\t\t\t\t".'<span class="red">You must specify a database!</span><br>'."\n";
?>
				<select style="width: 150px;" name="database">
					<option value=""> - Select database - </option>
<?php
		$res = $db->query('SHOW DATABASES');
		while($ob = $db->fetch_row($res))
		{
			$selected = (isset($_SESSION['php_db']) && $ob[0] == $_SESSION['php_db']) ? ' selected' : '';
			echo "\t\t\t\t\t".'<option value="'.$ob[0].'"'.$selected.'>'.$ob[0]."</option>\n";
		}
?>
				</select>
			</td>
		</tr>

		<tr>
			<td class="puncon1right">Prefix:&nbsp</td>
			<td class="puncon2"><input type="text" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['php_prefix'])) echo $_SESSION['php_prefix']; else echo ''; ?>" name="phppre"></td>
		</tr>

		<tr>
			<td class="puncon1right">Old charset:&nbsp</td>
			<td class="puncon2"><input type="text" style="width: 150px;" class="form" value="<?php if(isset($_SESSION['old_charset'])) echo ($_SESSION['old_charset'] != 'UTF-8') ? $_SESSION['old_charset'] : ''; else echo 'ISO-8859-2'; ?>" name="old_charset"> Leave blank for UTF-8.</td>
		</tr>

		<tr>
			<td class="puncon1right" style="width: 140px; white-space: nowrap">Actions:&nbsp;</td>
			<td class="puncon2" style="white-space: nowrap">
				<b>Warning:</b> <span style="color: red">ALL FluxBB tables will be deleted!</span><br>
				<br>&nbsp;&nbsp;<input type="submit" id="submit" name="start_converter" value="Start converter"><br /><br />
				If you run into problems with the converter, please send an email to<br />this address: <a href="mailto:chacmool@gmail.com">chacmool@gmail.com</a>. Please inform me which forum<br /> you're trying to convert, under which part of conversion it failed,<br /> and the whole error-message. <strong>Please also send a sql-dump of <br />your database, so I can try the converter with your actual data.</strong>
			</td>
		</tr>

		</form>
