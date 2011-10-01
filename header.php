<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo (isset($_SESSION['old_charset']) && !empty($_SESSION['old_charset']) && isset($_GET['page']) && $_GET['page'] != 'done' && is_numeric($_GET['page']) ? $_SESSION['old_charset'] : 'utf-8') ?>">
		<title>Converter - Done!</title>
		<link rel="stylesheet" type="text/css" href="<?php echo (file_exists(PUN_ROOT.'style/Air.css')) ? PUN_ROOT.'style/Air.css' : 'Sulfur.css' ?>">
		<style>
			.red { font: 10px Verdana, Arial, Helvetica, sans-serif; color: #FF0000; }
			.pun .blocktable table {table-layout: auto}
			.pun {width: 70%; margin: auto;}
		</style>
		<script type="text/javascript">
			function duffusers(type){	
<?php
	// Check for MSIE, beqause IE don't understand "table-row"
	$type = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) ? 'block' : 'table-row';
?>

				if(type == 'same')
				{
					document.getElementById('diff1').style.display = 'none';
					document.getElementById('diff2').style.display = 'none';
					document.getElementById('diff3').style.display = 'none';
					document.getElementById('diff4').style.display = 'none';
					document.getElementById('same').style.display = '<?php echo $type; ?>';
				}
				
				else
				{
					document.getElementById('same').style.display = 'none';
					document.getElementById('diff1').style.display = '<?php echo $type; ?>';
					document.getElementById('diff2').style.display = '<?php echo $type; ?>';
					document.getElementById('diff3').style.display = '<?php echo $type; ?>';
					document.getElementById('diff4').style.display = '<?php echo $type; ?>';
				}

			}
		</script>
	</head>
	<body>

<div class="pun">
	<div class="punwrap">
		<div class="blocktable">
			<div class="box">
				<div class="inbox">
