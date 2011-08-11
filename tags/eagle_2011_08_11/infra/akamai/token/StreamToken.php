<?php
/*
 * Copyright:   Copyright (c) Akamai Conference 2006<p>
 * Company:     Akamai<p>
 *
 * Base class for all Stream Tokens types.  Provides utility functions
 * for constructing tokens.
 *
 * $Id$
*/
class StreamToken {
    var $codeVersion = "3.0.1";

    var $flags;     // integer
    var $path;      // string
    var $ip;        // string
    var $profile;   // string
    var $password;  // string
    // Was "time" but time is a reserved function.
    var $tokenTime; // integer (long)
    var $window;    // integer (long)
    var $duration;  // integer (long)
    var $payload;   // string
    var $tokenType; // string
    var $encryptKey;// string

    // Really a constant, but wanted value scoped to class.
    var $ENCRYPTED_LEN;

    // Encoding lookup array.
    // Really a constant, but wanted value scoped to class.
    var $choices64;

    // Base for rotation calculations
    // Really a constant, but wanted value scoped to class.
    var $ROT_BASE;

    //  Flag constants.
    var $FLAG_IP;
    var $FLAG_PATH;
    var $FLAG_PROFILE;
    var $FLAG_PASSWD;
    var $FLAG_WINDOW;
    var $FLAG_PAYLOAD;
    var $FLAG_DURATION;

    /*
     * Default class constructor.  Initializes member variables.
     */
    function StreamToken() {
        $this->choices64 = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
                 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
                 'u', 'v', 'w', 'x', 'y', 'z',
                 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
                 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                 'U', 'V', 'W', 'X', 'Y', 'Z',
                 '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                 '.', '/');

         $this->ROT_BASE = 9;
         $this->ENCRYPTED_LEN = 13;

         // Flag constants;
         $this->FLAG_IP       = 1;
         $this->FLAG_PATH     = 2;
         $this->FLAG_PROFILE  = 4;
         $this->FLAG_PASSWD   = 8;
         $this->FLAG_WINDOW   = 16;
         $this->FLAG_PAYLOAD  = 32;
         $this->FLAG_DURATION = 64;
    }

    /*
     * Version number accessor.
     */
    function getVersion() {
        return $this->codeVersion;
    }

    /*
     * Converts integer (32) values to character strings using
     * lookup array.
     */
    function to64($value, $minValue) {
        $val = $value;
        $result = "";

        for(; $val>63; $val = $val / 64) {
            $result .= $this->choices64[$val % 64];
        }

        // Pick up last bit.
        $result .= $this->choices64[$val % 64];

        // Pad result to minimum length
        while( strlen($result) < $minValue ) {
            $result .= $this->choices64[0];
        }

        return strrev($result);
    }

    /*
     * Converts 64 based encoding to integer (32).
     */
    function makeInt($c) {
        if (ord($c) >= 97 && ord($c) <= 122)
           return (ord($c) - 97);
       else if (ord($c) >= 65 && ord($c) <= 90)
           return (ord($c) - 39); // - 65 + 26
       else if (ord($c) >= 48 && ord($c) <= 57)
           return (ord($c) + 4); // - 48 + 52
       else if (ord($c) == 46)
           return 62;
       else
           return 63;
    }

    /*
     * Convert from encoded string to a number
     */
    function from64($buffer) {
        $result = 0;
        for( $i=0;$i < strlen($buffer); $i++ ) {
            $result = $result * 64 + $this->makeInt( $buffer[$i] );
        }

        return $result;
    }

    /*
     * Replace forward slashes in string with underscore.
     */
    function fixSlash($buf) {
        return str_replace('/','_',$buf);
    }

    /*
     * Replace underscores in string with forward slashes.
     */
    function unFixSlash($buf) {
        return str_replace('_','/',$buf);
    }

