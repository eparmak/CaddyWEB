<?php 
session_start();
require 'config.php'; 

$session_duration = time() - $_SESSION['lastactivity'];
if ($session_duration > $timeout_duration) {
	session_unset();
	session_destroy();
	header("Location: index.php");
}
if ( $_SESSION['logged'] != 1 ) header("Location: index.php");

else $_SESSION['lastactivity'] = time();

if ( $_POST['action'] == 'uploadcert' ) {
	print_r($_FILES['crt']);
	if ( pathinfo($_FILES['crt']['name'], PATHINFO_EXTENSION) != 'crt' AND  pathinfo($_FILES['key']['name'], PATHINFO_EXTENSION) != 'crt' ) {
		echo 'Files is not in valid format, please upload .crt and .key files';
	}
	else {
		$crtfile = $_FILES['crt']['name'];
		$keyfile = pathinfo($_FILES['crt']['name'], PATHINFO_FILENAME);
		echo $certsdir . $keyfile . '.key';
		move_uploaded_file($_FILES['crt']['tmp_name'],$certsdir . $crtfile);
		move_uploaded_file($_FILES['key']['tmp_name'],$certsdir . $keyfile . '.key');
		header("Location: /certs.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Caddy Configuration</title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body>

    <div class="container">
        <h1>Create New Caddy Configuration</h1>
			<form action="" method="POST" enctype="multipart/form-data">
				<label for="crt">CRT File</label>
				<input type="file" id="crt" name="crt" accept=".crt" required>
				<div style="margin-bottom: 20px;"></div>

				<label for="key">Key File</label>
				<input type="file" id="key" name="key" accept=".key" required>
				<input type='hidden' name='action' value='uploadcert'>
				<div style="margin-bottom: 20px;"></div>

				<button type="submit">Upload Certificate</button>
				<div style="margin-bottom: 20px;"></div>

				<button type="button" class="cancel-btn" onclick="window.location.href='certs.php'">Cancel</button>
			</form>
    </div>
</body>
</html>
