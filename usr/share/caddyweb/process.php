<?php
session_start();
include 'config.php';
include 'config.class.php'; 
$session_duration = time() - $_SESSION['lastactivity'];
if ($session_duration > $timeout_duration) {
	session_unset();
	session_destroy();
	header("Location: index.php");
}
if ( $_SESSION['logged'] != 1 ) header("Location: index.php");

else $_SESSION['lastactivity'] = time();
if ( isset($_GET['action']) ) {
	$action = $_GET['action'];
	
	if ( $action === 'reloadcaddy' ) {
		$ret = shell_exec('/usr/bin/caddy reload --config=' . $basecaddyconfig);
		header("Location: /domains.php?result=reloadsuccess");
	}
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete' && isset($_POST['domain'])) {
        $domain = $_POST['domain'];
        $filePath = $domain;

        if (file_exists($filePath)) {

            if (unlink($filePath)) {
               header('Location: domains.php?status=success&message=Deleted%20successfully');
            } else {
                header('Location: domains.php?status=error&message=Unable%20to%20delete%20the%20file');
            }
        } else {
			header('Location: domains.php?status=error&message=File%20does%20not%20exist');
        }
    } elseif ($action === 'create') {
        $domain = trim($_POST['domain']);
        $upstreams = array_map('trim', explode(',', $_POST['upstreams']));
		$servername = $_POST['servername'];
        $httpOnly = isset($_POST['httpOnly']) ? true : false;
        $loadBalance = isset($_POST['loadBalance']) ? true : false;
		$customcert = isset($_POST['customCert']) ? true : false;
		$allowedips = isset($_POST['allowedips']) ? true : false;
		$certfile = $_POST['cert'];
        $loadBalanceStrategy = $loadBalance ? ($_POST['loadBalancingMethod'] ?? '') : ''; // Only set if load balance is true
        $failover = isset($_POST['failover']) ? true : false;
        $failDuration = $_POST['failDuration'] ?? $failover_failduration; // Default to empty if not set
        $maxFails = $_POST['maxFails'] ?? $failover_maxfails; // Default to 1 if not set
        $insecureSkipVerify = isset($_POST['insecureSkipVerify']) ? true : false;
        $letsEncrypt = isset($_POST['letsEncrypt']) ? true : false;
		$letsEncryptmail = $letsencryptmail;
		$httpOnly = isset($_POST['httpOnly']) ? true : false;
		$ipaddr = explode(',',$_POST['ipaddr']);
        // Initialize the CaddyfileWriter
        $caddyWriter = new CaddyfileWriter($confdir . $domain . '.conf');
        // Add the configuration
        $caddyWriter->addConfiguration(
			$servername,
            $domain,
            $upstreams,
            $letsEncrypt,
			$letsEncryptmail,
			$customcert,
			$certfile,
            $insecureSkipVerify,
            [$loadBalance, $loadBalanceStrategy],
            $failDuration,
            $maxFails,
			$httpOnly,
			$allowedips,
			$ipaddr
        );

		echo '<br/><br/><br/><br/>';
		//print_r($_POST);
        header('Location: domains.php?status=success&message=Configuration%20created%20successfully');
    } elseif ( $action = 'deletecert' ) {
		unlink($_POST['cert']);
		unlink(str_replace('.crt','.key',$_POST['cert']));
		header('Location: certs.php?status=deleteSuccess');
	} else {
        header('Location: domains.php?status=error&message=Unknown%20action');
    }
} else {
    header('Location: domains.php?status=error&message=Missing%20parameters');
}
exit;
