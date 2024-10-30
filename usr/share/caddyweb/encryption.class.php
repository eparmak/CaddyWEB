<?php
class Encryption {
    private $cipher = "aes-256-cbc"; 
    private $key;

    public function __construct($key) {
        if (strlen($key) !== 32) {
            throw new Exception("Key must be 32 characters long for AES-256");
        }
        $this->key = $key;
    }


    public function encrypt($plaintext) {
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }

    public function decrypt($ciphertext) {
        $ciphertext = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($ciphertext, 0, $ivlen);
        $ciphertext = substr($ciphertext, $ivlen);
        return openssl_decrypt($ciphertext, $this->cipher, $this->key, 0, $iv);
    }
}
?>
