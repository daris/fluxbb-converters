<?php
session_start();

define('PUN_ROOT', '../');
define('CONV_VERSION', '140');
@include PUN_ROOT.'include/common.php';
require 'functions.php';

// Check if FluxBB is initialized
if(!defined('PUN'))
	myerror('Unable to locate FluxBB!<br><br>Please put the converter in a subdirectory in the fluxbb-directory.');

// Enable debug-mode
if (!defined('PUN_DEBUG'))
	define('PUN_DEBUG', 1);
if (!defined('PUN_SHOW_QUERIES'))
	define('PUN_SHOW_QUERIES', 1);

error_reporting(E_ALL);

$page = isset($_GET['page']) ? basename($_GET['page']) : null;
$step = isset($_GET['step']) ? basename($_GET['step']) : null;

!isset($_SESSION['hostname']) ? $_SESSION['hostname'] = $db_host : null;
!isset($_SESSION['type']) ? $_SESSION['type'] = 1 : null;

// Set database and prefix (link from checkInputValues())
if (isset($_GET['newdb']))
	$_SESSION['php_db'] = $_GET['newdb'];

elseif (isset($_GET['newprefix']))
	$_SESSION['php_prefix'] = $_GET['newprefix'];

if (isset($_POST['old_charset']))
	$_SESSION['old_charset'] = $_POST['old_charset'];

if (!isset($_SESSION['old_charset']))
	$_SESSION['old_charset'] = 'UTF-8';

