<?php
require_once 'config.class.php';
require_once 'config.php';
session_start();
$version = file_get_contents('version');
echo 'Ver : ' . $version;
$caddyReader = new CaddyfileReader();
$conffiles = glob($confdir . '*');

$session_duration = time() - $_SESSION['lastactivity'];
if ($session_duration > $timeout_duration) {
	session_unset();
	session_destroy();
	header("Location: index.php");
}
if ( $_SESSION['logged'] != 1 ) header("Location: index.php");

else $_SESSION['lastactivity'] = time();

function isProcessRunning($processName) {
    exec("ps aux", $output);

 
    foreach ($output as $line) {
        if (strpos($line, $processName) !== false) {
            return true; // Process is running
        }
    }

    return false; 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caddy Configuration Management</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function confirmDelete(domain) {
            if (confirm("Are you sure you want to delete the configuration for " + domain + "?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process.php';

                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);

                var domainInput = document.createElement('input');
                domainInput.type = 'hidden';
                domainInput.name = 'domain';
                domainInput.value = domain;
                form.appendChild(domainInput);

                document.body.appendChild(form);
                form.submit(); 
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>
			Caddy Configuration Management<br/>
			Caddy Status : <?= isProcessRunning('caddy') ? '<font color="#7FFFD4">Running</font>' : '<font color="#FF0000">Stopped</font>'; ?>
		</h1>
        
 
        <div class="top-bar">
			<a href="domains.php" class="orange-btn" style="margin-left: 10px;">Domain<br/>Manager</a>
			<a href="certs.php" class="orange-btn" style="margin-left: 10px;">Certificate<br/>Manager</a>
			<a href="process.php?action=reloadcaddy" class="blue-btn" style="margin-left: 10px;">Reload<br/>Caddy</a>
			<a href="new_domain.php" class="green-btn" style="float: right;">New Domain</a>


        </div>

        <table>
            <thead>
                <tr>
					<th>Server Name</th>
                    <th>Domains</th>
                    <th>Upstreams</th>
                    <th>HTTP Only</th>
                    <th>Load Balancing</th>
                    <th>Load Balance Strategy</th>
                    <th>Failover</th>
                    <th>Fail Duration</th>
                    <th>Max Fails</th>
                    <th>Insecure Verify</th>
                    <th>Lets Encrypt</th>
					<th>Custom Cert</th>
					<th>Ip Restriction</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
					foreach ($conffiles as $filePath) {
						$configurations = $caddyReader->getConfig($filePath);
							foreach ($configurations as $conf) {
				?>
						
                    <tr>
						<td><?= $conf['filename']; ?></td>
                        <td><?= str_replace('http://','',str_replace(',','<hr>',$conf['domain'])); ?></td>
                        <td><?= is_array($conf['upstreams']) ? implode("<hr>", $conf['upstreams']) : 'N/A' ?></td>
                        <td><?= $conf['httpOnly'] ? 'Yes' : 'No' ?></td>
                        <td><?= $conf['loadbalance'] ? 'Yes' : 'No' ?></td>
                        <td><?= $conf['loadbalancemethod'] ? $conf['loadbalancemethod'] : 'None' ?></td>
                        <td><?= $conf['failover'] ? 'Yes' : 'No' ?></td>
                        <td><?= htmlspecialchars($conf['failDuration'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($conf['maxFails'] ?? 'N/A') ?></td>
                        <td><?= $conf['tlsInsecureVerify'] ? 'Yes' : 'No' ?></td>
                        <td><?= $conf['letsEncrypt'] ? 'Yes' : 'No' ?></td>
						<td><?= $conf['customCert'] ? $conf['customCertCrt'] : 'No' ?></td>
						<td><?= $conf['iprestriction'] ? implode('<br/>',$conf['allowedips']) : 'No' ?></td>
                        <td>
							<form action="edit_domain.php" method="POST">
								<input type="hidden" value="<?= $conf['filename'] ?>" name="filename">
								<input type="hidden" value="<?= $conf['domain'] ?>" name="domain">
								<input type="hidden" value="<?= implode(",", $conf['upstreams']); ?>" name="upstreams">
								<input type="hidden" value="<?= $conf['httpOnly'] ?>" name="httpOnly">
								<input type="hidden" value="<?= $conf['loadbalance'] ?>" name="loadbalance">
								<input type="hidden" value="<?= $conf['loadbalancemethod'] ?>" name="loadbalancemethod">
								<input type="hidden" value="<?= $conf['failover'] ?>" name="failover">
								<input type="hidden" value="<?= $conf['failDuration'] ?>" name="failDuration">
								<input type="hidden" value="<?= $conf['maxFails'] ?>" name="maxFails">
								<input type="hidden" value="<?= $conf['tlsInsecureVerify'] ?>" name="tlsInsecureVerify">
								<input type="hidden" value="<?= $conf['letsEncrypt'] ?>" name="letsEncrypt">
								<button type="submit" class="edit-btn">Edit</button>
							</form>
                            <button class="delete-btn" onclick="confirmDelete('<?= $confdir . $conf['filename'] ?>')">Delete</button>
                        </td>
                    </tr>
					<?php } } ?>
            </tbody>
        </table>
    </div>
</body>
</html>