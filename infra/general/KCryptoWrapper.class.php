<?php

class KCryptoWrapper
{
        public static function getEncryptorClassName()
        {
            if (extension_loaded('mcrypt'))
            {
                return 'McryptWrapper';
            }
            else
            {
                return 'OpenSSLWrapper';
            }
        }

        public static function __callStatic($func, $args)
        {
                $encryptorClass = self::getEncryptorClassName();
                return call_user_func_array(array($encryptorClass, $func), $args);
        }

        public static function getEncryptor()
        {
                $encryptorClass = self::getEncryptorClassName();
                return new $encryptorClass();
        }

}

class OpenSSLWrapper
{
    const AES_BLOCK_SIZE = 16;
    const DES3_BLOCK_SIZE = 8;
    const DES3_METHOD = 'DES-EDE3';

	protected static function get_aes_method($key)
	{
		switch (strlen($key))
		{
		case 32:
			return 'AES-256-CBC';

		case 24:
			return 'AES-192-CBC';

		default:
			return 'AES-128-CBC';
		}
    }

    public static function random_pseudo_bytes($length)
    {
	    return openssl_random_pseudo_bytes($length);
    }

    public static function encrypt_3des($str, $key)
    {
	    if (strlen($str) % self::DES3_BLOCK_SIZE) {
		$padLength = self::DES3_BLOCK_SIZE - strlen($str) % self::DES3_BLOCK_SIZE;
		$str .= str_repeat("\0", $padLength);
	    }
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
	    //If null was passed as str return empty string
	    if(is_null($str))
	    {
		    return '';
	    }

	    // Pad with null byte to be compatible with mcrypt PKCS#5 padding
	    // See http://thefsb.tumblr.com/post/110749271235/using-opensslendecrypt-in-php-instead-of as 
	    if (strlen($str) % self::AES_BLOCK_SIZE) {
		$padLength = self::AES_BLOCK_SIZE - strlen($str) % self::AES_BLOCK_SIZE;
		$str .= str_repeat("\0", $padLength);
	    }
	    return openssl_encrypt(
		    $str,
		    self::get_aes_method($key),
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
		    $iv
	    );  
    }
    
    public static function decrypt_aes($str, $key, $iv)
    {
	    return openssl_decrypt(
		    $str,
		    self::get_aes_method($key),
		    $key,
		    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
		    $iv
	    );
    }



}

class McryptWrapper
{
    public static function random_pseudo_bytes($length)
    {
	    return mcrypt_create_iv($length,MCRYPT_DEV_URANDOM);
    }

    public static function encrypt_3des($str, $key)
    {
	    $td = mcrypt_module_open('tripledes', '', 'ecb', ''); 
	    $key = substr($key, 0, mcrypt_enc_get_key_size($td));

	    mcrypt_generic_init($td, $key, str_repeat("\0", 8));
	    $encrypted_data = mcrypt_generic($td, $str);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return $encrypted_data;
    }

    public static function decrypt_3des($str, $key)
    {
	    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    $key = substr($key, 0, mcrypt_enc_get_key_size($td));

	    mcrypt_generic_init($td, $key, str_repeat("\0", 8));
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
