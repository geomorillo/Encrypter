# Encrypter
Encrypt using openssl for nova framework

**Installation**

Copy the Encrypter.php and Key.php to addons folder

Usage:
* Setup in your Config.php the variable ENCRYPT_KEY with a 32 bits key
* The Key helper will help you generate a new key so you can save it to a file

Example:

```
    public function enc() {
        $encrypted = Encrypter::encrypt("okokokook");
        echo $encrypted;
        echo "<br>";
        echo(Encrypter::decrypt($encrypted));
    }
```

**Note:  With this example you generate a new key everytime,
Save it to a file so you can load it and pass it to the Encrypter to be able to 
encrypt or decrypt, if you lose your key you lose your data**