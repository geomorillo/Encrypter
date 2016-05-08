<?php

/**
 * Simple helper to generate a key file for Encrypter
 *
 * @author geomorillo
 */
namespace Helpers;
class Key {
    //suported $length 16, 32 the algo should be automatically picked by the encrypter
    public static function generate($length = 16){
        return 'base64:'.base64_encode(createKey($length));
    }
    
}
