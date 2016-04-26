<?php

/**
 *  Encrypter for Nova Framework
 *
 * @author Manuel Jhobanny Morillo Ordoñez geomorillo@yahoo.com 
 * 
 */

namespace Helpers; 
class Encrypter {

    //put your code here
    protected $algo;
    protected $iv;

    public function __construct($key, $algo = 'AES-128-CBC') {
        $key = (string) $key;
        if ($this->startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        if ($this->supported($key, $algo)) {
            $this->key = $key;
            $this->algo = $algo;
        } else {
            return FALSE;
        }
    }

    public function startsWith($key, $search) {
        return substr($key, 0, strlen($search)) === $search;
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $algo
     * @return bool
     */
    public function supported($key, $algo) {

        $length = mb_strlen($key, '8bit');
        return ($algo === 'AES-128-CBC' && $length === 16) || ($algo === 'AES-256-CBC' && $length === 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws Exception
     */
    public function encrypt($value) {
        if($this->algo ===FALSE){
            throw new Exception('Not supported algorithm found.');
        }
        $iv = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM); //$this->get_random_bytes($this->getIvSize());
        $value = openssl_encrypt(serialize($value), $this->algo, $this->key, 0, $iv);
       
        if ($value === false) {
            throw new Exception('Could not encrypt the data.');
        }
        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.

        $mac = $this->hash($iv = base64_encode($iv), $value);
        $json = json_encode(compact('iv', 'value', 'mac'));
        if (!is_string($json)) {
            throw new Exception('Could not encrypt the data.');
        }
        return base64_encode($json);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws Exception
     */
    public function decrypt($payload) {
        $payload = $this->getJsonPayload($payload);
        $iv = base64_decode($payload['iv']);
        $decrypted = openssl_decrypt($payload['value'], $this->algo, $this->key, 0, $iv);
        if ($decrypted === false) {
            throw new Exception('Could not decrypt the data.');
        }
        return unserialize($decrypted);
    }

    /**
     * Get the IV size for the cipher.
     *
     * @return int
     */

    /**
     * Get random bytes
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public function get_random_bytes($length) {
        if (empty($length) OR ! ctype_digit((string) $length)) {
            return FALSE;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        return FALSE;
    }

    function hash($iv, $value) {

        return hash_hmac('sha256', $iv . $value, $this->key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws Exception
     */
    protected function getJsonPayload($payload) {
        $payload = json_decode(base64_decode($payload), true);
        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (!$payload || $this->invalidPayload($payload)) {
            throw new Exception('The payload is invalid.');
        }
        if (!$this->validMac($payload)) {
            throw new Exception('The MAC is invalid.');
        }
        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  array|mixed  $data
     * @return bool
     */
    protected function invalidPayload($data) {
        return !is_array($data) || !isset($data['iv']) || !isset($data['value']) || !isset($data['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     *
     * @throws Exception
     */
    protected function validMac(array $payload) {
        $bytes = $this->get_random_bytes(16);
        $calcMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);
        $knowMac = hash_hmac('sha256', $payload['mac'], $bytes, true);
        return hash_equals($knowMac, $calcMac);
    }

}