    /*
     * Scramble string based on rot-13 derived algorithm.
     */
    function obfuscate($token, $trailer, $startIndex, $digestLen) {
        // temp var for token manipulation
        $tempstr = "";
        $e_digits = array();

        /*
         * eIndex - index into e_digits
         * index - index into token
         */
        for($eIndex=0, $index=0; $index<$digestLen; $index++,$eIndex++) {
            $tempstr = substr( $token, $index + $startIndex, 1);
            $val = $this->from64($tempstr);
            $e_digits[$eIndex] = $val % 10;
        }

        // Note: length not set as in java code.
        $obfuscated[0] = $trailer[0];

        for($index=0; $index < strlen($trailer); $index++) {
            $rot_val = $this->ROT_BASE + $e_digits[$index % $digestLen];

            // Get code of character at index
            $ch = ord($trailer[$index]);


            // "rotation" logic
            if ($ch >= 97 && $ch <= 122) {
                $ch = $ch + $rot_val;
                if ($ch > 122) {
                    // extend the rotation into the capitals
                    // 'A' + ch - 'z' - 1
                    $ch = 65 + $ch - 122 - 1;
                }
            } elseif ($ch >= 65 && $ch <= 90) {
                $ch = $ch + $rot_val;
                if ($ch > 90) {
                    // extend the rotation into the digits
                    // '0' + ch - 'Z' - 1;
                    $ch = 48 + $ch - 90 - 1;
                    //  see if we extended past all the digits
                    if ($ch > 57) {
                        //extend the rotation into the lowers
                        // 'a' + (ch - '9' - 1)
                        $ch = 97 + $ch - 57 - 1;
                    }
                }
            }elseif ($ch >= 48 && $ch <= 57) {
                $ch = $ch + $rot_val;
                if ($ch > 57) {
                    // extend the rotation into the lowers
                    // 'a' + ch - '9' - 1;
                    $ch = 97 + $ch - 57 - 1;
                }
            }

            // Assign to obfucated string converting code to character
            $obfuscated[$index] = chr($ch);
        }

        $obfString = NULL;

        // Array to string
        foreach ($obfuscated as $index => $val) {
            $obfString .= $val;
        }

        // Fix slashes
        $obfString = $this->fixSlash($obfString);
        return $obfString;
    }

    /*
     * Construct a text buffer based on user provided information.  Buffer is
     * used as the basis for the token.
     */
    function buildBuf($uPath, $uIP, $uTime, $uProf, $uPwd, $uPay, $durBuf) {
        $retBuf = "";

        if ($uPath != NULL)
            $retBuf .= $uPath;
        if ($uIP != NULL)
            $retBuf .= $uIP;
        if ($uTime != NULL)
            $retBuf .= $uTime;
        if ($uProf != NULL)
            $retBuf .= $uProf;
        if ($uPwd != NULL)
            $retBuf .= $uPwd;
        if ($uPay != NULL)
            $retBuf .= $uPay;
        if ($durBuf != NULL)
            $retBuf .= $durBuf;

        return $retBuf;
    }

    /*
     * Convert time and window to encoded strings.  Used
     * in trailer.
     */
    function fixWindowAndTime($uWindow, $uTime) {
        $result = "";

        if( $uWindow > 0 ) {
            if( $uTime <= 0 ) {
                $this->tokenTime = time();
            }
            $result = $this->to64( $this->tokenTime, 1);
            $result .= '-';
            $result .= $this->to64($uWindow, 1);
        }

        return $this->fixSlash($result);
    }

    /*
       Set token flags (data members) based on which
       token paremeters where used.
    */
    function setFlagBits() {
        $this->flags = 0;

        // Note: NULL == "" so checking for 0 length string is not needed.
        if ($this->ip != NULL) {
            $this->flags = $this->flags | $this->FLAG_IP;
        }
        if ($this->path != NULL) {
            $this->flags = $this->flags | $this->FLAG_PATH;
        }
        if ($this->profile != NULL) {
            $this->flags = $this->flags | $this->FLAG_PROFILE;
        }
        if ($this->password != NULL) {
            $this->flags = $this->flags | $this->FLAG_PASSWD;
        }
        if ($this->window > 0) {
            $this->flags = $this->flags | $this->FLAG_WINDOW;
        }
        if ($this->payload != NULL) {
            $this->flags = $this->flags | $this->FLAG_PAYLOAD;
        }
        if ($this->duration > 0) {
            $this->flags = $this->flags | $this->FLAG_DURATION;
        }
    }

    /*
     * Create trailer for token.
     */
    function makeTrailer($token, $profile, $payload)  {
         $trailer = "";

         if( $profile == NULL && $payload == NULL ) {
             return $trailer;
         }

         if( $profile != NULL ) {
             $trailer .= '-';
             $trailer .= $profile;
         }

         if( $this->tokenType == "a" ) {
             if( $payload != NULL ) {
                 $trailer .= '-';
                 $trailer .= $payload;
             }
             $obsBuf = $this->obfuscate($token, $trailer, 2, 13);
         } else {
             if( $payload != NULL ) {
                 $trailer .= '-';
                 for($i=0;$i<strlen($payload);$i++) {
                     $ch = ord( $payload[$i] ) & 0xff;
                     $trailer .= $this->to64($ch,2);
                 }
             }
             $obsBuf = $this->obfuscate($token,$trailer,3,32);
         }
         $obsBuf = $this->fixSlash($obsBuf);
         return $obsBuf;
    }

