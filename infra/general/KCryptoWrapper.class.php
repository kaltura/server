<?php

require_once('/opt/kaltura/app/alpha/config/kConf.php');

class KCryptoWrapper
{
    public static function getEncryptor()
    {
	if (extension_loaded('mcrypt')){
	    return new McryptWrapper();
	}else{
	    return new OpenSSLWrapper();
	}
    }

}

class OpenSSLWrapper
{
    const BLOCK_SIZE = 16;
    const AES_METHOD = 'AES-128-CBC';
    const DES3_METHOD = 'DES-EDE3';

    public static function random_pseudo_bytes($bytes)
    {
	    return openssl_random_pseudo_bytes($bytes);
    }

    public static function encrypt_3des($str, $key)
    {
	    $padLength = self::BLOCK_SIZE - strlen($str) % self::BLOCK_SIZE;
	    $str .= str_repeat(chr("\0"), $padLength);
	    return openssl_encrypt(
		    $str,
		    self::DES3_METHOD,
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
	    );
    }
    public static function decrypt_3des($str, $key)
    {
	    return openssl_decrypt(
		    $str,
		    self::DES3_METHOD,
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
	    );
    }
    public static function encrypt_aes($str, $key, $iv)
    {

	    // Pad with null byte to be compatible with mcrypt PKCS#5 padding
	    // See http://thefsb.tumblr.com/post/110749271235/using-opensslendecrypt-in-php-instead-of as 
	    $padLength = self::BLOCK_SIZE - strlen($str) % self::BLOCK_SIZE;
	    $str .= str_repeat(chr("\0"), $padLength);
	    return openssl_encrypt(
		    $str,
		    self::AES_METHOD,
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
		    $iv
	    );  
    }
    
    public static function decrypt_aes($str, $key, $iv)
    {
	    return openssl_decrypt(
		    $str,
		    self::AES_METHOD,
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
		    $iv
	    );
    }



}

class McryptWrapper
{
    public static function random_pseudo_bytes($bytes)
    {
	    return mcrypt_create_iv($bytes,MCRYPT_DEV_URANDOM);
    }

    public static function encrypt_3des($str, $key)
    {
	    $td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
	    $key = substr($key, 0, mcrypt_enc_get_key_size($td));

	    mcrypt_generic_init($td, $key, null);
	    $encrypted_data = mcrypt_generic($td, $str);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return $encrypted_data;
    }

    public static function decrypt_3des($str, $key)
    {
	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    $key = substr($key, 0, mcrypt_enc_get_key_size($td));

	    mcrypt_generic_init($td, $key, null);
	    $decrypted_data = mdecrypt_generic($td, $str);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return $decrypted_data;
    }

    public static function encrypt_aes($str, $key, $iv)
    {
	    return mcrypt_encrypt(
		    MCRYPT_RIJNDAEL_128,
		    $key,
		    $str,
		    MCRYPT_MODE_CBC,
		    $iv 
	    ); 
    }

    public static function decrypt_aes($str, $key, $iv)
    {
	    return mcrypt_decrypt(
		    MCRYPT_RIJNDAEL_128,
		    $key,
		    $str,
		    MCRYPT_MODE_CBC,
		    $iv	
	    );

    }
}