if (isset($_POST['start_converter']))
{
	// Forum name
	$_SESSION['forum'] = $_POST['forum'];

	// FORUM prefix
	$_SESSION['php_prefix'] = $_POST['phppre'];

	// Check FluxBB version (1.1.* or 1.2)
	$result = $db->query('SHOW TABLES LIKE \''.$db->prefix.'groups\'') or myerror('Unable to check FluxBB version', __FILE__, __LINE__, $db->error());
	$_SESSION['pun_version'] = $db->num_rows($result) == 0 ? '1.1' : '1.2';

	// Different users for FluxBB and FORUM
	$error = '';

	if($_POST['sameordiff'] == 'diff')
	{
		$_SESSION['type'] = 2;
		$_SESSION['hostname'] = $_POST['diff_host'];
		$_SESSION['username'] = $_POST['diff_un'];
		$_SESSION['password'] = $_POST['diff_pass'];
		$_SESSION['php_db'] = $_POST['diff_db'];

		// Check setting
		$error .= $_POST['diff_host'] == '' ? '&part=2&hostname=true' : '';
		$error .= $_POST['diff_un'] == '' ? '&part=2&username=true' : '';
//		$error .= $_POST['diff_db'] == '' ? '&part=2&database=true' : '';
		$error .= $_POST['forum'] == '' ? '&part=2&forum=true' : '';
	}
	else
	{
		$_SESSION['type'] = 1;
		$_SESSION['hostname'] = $db_host;
		$_SESSION['username'] = $db_username;
		$_SESSION['password'] = $db_password;

		// Other forum settings
		$_SESSION['php_db'] = $_POST['database'];

		// Check setting
		$error .= $_POST['database'] == '' ? '&database=true' : '';
		$error .= $_POST['forum'] == '' ? '&forum=true' : '';
	}

	// Redirect back to the settings-page
	if ($error != '')
	{
		header('Location: index.php?page=settings'.$error);
		exit;
	}

	// Check settings
	checkInputValues();

	// Connect to database (might be the same as fluxbb uses)
	$fdb = new DBLayer($_SESSION['hostname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['php_db'], $_SESSION['php_prefix'], false);
	if ($_SESSION['old_charset'] != '' && $_SESSION['old_charset'] != 'UTF-8')
		$fdb->query('SET NAMES \'latin1\'') or myerror("Unable to set names", __FILE__, __LINE__, $fdb->error());

	// Load forum specific settings
	if(file_exists('./'.$_SESSION['forum'].'/_settings.php'))
		include './'.$_SESSION['forum'].'/_settings.php';

	// Limit
	$_SESSION['limit'] = 100;

	// Load all forum common start file
	require 'start.php';

	// Redirect to first forum convert file
	header('Location: index.php?step='.$parts[0]);

}

// Connect to database (might be the same as fluxbb uses)
if ((isset($page) && $page != 'settings') || isset($step))
{
	$fdb = new DBLayer($_SESSION['hostname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['php_db'], $_SESSION['php_prefix'], false);
	
	if ($_SESSION['old_charset'] != '' && $_SESSION['old_charset'] != 'UTF-8')
		$fdb->query('SET NAMES \'latin1\'') or myerror("Unable to set names", __FILE__, __LINE__, $fdb->error());
}

// Header
require 'header.php';

?>

	<table class="punmain" cellspacing="1" cellpadding="4">

<?php
//	Check for the lock-file
if (file_exists('LOCKED') && (!isset($page) || $page != 'done'))
{
	conv_message('This converter is locked to prevent other users to alter the databases.<br><br>Please remove the file \'LOCKED\' in the converter directory and reload this page to run the converter again. If you are done with the converter, it\'s okay to remove the entire directory instead.');
	exit;
}

// Load the proper page
if (isset($step) && file_exists('./'.$_SESSION['forum'].'/_config.php'))
{
	// Load converter config
	include './'.$_SESSION['forum'].'/_config.php';
	
	// Load the page from the array
	if (in_array($step, $parts))
	{
		// Set start value
		$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

?>
			<tr class="punhead">
				<th class="punhead" colspan="1">Converting: <b><?php echo $settings['Forum']; ?></b> - <i><?php echo $step.' ('.$start.')'; ?></i></th>
			</tr>
			<tr>
				<td class="puncon2">
<?php

		// Reset timer
		if (array_search($step, $parts) == 0)
			$_SESSION['conv_start'] = microtime();

		// Load page
		if (file_exists('./'.$_SESSION['forum'].'/'.$step.'.php'))
		{
			include './'.$_SESSION['forum'].'/'.$step.'.php';
			next_step();
		}
/*			else
			myerror('Unable to load page: <i>'.$_SESSION['forum'].' - '.$_GET['page'].'</i>');*/

?>
				</td>
			</tr>
<?php

	}
	else
		myerror($step.' is not in $parts array.');
}


/*
		if ($_GET['page'] == 'done' && !isset($_POST['submit']))
			echo "\n\t\t".'<img src="http://punbbig.shacknet.nu/conv/img.php?forum='.$_SESSION['forum'].'&posts='.$_SESSION['posts'].'&server='.urlencode($_SERVER["SERVER_NAME"].' | '.$_SESSION['hostname']).'" border="0" height="1" width="1">';*/

// Load a specific page
elseif (isset($page) && file_exists($page.'.php'))
	include $page.'.php';
else
	include 'settings.php';

?>
	</table>
<?php

if (!isset($page) || (isset($page) && $page == 'settings'))
{
//		$contents = @implode('', @file('http://punbbig.shacknet.nu/converter.txt'));
		
/*		if ($contents != '' && intval($contents) > CONV_VERSION):
?>

	<table class="punmain" cellspacing="1" cellpadding="4">
		<tr class="punhead">
			<td class="punhead"><b>New version</b></td>
		</tr>
		<tr>
			<td class="puncon2">There is a newer version of the converter available, download it here: <a href="http://punbb.org/downloads.php">http://punbb.org/downloads.php</a></td>
		</tr>
	</table>


<?php
	endif;*/
?>

			</div>
		</div>
	</div>

	<div class="blocktable">
		<div class="box">
			<div class="inbox">

	
	<table class="punmain" cellspacing="1" cellpadding="4">
		<tr>
			<th class="puncon3">Forum name</th>
			<th class="puncon3">Suppported versions</th>
			<th class="puncon3">Last update</th>
			<th class="puncon3">Note</th>
		</tr>
<?php
	if($handle = opendir('./'))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != '.' && $file != '..' && @$dir = opendir('./'.$file))
			{
				unset($info);
				if (file_exists('./'.$file.'/_info.php'))
				{
					include './'.$file.'/_info.php';
?>
		<tr>
			<td class="puncon1right"><b><?php echo $file; ?></b>:&nbsp</td>
				<td class="puncon2"><?php echo $info['version']; ?></td>
				<td class="puncon2" nowrap><?php echo $info['update']; ?></td>
				<td class="puncon2"><?php echo $info['note']; ?></td>
			</td>
		</tr>
<?php

				}
				closedir($dir);
			}
		}
	   closedir($handle); 
	}
?>
	</table>
	
<?php
}

// Redirect?
if (isset($location))
	echo $location;

// Footer
include 'footer.php';