    /*
     * Build token from buffers.  Note, newFlags is not used.
     */
    function buildToken($tType, $newFlags, $digested, $timeBuf, $prof, $payl, $durBuf) {
        $newToken = $tType;
        $newToken .= $this->to64($this->flags, 2);
        $newToken .= $digested;

        if( $tType != "a")
            $newToken .= '-';

        $newToken .= $timeBuf;
        if( NULL != $durBuf ) {
            $newToken .= '-';
            $newToken .= $durBuf;
        }
        $newToken = $this->fixSlash($newToken);
        $newToken .= $this->makeTrailer( $newToken, $prof, $payl);
        $newToken = $this->fixSlash($newToken);

        return $newToken;
    }

    // Token accessor.
    function getToken() {
        return $this->token;
    }

    /*
     * Takes the MD5 digest from PHP and encodes it to
     * be compatible with Java generated token.
     */
    function encodeMd5($md5Digested) {
        $digest64 = "";
        for ($i=0; $i < strlen($md5Digested); $i+=2) {
           $hexstr = substr($md5Digested, $i, 2);
           $hexval = hexdec($hexstr);
           $digest64 .= $this->to64($hexval,2);
        }

        return $digest64;
    }

    /*
     * Reverse obfuscation.
     */
     function deobfuscate($trailer, $enc_string, $digestLen) {
         if( strlen($trailer) == 0 ) {
             return "";
         }

         // Build an array of digits based upon the chars in the token
        for ($index = 0; $index < $digestLen; $index++) {
            $tempstr = substr($enc_string, $index, 1);
            $int_val = $this->from64($tempstr);
            $e_digits[$index] = $int_val % 10;
        }

        $result = "";

        for ($index = 0; $index < strlen($trailer); $index++) {
            $rot_val = $this->ROT_BASE + $e_digits[$index % $digestLen];

            // Get code of character at index
            $ch = ord($trailer[$index]);

            if ($ch >= 97 && $ch <= 122) {
                $ch = $ch - $rot_val;

                if ($ch < 97) {
                    // extend the rotation into the digits
                    // '9' - ('a' - ch - 1)
                    $ch = 57 - (97 - $ch - 1);
                    // see if we extended past all of the digits
                    if ($ch < 48) {
                        // extend into the capitals
                        // 'Z' - ('0' - ch - 1)
                        $ch = 90 - (48 - $ch - 1);
                    }
                }
            } elseif ($ch >= 65 && $ch <= 90) {
                $ch = $ch - $rot_val;
                if ($ch < 65) {
                    // extend the rotation into the lower case
                    $ch = 122 - (65 - $ch - 1);
                }
            } elseif ($ch >= 48 && $ch <= 57) {
                $ch = $ch - $rot_val;
                if ($ch < 48) {
                    // extend the rotation into the capitals
                    $ch = 90 - (48 - $ch - 1);
                }
            }

            $result .= chr($ch);
        }

        // Note: code does not unfix slashes!

        return $result;
     }

