<?php 
session_start();
require 'config.php'; 
$certfiles = glob($certsdir . '*.crt');

$session_duration = time() - $_SESSION['lastactivity'];
if ($session_duration > $timeout_duration) {
	session_unset();
	session_destroy();
	header("Location: index.php");
}
print_r($_POST);
if ( $_SESSION['logged'] != 1 ) header("Location: index.php");

else $_SESSION['lastactivity'] = time();
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
        <h1>Edit Caddy Configuration</h1>
        <form action="process.php" method="POST">
            <label for="servername">Server Name:</label>
            <input type="text" id="servername" name="servername" value="<?= $_POST['filename']; ?>" required>
            <div style="margin-bottom: 20px;"></div>
			
            <label for="domain">Domains (comma-seperated):</label>
            <input type="text" id="domain" name="domain" value="<?= str_replace('http://','',str_replace(' ',',',$_POST['domain'])); ?>" required>
            <div style="margin-bottom: 20px;"></div>

            <label for="upstreams">Upstreams (comma-separated):</label>
            <input type="text" id="upstreams" name="upstreams" value="<?= $_POST['upstreams']; ?>" required>
            <div style="margin-bottom: 20px;"></div>

            <div class="checkbox-container">
                <input type="checkbox" id="allowedips" name="allowedips" value="1">
                <label for="allowedips">Allowed IPs</label>
            </div>
			
			<div id="containerallowedips" style="display: none;">
				<label for="ipaddr">Allowed IPs (Comma Seperated):</label><br/>
				<input type="text" id="ipaddr" name="ipaddr" value="" min="1" style="width: 500px;" >
			</div>

            <div class="checkbox-container">
                <input type="checkbox" id="httpOnly" name="httpOnly" <?php if ( $_POST['httpOnly']) echo 'checked'; ?>>
                <label for="httpOnly">HTTP Only</label>
            </div>
			<div style="margin-bottom: 20px;"></div>


            <div class="checkbox-container">
                <input type="checkbox" id="letsEncrypt" name="letsEncrypt" <?php if ( $_POST['letsEncrypt']) echo 'checked'; ?>>
                <label for="letsEncrypt">Let's Encrypt</label>
            </div>
            <div style="margin-bottom: 20px;"></div>

            <div class="checkbox-container">
                <input type="checkbox" id="insecureSkipVerify" name="insecureSkipVerify" <?php if ( $_POST['tlsInsecureVerify']) echo 'checked'; ?>>
                <label for="insecureSkipVerify">TLS Insecure Skip Verify</label>
            </div>
			
						<div class="checkbox-container">
                <input type="checkbox" id="customCert" name="customCert" <?php if ( $_POST['customCert'] ) echo 'checked'; ?>>
                <label for="customCert">Use Custom Certificate</label>
            </div>
			<div id="choosecert" style="display: none">
				<label for="cert" style="margin-right: 10px">Choose Certificate</label>
				<select id="cert" name="cert">
					<?php foreach ( $certfiles as $certfile ) {
						if ( basename($certfile) == basename($_POST['customCertCrt']) ) echo "<option value='$certfile' selected>" . basename($certfile) . "</option>";
						else echo "<option value='$certfile'>" . basename($certfile) . "</option>";
					} ?>
				</select>			
			</div>
			
            <div style="margin-bottom: 20px;"></div>
			
            <div class="checkbox-container">
                <input type="checkbox" id="loadBalance" name="loadBalance" <?php if ( $_POST['loadbalance']) echo 'checked'; ?>>
                <label for="loadBalance">Load Balance</label>
            </div>
			
            <div style="margin-bottom: 20px;"></div>
			<div id="lb_methodbox" style="display: none">
            <label for="loadBalancingMethod">Load Balancing Method:</label>
            <select id="loadBalancingMethod" name="loadBalancingMethod">
				<option value="first" <?php if ( $_POST['loadbalancemethod'] == 'first' ) echo 'selected'; ?> >First</option>
                <option value="round_robin" <?php if ( $_POST['loadbalancemethod'] == 'round_robin' ) echo 'selected'; ?>>Round Robin</option>
                <option value="random" <?php if ( $_POST['loadbalancemethod'] == 'random' ) echo 'selected'; ?>>Random</option>
				<option value="ip_hash" <?php if ( $_POST['loadbalancemethod'] == 'ip_hash' ) echo 'selected'; ?>>IP Hash</option>
				<option value="uri_hash" <?php if ( $_POST['loadbalancemethod'] == 'uri_hash' ) echo 'selected'; ?>>URI Hash</option>				
                <option value="least_conn" <?php if ( $_POST['loadbalancemethod'] == 'least_conn' ) echo 'selected'; ?>>Least Connections</option>
            </select></div>
            <div style="margin-bottom: 20px;"></div>

            <label for="failDuration">Fail Duration:</label>
            <input type="text" id="failDuration" name="failDuration" value="<?= $failover_failduration; ?>">
            <div style="margin-bottom: 20px;"></div>

            <label for="maxFails">Max Fails:</label>
            <input type="number" id="maxFails" name="maxFails" value="<?= $failover_maxfails; ?>" min="1">
            <div style="margin-bottom: 20px;"></div>
			<input type="hidden" name="action" value="create">
            <button type="submit">Save Configuration</button>
            <div style="margin-bottom: 20px;"></div>
            <button type="button" class="cancel-btn" onclick="window.location.href='domains.php'">Cancel</button>
        </form>
	<script language='javascript'>
		document.getElementById('httpOnly').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('letsEncrypt').checked = false;
				document.getElementById('customCert').checked = false;
				document.getElementById('choosecert').style.display = 'none';
			}
		});
		document.getElementById('letsEncrypt').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('httpOnly').checked = false;
				document.getElementById('customCert').checked = false;
				document.getElementById('choosecert').style.display = 'none';
			}
		});
		document.getElementById('loadBalance').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('lb_methodbox').style.display = 'block';
			}
			else {
				document.getElementById('lb_methodbox').style.display = 'none';
			}
		});
		document.getElementById('allowedips').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('containerallowedips').style.display = 'block';
			}
			else {
				document.getElementById('containerallowedips').style.display = 'none';
			}
		});
		document.getElementById('customCert').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('choosecert').style.display = 'flex';
				document.getElementById('httpOnly').checked = false;
				document.getElementById('letsEncrypt').checked = false;
			}
			else {
				document.getElementById('choosecert').style.display = 'none';
				document.getElementById('httpOnly').checked = false;
				document.getElementById('letsEncrypt').checked = false;
			}
		});
		
		function doFormActions() {
			if ( document.getElementById('customCert').checked ) {
				document.getElementById('choosecert').style.display = 'flex';
				document.getElementById('httpOnly').checked = false;
				document.getElementById('letsEncrypt').checked = false;

			}
			
			if (document.getElementById('loadBalance').checked) {
				document.getElementById('lb_methodbox').style.display = 'block';
			}
		}
		
		doFormActions();
	</script>
    </div>
</body>
</html>
