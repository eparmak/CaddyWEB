<?php
session_start();
require_once 'certs.class.php';
require_once 'config.php';
$certfiles = glob($certsdir . '*.crt');

function isProcessRunning($processName) {
  
    exec("ps aux", $output);


    foreach ($output as $line) {
        if (strpos($line, $processName) !== false) {
            return true; 
        }
    }

    return false; 
}

$session_duration = time() - $_SESSION['lastactivity'];
if ($session_duration > $timeout_duration) {
	session_unset();
	session_destroy();
	header("Location: index.php");
}
if ( $_SESSION['logged'] != 1 ) header("Location: index.php");

else $_SESSION['lastactivity'] = time();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caddy Configuration Management</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function confirmDelete(certfile) {
            if (confirm("Are you sure you want to delete " + certfile + "?")) {
               
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process.php';

                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'deletecert';
                form.appendChild(actionInput);

                var certInput = document.createElement('input');
                certInput.type = 'hidden';
                certInput.name = 'cert';
                certInput.value = certfile;
                form.appendChild(certInput);

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
			<a href="uploadcert.php" class="green-btn" style="float: right;">UPLOAD</a>
        </div>

        <table>
            <thead>
                <tr>
					<th>CRT</th>
					<th>KEY</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>Locality</th>
                    <th>Organization</th>
                    <th>Unit</th>
                    <th>Common Name</th>
                    <th>Subject Alternative Name</th>
                    <th>KEY Size</th>
					<th>Valid From</th>
                    <th>Expires</th>
					<th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
					foreach ($certfiles as $certfile) {
						$fileinfo = pathinfo($certfile);
						$keyfile = $certsdir . pathinfo($certfile)['filename'] . '.key';
						$certContent = file_get_contents($certfile);
						$certData = openssl_x509_parse($certContent);
						$publicKey = openssl_pkey_get_public($certContent);
						$keyDetails = openssl_pkey_get_details($publicKey);

				?>
						
                    <tr>
						<td><?= basename($certfile); ?></td>
                        <td><?= file_exists($keyfile) ? basename($keyfile) : 'N/A'; ?></td>
                        <td><?= $certData['subject']['C'] ?></td>
                        <td><?= $certData['subject']['ST'] ?></td>
                        <td><?= $certData['subject']['L'] ?></td>
                        <td><?= $certData['subject']['O'] ?></td>
                        <td><?= $certData['subject']['OU'] ?></td>
                        <td><?= $certData['subject']['CN'] ?></td>
                        <td><?= str_replace(',','<br/>',$certData['extensions']['subjectAltName']); ?></td>
						<td><?= $keyDetails['bits'] ?></td>
						<?php 
							$dValidFrom = $certData['validFrom'];
							$validFrom = DateTime::createFromFormat('ymdHis', substr($dValidFrom, 0, -1));
							$validFrom = $validFrom->format('Y-m-d H:i:s');
							
							$dValidTo = $certData['validTo'];
							$validTo = DateTime::createFromFormat('ymdHis', substr($dValidTo, 0, -1));
							$validTo = $validTo->format('Y-m-d H:i:s');
						?>
						<td><?= $validFrom; ?></td>
						<td><?= $validTo; ?></td>
                        <td>
                            <button class="delete-btn" onclick="confirmDelete('<?= $certfile; ?>')">Delete</button>
                        </td>
                    </tr>
					<?php  } ?>
            </tbody>
        </table>
    </div>
</body>
</html>