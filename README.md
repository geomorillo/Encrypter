# Encrypter
Encrypt using openssl for nova framework

**Installation**

Copy the Encrypter.php and Key.php to addons folder

Usage:

*The Key helper will help you generate a new key so you can save it to a file

Example:

```
    public function enc() {
        $key = Key::generate(); //generate a new key, you can save to file if you want.
        $enc = new Encrypter($key); //pass the key to the Encrypter class
        $encrypted=  $enc->encrypt('Hey bro this is secret'); //some test to encrypt
        echo $encrypted;
        $decrypted = $enc->decrypt($encrypted);
        echo "<br>";
        echo $decrypted;
    }
```

**Note:  With this example you generate a new key everytime,
Save it to a file so you can load it and pass it to the Encrypter to be able to 
encrypt or decrypt, if you lose your key you lose your data**