<?PHP

/*
 * Copyright:   Copyright (c) Akamai Conference 2006<p>
 * Company:     Akamai<p>
 *
 * Type D token type.  Double MD5 of user data salted with password.
 *
 * $Id$
*/
class TypeDToken extends StreamToken {

    /*
     * Ctor taking token string.

    function TypeDToken($token) {
        $this->parseToken($token, "d");
    }
    */

    /*
     * Ctor for token type.
     */
    function TypeDToken($userPath,
               $userIP,
               $userProfile,
               $userPasswd,
               $userTime,
               $userWindow,
               $userDuration,
               $userPayload)
    {
       $durBuf = "";

       // Call parent ctor
       parent::StreamToken();

       // Set member functions
       // Note: removed deep copy constuct from java
       // code as it is redundant here.  Java code used:
       // path = (userPath == null) ? null : new String(userPath);
       $this->tokenType = "d";
       $this->path = $userPath;
       $this->ip = $userIP;
       $this->profile = $userProfile;
       $this->payload = $userPayload;
       $this->window = $userWindow;
       $this->tokenTime = $userTime;
       $this->password = $userPasswd;
       $this->duration = $userDuration;

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

       // convert from hex string to akamai 64 encoding.
       $digest64 = $this->encodeMd5($md5Digested);

       $this->setFlagBits();
       $this->token = $this->buildToken($this->tokenType,
                      $this->flags, $digest64,
                      $timeBuf, $this->profile,
                      $this->payload, $durBuf);
    }


}

?>
