<?PHP
require 'NIST_CBC.php';
/*
 * Copyright:   Copyright (c) Akamai Conference 2006<p>
 * Company:     Akamai<p>
 *
 * Type D token type.  Double MD5 of user data salted with password.
 *
 * $Id$
*/
class TypeEToken extends StreamToken {

    /*
     * Ctor for token type.
     */
    function TypeEToken($userPath,
               $userIP,
               $userProfile,
               $userPasswd,
               $userTime,
               $userWindow,
               $userDuration,
               $userPayload,
               $userKey)
    {
       $durBuf = "";

       // Call parent ctor
       parent::StreamToken();

       // Set member functions
       // Note: removed deep copy constuct from java
       // code as it is redundant here.  Java code used:
       // path = (userPath == null) ? null : new String(userPath);
       $this->tokenType = "e";
       $this->path = $userPath;
       $this->ip = $userIP;
       $this->profile = $userProfile;
       $this->payload = $userPayload;
       $this->window = $userWindow;
       $this->tokenTime = $userTime;
       $this->password = $userPasswd;
       $this->duration = $userDuration;
       $this->encryptKey = $userKey;

       $timeBuf = $this->fixWindowAndTime($this->window, $this->tokenTime);
       if( 0 != $userDuration ) {
           $durBuf = $this->to64($userDuration, 1);
       } else {
           $durBuf = NULL;
       }

       $md5Buf = $this->buildBuf($userPath, $userIP, $timeBuf, $userProfile, $userPasswd, $userPayload, $durBuf);
       $md5Digested = md5( $md5Buf );

       // Convert hex codes to characters
       $digest64 = "";
       for ($i=0; $i < strlen($md5Digested); $i+=2) {
           $hexstr = substr($md5Digested, $i, 2);
           $hexval = hexdec($hexstr);
           $digest64 .= chr($hexval);
       }

       // Append the password
       $digest64 .= $userPasswd;

       // MD5 again
       $md5Digested = md5( $digest64 );



       // --- Insert encryption here ---

       // Convert key string to bytes using encode64 transform
       $keyBytes = array();

       if( strlen($userKey) > 32) {
           for ($i=0; $i < strlen($userKey); $i+=2) {
               $hexstr = substr($userKey, $i, 2);
               $keyBytes[$i/2] = $this->from64($hexstr);
           }
       } else {
           for ($i=0; $i < strlen($userKey); $i++) {
               $keyBytes[$i] = ord($userKey[$i]);
           }
       }

       // Java code stores session key in local var, but never uses it.

       $cipher = new NIST_CBC();
       $cipher->init(NULL, $keyBytes);

       // Need 16 random integers.
       srand((double)microtime()*1000000);
       for($i=0;$i<16;$i++) {
            $CBC_IV[$i] = rand(0,255);
            $CBC_IV[$i] = 0xFF;
       }

       // Set up feedback buffer
       $cipher->setIV( $CBC_IV );

       // Convert hex coded digest to bytes.
       $input = array();
       for ($i=0; $i < strlen($md5Digested); $i+=2) {
           $hexstr = substr($md5Digested, $i, 2);
           $hexval = hexdec($hexstr);
           $input[] = $hexval;
       }

       $result = $cipher->doFinal($input, 0, sizeOf($input) );

       // Copy random bytes into first 16 of result
       // Append encrypted result.
       $encrypted = array_merge($CBC_IV,$result);

       // ------------------------------

       $digest64 = "";
       for($i=0;$i<sizeOf($encrypted);$i++) {
           $y = $encrypted[$i] & 0xFF;
           $digest64 .= $this->to64($y,2);
       }

       $this->setFlagBits();
       $this->token = $this->buildToken($this->tokenType,
                      $this->flags, $digest64,
                      $timeBuf, $this->profile,
                     $this->payload, $durBuf);
    }
}
?>
