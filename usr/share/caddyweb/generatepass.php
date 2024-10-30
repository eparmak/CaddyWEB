<?php
	include 'config.php';
	include 'encryption.class.php';
	$encryption = new Encryption($encryptionKey);
	echo 'encrypted password for ' . $argv[1] . ' is ' . $encryption->encrypt($argv[1]) . "\n";
?>