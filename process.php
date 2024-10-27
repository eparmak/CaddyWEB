<?php
include 'config.php';
include 'config.class.php'; // Include the class file
print_r($_POST);
if ( isset($_GET['action']) ) {
	$action = $_GET['action'];
	
	if ( $action === 'reloadcaddy' ) {
		$ret = shell_exec('/usr/bin/caddy reload --config=' . $basecaddyconfig);
		header("Location: /domains.php?result=reloadsuccess");
	}
}
// Check if the action is set
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Handle the delete action
    if ($action === 'delete' && isset($_POST['domain'])) {
        $domain = $_POST['domain'];
        // Define the path of the Caddyfile configuration file to delete
        $filePath = $domain;
        // Check if the file exists
        if (file_exists($filePath)) {
            // Attempt to delete the file
            if (unlink($filePath)) {
                // Success: Redirect back to the main page with success message
               header('Location: domains.php?status=success&message=Deleted%20successfully');
            } else {
                // Error deleting: Redirect with error message
                header('Location: domains.php?status=error&message=Unable%20to%20delete%20the%20file');
            }
        } else {
            // File does not exist: Redirect with error message
			header('Location: domains.php?status=error&message=File%20does%20not%20exist');
        }
    
    // Handle the create action
    } elseif ($action === 'create') {
        // Get the required fields
        $domain = trim($_POST['domain']);
        $upstreams = array_map('trim', explode(',', $_POST['upstreams']));
        
        // Checkboxes: Explicitly set them to false if not set in POST
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


        // Redirect back to the main page with success message
		echo '<br/><br/><br/><br/>';
        header('Location: domains.php?status=success&message=Configuration%20created%20successfully');
    } else {
        // Unknown action: Redirect with error message
        header('Location: domains.php?status=error&message=Unknown%20action');
    }
} else {
    // Missing parameters: Redirect with error message
    header('Location: domains.php?status=error&message=Missing%20parameters');
}
exit;
