<?php 
session_start();
include 'config.php'; 
include 'encryption.class.php';
$encryption = new Encryption($encryptionKey);

if ( $_POST['action'] == 'checkLogin' ) {
	if ( $_POST['username'] == $loginUsername AND $_POST['password'] == $encryption->decrypt($loginPassword) ) {
		$_SESSION['logged'] = 1;
		$_SESSION['lastactivity'] = time();
		header("Location: domains.php");
	}
	else {
		echo 'Wrong Username Or Password';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 100%; max-width: 400px; background-color: #333; color: #fff;">
        <h2 class="text-center mb-4" style="color: #f5ba13;">Login</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
			<input type='hidden' name='action' value='checkLogin'>
            <button type="submit" class="btn btn-warning w-100 mt-3">Login</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
