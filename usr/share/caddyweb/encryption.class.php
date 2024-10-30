<?php
class Encryption {
    private $cipher = "aes-256-cbc"; // Encryption algorithm
    private $key; // Secret key

    // Constructor to initialize the secret key (32 characters for AES-256)
    public function __construct($key) {
        if (strlen($key) !== 32) {
            throw new Exception("Key must be 32 characters long for AES-256");
        }
        $this->key = $key;
    }

    // Function to encrypt the plaintext
    public function encrypt($plaintext) {
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen); // Generate a secure IV
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $ciphertext); // Encode the IV + ciphertext
    }

    // Function to decrypt the ciphertext
    public function decrypt($ciphertext) {
        $ciphertext = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($ciphertext, 0, $ivlen); // Extract the IV
        $ciphertext = substr($ciphertext, $ivlen); // Extract the ciphertext
        return openssl_decrypt($ciphertext, $this->cipher, $this->key, 0, $iv);
    }
}
?>