     /*
      * Parse trailer into member variables.
      */
     function parseTrailer($trailer, $enc_string) {
         $pos1 = 0;
         $pos2 = 0;
         $pos3 = 0;

         $has_profile = false;
         $real_trailer = "";

         if (($this->flags & $this->FLAG_WINDOW) == $this->FLAG_WINDOW) {
            $pos1 = strpos($trailer,'-');
            $pos2 = strpos($trailer,'-', $pos1 + 1);
            $this->tokenTime = $this->from64( substr($trailer, 0, $pos1) );
            if ($pos2 > 0) {
                $this->window = $this->from64(substr($trailer,$pos1 + 1, $pos2-$pos1-1));
                $trailer = substr($trailer, $pos2);
            } else {
                $this->window = from64( substr($trailer, $pos1 + 1));
                $trailer = "";
           }
         }

         if (($this->flags & $this->FLAG_DURATION) == $this->FLAG_DURATION) {
            $pos1 = strpos($trailer,'-');
            $pos2 = strpos($trailer,'-', $pos1 + 1);
            $this->duration = from64(substr($trailer,$pos1 + 1, $pos2-$pos1-1));
            $trailer = substr($trailer, $pos2);
        }

        if ($this->tokenType.equals("a"))
        {
            $real_trailer = $this->deobfuscate($trailer, $enc_string, 13);
        } else {
            $real_trailer = $this->deobfuscate($trailer, $enc_string, 32);
        }

        $pos1 = strpos($trailer,'-');
        $pos2 = strpos($trailer,'-', $pos1 + 1);

        if ($pos2 < 0) {
            $pos2 = strlen($real_trailer);
        }
        $pos3 = strlen($real_trailer);

        if (($this->flags & $this->FLAG_PROFILE) == $this->FLAG_PROFILE) {
            $this->profile = substr( $real_trailer, $pos1+1, $pos2-$pos1-1);
            $has_profile = true;
        }

        if (($this->flags & $this->FLAG_PAYLOAD) == $this->FLAG_PAYLOAD) {
            $end = 0;
            if( $has_profile) {
                $end = $pos3 - $pos2;
                $this->payload = substr($real_trailer, $pos2+1, $end);
            } else {
                $this->payload = "";
            }
        }

        if ($this->payload != NULL && (($this->tokenType == "c") || ($this->tokenType == "d") || ($this->tokenType == "e")))
        {
            $decoded = "";
            for ($i = 0,$j = 0; $i < strlen($this->payload); $i+= 2, $j++)
            {
                $index = $this->from64( substr($this->payload, $i, 2));
                $ch = chr($index & 0xff);
                $decoded .= $ch;
            }
            $this->payload = $decoded;
        }

     }

     /*
      * Parse token into components.
      */
     function parseToken($token,$type) {
         $tmpToken = $this->unFixSlash($token);

         $tType = substr($tmpToken,0,1);
         if( $tType != $type ) {
             return; // @todo Error out correctly
         }

         $this->flags = substr($tmpToken,1,2);
         $endString = substr($tmpToken,3,32);
         $trailer = substr($tmpToken, 36);

         $this->parseTrailer($trailer, $endString);
     }

    /*
     * Unit testing function.  Outputs various values that
     * are read in by a Java (junit) test and compared to Java
     * library results.
     */
    function test() {

        // to64 testing (0-5)
        echo $this->to64(0,0) . "\n";
        echo $this->to64(1,0) . "\n";
        echo $this->to64(63,0) . "\n";
        echo $this->to64(64,0) . "\n";
        echo $this->to64(1027,0) . "\n";
        echo $this->to64(1027,4) . "\n";

        // from64 testing (6-11)
        echo $this->from64('a') . "\n";
        echo $this->from64('b') . "\n";
        echo $this->from64('.') . "\n";
        echo $this->from64('/') . "\n";
        echo $this->from64('akamai') . "\n";
        echo $this->from64('aaaaakamai') . "\n";

        // fixWindowAndTime testing (12 and 13)
        $this->tokenTime = 1147791762;
        echo $this->fixWindowAndTime(300, 1147791762) . "\n";
        $this->tokenTime = 1147792462;
        echo $this->fixWindowAndTime(300, 1147791762) . "\n";

        // buildbuf testing (14)
        echo $this->buildBuf("A","B","C","D","E","F","G") . "\n";

        // setBitFlags testing (15-22)
        $this->setFlagBits();
        $bits = $this->flags;
        echo "{$this->flags}" . "\n";

        $this->ip = "127.0.0.1";
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->path = "/foo/bar";
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->profile = "profile";
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->password = 'myvoice';
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->window = 300;
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->payload = '100kton';
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        $this->duration = 300;
        $this->setFlagBits();
        echo "{$this->flags}" . "\n";

        // obfuscate test (23)
        echo $this->obfuscate("akamaiakamaiakamaiakamaiakamaiakamai",
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./",
            3,32) . "\n";

        // makeTrailer test (24)
        // Set token type.
        $this->tokenType = 'c';
        $trailer =  $this->makeTrailer("akamaiakamaiakamaiakamaiakamaiakamai",
            "abcdefghijklmnopqrstuvwxyz",
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        echo $trailer . "\n";



        // Set token type for trailer. (25)
        $this->tokenType = 'c';
        $this->flags = 0;
        echo $this->buildToken('c', 'newFlags', 'digesteddigesteddigesteddigesteddigested', 'timeBuf', 'prof','payl', 'durBuf') . "\n";
    }

}

?>
