<?PHP
/*
 * Rijndael --pronounced Reindaal-- is a variable block-size (128-, 192- and
 * 256-bit), variable key-size (128-, 192- and 256-bit) symmetric cipher.<p>
 *
 * Rijndael was written by <a href="mailto:rijmen@esat.kuleuven.ac.be">Vincent
 * Rijmen</a> and <a href="mailto:Joan.Daemen@village.uunet.be">Joan Daemen</a>.<p>
 *
 * Portions of this code are <b>Copyright</b> &copy; 1997, 1998
 * <a href="http://www.systemics.com/">Systemics Ltd</a> on behalf of the
 * <a href="http://www.systemics.com/docs/cryptix/">Cryptix Development Team</a>.
 * <br>All rights reserved.<p>
 *
 * $Id: RijndaelAlgorithm.php 5428 2006-06-06 21:08:18Z rbergstrom $
 */
require 'RijndaelConstants.php';
class RijndaelAlgorithm extends RijndaelConstants {

     /*
      * Ctor.
      */
     function RijndaelAlgorithm() {
         parent::RijndaelConstants();
     }

     /*
     * Expand a user-supplied key material into a session key.
     *
     * Parameters:
     *    k          The 128/192/256-bit user-key to use.
     *    blockSize  The block size in bytes of this Rijndael.
     */
     function makeKey($k, $blockSize) {
        // Validate key
        if( NULL == $k ) {
            trigger_error("Empty key", E_USER_ERROR);
        }
        if( !(16 == sizeOf($k) || 24 == sizeOf($k) || 32 == sizeOf($k) ) ) {
            trigger_error("Incorrect key length", E_USER_ERROR);
        }

        $ROUNDS = $this->getRounds(sizeOf($k), $blockSize);
        $BC = $blockSize / 4;
        $ROUND_KEY_COUNT = ($ROUNDS +1) * $BC;
        $KC = sizeOf($k) /4;
        /*
        echo "ROUNDS: ". $ROUNDS ."\n";
        echo "BC: ". $BC ."\n";
        echo "KC: ". $KC ."\n";
        */
        // copy user material bytes into temporary ints
        for($i=0,$j=0; $i<$KC;) {
            //echo "j: $j \n";
            $tk[$i++] = ($k[$j++] & 0xFF) << 24  | ($k[$j++] & 0xFF) << 16  | ($k[$j++] & 0xFF) << 8 | ($k[$j++] & 0xFF);
            /*
            $tk[$i++] = (($k[$j++] & 0xFF) << 24) |
                        (($k[$j++] & OxFF) << 16) |
                        (($k[$j++] & 0xFF) <<  8) |
                        ($k[$j++] & 0xFF); */
          //  echo "tk: " . dechex( $tk[$i-1] ) . "\n";
        }
        // copy values into round key arrays
        $t = 0;
        $rconpointer = 0;
        for($j=0,$t=0; ($j<$KC) && ($t<$ROUND_KEY_COUNT); $j++,$t++) {
            // 06062006 RAB PHP has no integer division so convert to int.
            $Ke[(int)$t / $BC][$t % $BC] = $tk[$j];
            $Kd[$ROUNDS - (int)($t / $BC)][$t % $BC] = $tk[$j];
        }

        while($t<$ROUND_KEY_COUNT) {
            // extrapolate using phi (the round key evolution function)
            $tt = $tk[$KC-1];

            $tk[0] ^= ($this->S[($tt>>16) & 0xFF] & 0xFF) << 24 ^
                      ($this->S[($tt>> 8) & 0xFF] & 0xFF) << 16 ^
                      ($this->S[$tt       & 0xFF] & 0xFF) <<  8 ^
                      ($this->S[($tt>>24) & 0xFF] & 0xFF)       ^
                      ($this->rcon[$rconpointer++]   & 0xFF) << 24;

            if($KC !=8) {
                for ($i = 1, $j = 0; $i < $KC; ) $tk[$i++] ^= $tk[$j++];
            } else {
                for ($i = 1, $j = 0; $i < $KC / 2; ) $tk[$i++] ^= $tk[$j++];
                $tt = $tk[$KC / 2 - 1];
                $tk[$KC / 2] ^= ($this->S[$tt         & 0xFF] & 0xFF)        ^
                                ($this->S[($tt >>  8) & 0xFF] & 0xFF) <<  8 ^
                                ($this->S[($tt >> 16) & 0xFF] & 0xFF) << 16 ^
                                ($this->S[($tt >> 24) & 0xFF] & 0xFF) << 24;
                for ($j = $KC / 2, $i = $j + 1; $i < $KC; ) $tk[$i++] ^= $tk[$j++];
            }
            // copy values into round key arrays
            for ($j = 0; ($j < $KC) && ($t < $ROUND_KEY_COUNT); $j++, $t++) {
                $Ke[(int)$t / $BC][$t % $BC] = $tk[$j];
                $Kd[$ROUNDS - (int)($t / $BC)][$t % $BC] = $tk[$j];
            }
        }

        // inverse MixColumn where needed
        for ($r = 1; $r < $ROUNDS; $r++) {
            for ($j = 0;$j < $BC; $j++) {
                $tt = $Kd[$r][$j];
                $Kd[$r][$j] = $this->U1[($tt >> 24) & 0xFF] ^
                           $this->U2[($tt >> 16) & 0xFF] ^
                           $this->U3[($tt >>  8) & 0xFF] ^
                           $this->U4[ $tt        & 0xFF];
            }
        }
        $sessionKey = array( $Ke, $Kd );
        return $sessionKey;
     }

