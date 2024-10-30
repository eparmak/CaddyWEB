<?php
class UserAuth {
    private $db;
    private $encryptor; // Add an instance of the Encryption class

    // Constructor to initialize the Database object and Encryption class
    public function __construct(Database $db, Encryption $encryptor) {
        $this->db = $db;
        $this->encryptor = $encryptor; // Initialize the encryptor
    }

    // Method to check user login credentials
    public function login($username, $password) {
        // Prepare SQL query to fetch the user by username
        $sql = "SELECT * FROM users WHERE username = :username";

        // Check if the username is not empty
        if (empty($username)) {
            throw new Exception("Username cannot be empty");
        }

        $stmt = $this->db->query($sql, ['username' => $username]);
        $user = $stmt->fetch();

        // Check if user exists and decrypt the stored password
        if ($user) {
            // Decrypt the stored password
            $decryptedPassword = $this->encryptor->decrypt($user['password']);
            // Verify the decrypted password
            if ($decryptedPassword === $password) {
				$_SESSION['username'] = $username;
				$_SESSION['userid'] = $user['id'];
                $_SESSION['login_time'] = time(); // Store the login timestamp
                $_SESSION['access_level'] = $user['accesslevel']; // Store access level
                return [
                    'status' => true,
                    'message' => 'Login successful',
                    'user' => $user,
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Invalid password',
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'User not found',
            ];
        }
    }
	
	
    public function checkSession() {
        if (isset($_SESSION['login_time'])) {
            // Check if the session is older than 15 minutes (900 seconds)
            if (time() - $_SESSION['login_time'] > 900) {
                // Session has expired
                session_unset(); // Clear the session
                session_destroy(); // Destroy the session
                return [
                    'status' => false,
                    'message' => 'Your session has expired. Please log in again.',
                ];
            } else {
                // Update the login time if session is still active
                $_SESSION['login_time'] = time();
                return [
                    'status' => true,
                ];
            }
        }
        return [
            'status' => false,
        ];
    }
}
?>