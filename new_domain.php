<?php 
require 'config.php'; 
$certfiles = glob($certsdir . '*.crt');
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
        <form action="process.php" method="POST">
            <label for="servername">Server Name:</label>
            <input type="text" id="servername" name="servername" required>
            <div style="margin-bottom: 20px;"></div>
			
            <label for="domain">Domains (comma-seperated):</label>
            <input type="text" id="domain" name="domain" required>
            <div style="margin-bottom: 20px;"></div>

            <label for="upstreams">Upstreams (comma-separated):</label>
            <input type="text" id="upstreams" name="upstreams" required>
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
                <input type="checkbox" id="httpOnly" name="httpOnly" value="1">
                <label for="httpOnly">HTTP Only</label>
            </div>
			<div style="margin-bottom: 20px;"></div>


            <div class="checkbox-container">
                <input type="checkbox" id="letsEncrypt" name="letsEncrypt" value="1">
                <label for="letsEncrypt">Let's Encrypt</label>
            </div>
            <div style="margin-bottom: 20px;"></div>

            <div class="checkbox-container">
                <input type="checkbox" id="insecureSkipVerify" name="insecureSkipVerify" value="1">
                <label for="insecureSkipVerify">TLS Insecure Skip Verify</label>
            </div>

			<div style="margin-bottom: 20px;"></div>
			<div class="checkbox-container">
                <input type="checkbox" id="customCert" name="customCert" value="1">
                <label for="customCert">Use Custom Certificate</label>
            </div>
			<div id="choosecert" style="display: none">
				<label for="cert" style="margin-right: 10px">Choose Certificate</label>
				<select id="cert" name="cert">
					<?php foreach ( $certfiles as $certfile ) { ?>
					<option value="<?= $certfile ?>"><?= basename($certfile); ?></option>
					<?php } ?>
				</select>			
			</div>
			
			<div style="margin-bottom: 40px;"></div>
            <div class="checkbox-container">
                <input type="checkbox" id="loadBalance" name="loadBalance" value="1">
                <label for="loadBalance">Load Balance</label>
            </div>

			<div id="lb_methodbox" style="display: none">
            <label for="loadBalancingMethod">Load Balancing Method:</label>
            <select id="loadBalancingMethod" name="loadBalancingMethod">
				<option value="first">First</option>
                <option value="round_robin">Round Robin</option>
                <option value="random">Random</option>
				<option value="ip_hash">IP Hash</option>
				<option value="uri_hash">URI Hash</option>				
                <option value="least_conn">Least Connections</option>
            </select>
			</div>

            <label for="failDuration">Fail Duration:</label>
            <input type="text" id="failDuration" name="failDuration" value="<?= $failover_failduration; ?>">
            <div style="margin-bottom: 20px;"></div>

            <label for="maxFails">Max Fails:</label>
            <input type="number" id="maxFails" name="maxFails" value="<?= $failover_maxfails; ?>" min="1">
            <div style="margin-bottom: 20px;"></div>
			<input type="hidden" name="action" value="create">
            <button type="submit">Add Configuration</button>
            <div style="margin-bottom: 20px;"></div>
            <button type="button" class="cancel-btn" onclick="window.location.href='domains.php'">Cancel</button>
        </form>
	<script language='javascript'>
		document.getElementById('httpOnly').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('letsEncrypt').checked = false;
			}
		});
		document.getElementById('letsEncrypt').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('httpOnly').checked = false;
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
		document.getElementById('loadBalance').addEventListener('change', function() {
			if (this.checked) {
				document.getElementById('lb_methodbox').style.display = 'block';
			}
			else {
				document.getElementById('lb_methodbox').style.display = 'none';
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
	</script>
    </div>
</body>
</html>