     function getRounds($keySize,$blockSize) {
        switch ($keySize) {
        case 16:
            return $blockSize == 16 ? 10 : ($blockSize == 24 ? 12 : 14);
        case 24:
            return $blockSize != 32 ? 12 : 14;
        default: // 32 bytes = 256 bits
            return 14;
        }
    }

    function blockEncrypt($in, $inOffset, $sessionKey) {
        // Extract encryption key.
        $Ke = $sessionKey[0];    // Ke[ROUNDS+1][4]
        $ROUNDS = sizeOf($Ke)-1; // first dimension ROUNDS+1

        $Ker = $Ke[0];           // Ker[ROUNDS+1]
        $offset = $inOffset;

        // --- Debug --
        /* echo "Input: ";
        for($qq=0;$qq<16;$qq++) {
            echo dechex($in[$qq] & 0xff);
        }
        echo "\n"; */
        // --- Debug ---

        // plaintext to ints + key
        $t0   = (($in[$offset++] & 0xFF) << 24 |
                 ($in[$offset++] & 0xFF) << 16 |
                 ($in[$offset++] & 0xFF) <<  8 |
                 ($in[$offset++] & 0xFF)        ) ^ $Ker[0];
        $t1   = (($in[$offset++] & 0xFF) << 24 |
                 ($in[$offset++] & 0xFF) << 16 |
                 ($in[$offset++] & 0xFF) <<  8 |
                 ($in[$offset++] & 0xFF)        ) ^ $Ker[1];
        $t2   = (($in[$offset++] & 0xFF) << 24 |
                 ($in[$offset++] & 0xFF) << 16 |
                 ($in[$offset++] & 0xFF) <<  8 |
                 ($in[$offset++] & 0xFF)        ) ^ $Ker[2];
        $t3   = (($in[$offset++] & 0xFF) << 24 |
                 ($in[$offset++] & 0xFF) << 16 |
                 ($in[$offset++] & 0xFF) <<  8 |
                 ($in[$offset++] & 0xFF)        ) ^ $Ker[3];
        /*
        echo "Ker[0] " . $Ker[0] . "\n";
        echo "Ker[1] " . $Ker[1] . "\n";
        echo "Ker[2] " . $Ker[2] . "\n";
        echo "Ker[3] " . $Ker[3] . "\n";

        echo "Start CT:" . dechex($t0) .dechex($t1) .dechex($t2) .dechex($t3) . "\n";
        */


        $a0 = array();
        $a1 = array();
        $a2 = array();
        $a3 = array();

        // apply round transforms
        for ($r = 1; $r < $ROUNDS; $r++) {
            //echo "Round: $r \n";
            $Ker = $Ke[$r];
            /*
            echo "T1: " . $this->T1[($t0 >> 24) & 0xFF] . "\n";
            echo "T2: " . $this->T2[($t1 >> 16) & 0xFF] . "\n";
            echo "T3: " . $this->T3[($t2 >>  8) & 0xFF] . "\n";
            echo "T4: " . $this->T4[ $t3        & 0xFF] . "\n"; */

            $a0   = ($this->T1[($t0 >> 24) & 0xFF] ^
                     $this->T2[($t1 >> 16) & 0xFF] ^
                     $this->T3[($t2 >>  8) & 0xFF] ^
                     $this->T4[ $t3        & 0xFF]  ) ^ $Ker[0];
            $a1   = ($this->T1[($t1 >> 24) & 0xFF] ^
                     $this->T2[($t2 >> 16) & 0xFF] ^
                     $this->T3[($t3 >>  8) & 0xFF] ^
                     $this->T4[ $t0        & 0xFF]  ) ^ $Ker[1];
            $a2   = ($this->T1[($t2 >> 24) & 0xFF] ^
                     $this->T2[($t3 >> 16) & 0xFF] ^
                     $this->T3[($t0 >>  8) & 0xFF] ^
                     $this->T4[ $t1        & 0xFF]  ) ^ $Ker[2];
            $a3   = ($this->T1[($t3 >> 24) & 0xFF] ^
                     $this->T2[($t0 >> 16) & 0xFF] ^
                     $this->T3[($t1 >>  8) & 0xFF] ^
                     $this->T4[ $t2        & 0xFF]  ) ^ $Ker[3];

            $t0 = $a0;
            $t1 = $a1;
            $t2 = $a2;
            $t3 = $a3;

/*            echo "Ker[0] " . $Ker[0] . "\n";
            echo "Ker[1] " . $Ker[1] . "\n";
            echo "Ker[2] " . $Ker[2] . "\n";
            echo "Ker[3] " . $Ker[3] . "\n";
            echo "t0: $t0 \n";
            echo "t1: $t1 \n";
            echo "t2: $t2 \n";
            echo "t3: $t3 \n\n"; */
        }



        $result = array();

        // last round is special
        $Ker = $Ke[$ROUNDS];
        $tt = $Ker[0];
        $result[ 0] = ($this->S[($t0 >> 24) & 0xFF] ^ (($tt >> 24) & 0xFF) );
        $result[ 1] = ($this->S[($t1 >> 16) & 0xFF] ^ (($tt >> 16) & 0xFF) );
        $result[ 2] = ($this->S[($t2 >>  8) & 0xFF] ^ (($tt >>  8) & 0xFF) );
        $result[ 3] = ($this->S[ $t3        & 0xFF] ^  ($tt        & 0xFF) );
        $tt = $Ker[1];
        $result[ 4] = ($this->S[($t1 >> 24) & 0xFF] ^ (($tt >> 24) & 0xFF) );
        $result[ 5] = ($this->S[($t2 >> 16) & 0xFF] ^ (($tt >> 16) & 0xFF) );
        $result[ 6] = ($this->S[($t3 >>  8) & 0xFF] ^ (($tt >>  8) & 0xFF) );
        $result[ 7] = ($this->S[ $t0        & 0xFF] ^  ($tt        & 0xFF) );
        $tt = $Ker[2];

        $result[ 8] = ($this->S[($t2 >> 24) & 0xFF] ^ (($tt >> 24) & 0xFF) );
        $result[ 9] = ($this->S[($t3 >> 16) & 0xFF] ^ (($tt >> 16) & 0xFF) );
        $result[10] = ($this->S[($t0 >>  8) & 0xFF] ^ (($tt >>  8) & 0xFF) );
        $result[11] = ($this->S[ $t1        & 0xFF] ^  ($tt        & 0xFF) );
        $tt = $Ker[3];
        $result[12] = ($this->S[($t3 >> 24) & 0xFF] ^ (($tt >> 24) & 0xFF) );
        $result[13] = ($this->S[($t0 >> 16) & 0xFF] ^ (($tt >> 16) & 0xFF) );
        $result[14] = ($this->S[($t1 >>  8) & 0xFF] ^ (($tt >>  8) & 0xFF) );
        $result[15] = ($this->S[ $t2        & 0xFF] ^  ($tt        & 0xFF) );

        // --- Debug --
        /*
        echo "Result: ";
        for($qq=0;$qq<16;$qq++) {
            echo dechex($result[$qq] & 0xff);
        }
        echo "\n";
        */
        // --- Debug ---

        return $result;
    }
}
?>
