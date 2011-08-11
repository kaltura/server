<?PHP
require 'RijndaelAlgorithm.php';
/*
 * Copyright:   Copyright (c) Akamai Conference 2006<p>
 * Company:     Akamai<p>
 *
 * NIST wrapper to encryption algorihtm.  Adds multi-block processing.
 *
 * $Id: NIST_CBC.php 5428 2006-06-06 21:08:18Z rbergstrom $
*/
class NIST_CBC {
    var $BLOCK_SIZE = 16;
    var $sessionKey;

    var $state;
    var $iv;
    var $userIV;
    var $buffer;
    var $buffered;

    var $cipher;

    function NIST_CBC() {
    }

    function init($state, $key) {
        $this->state = $state;
        $this->cipher = new RijndaelAlgorithm();
        $this->generateKey($key);
        $this->engineInit();
    }

    function setIV($iv) {
        // Do not check state as we only encrypt
        if(sizeOf($iv) != $this->BLOCK_SIZE)
            trigger_error("Invalid IV Size.", E_USER_ERROR);

        // Deep copy arrays
        $this->userIV = $iv;
        $this->iv = $iv;
    }

    /* Mutates inOff as side effect?  In this code inLen is unmutated. */
    function update($in,$inOff,$inLen) {
        if ($this->iv == NULL)
            trigger_error("IV is NULL.", E_USER_ERROR);

        $out = array();

        // Don't try to mutate parameter.
        $offset = $inOff;

        for ($i = 0; $i < $inLen; $i++) {
            $this->buffer[$this->buffered++] = $in[$offset++];
            if ($this->buffered >= $this->BLOCK_SIZE) {
                $temp = $this->processBuffer();
                $out = array_merge($out, $temp);
            }
        }

        return $out;
    }

    function doFinal($in,$inOff,$inLen) {
        if (($inLen + $this->buffered) % $this->BLOCK_SIZE != 0)
            trigger_error("Invalid parameter.", E_USER_ERROR);

        $out = $this->update($in, $inOff, $inLen);

        $this->state = 0; // cipher is now un-initialized
        return $out;
    }

    function generateKey($key) {
        // Convert key to byte array.
        $this->sessionKey = $this->cipher->makeKey($key,$this->BLOCK_SIZE);
    }

    function engineInit() {
        for ($i = 0; $i < $this->BLOCK_SIZE;$i++)
            $this->buffer[$i] = 0;

        $this->buffered = 0;

        if ($this->userIV != NULL)
            $this->iv = $this->userIV;
    }

    /* Function only encrypts buffer for client side. */
    function processBuffer() {
       for ($i = 0; $i < $this->BLOCK_SIZE; $i++)
            $this->iv[$i] = $this->iv[$i] ^ $this->buffer[$i];

       $this->iv = $this->cipher->blockEncrypt($this->iv, 0, $this->sessionKey);

       // Copy array;
       $result = $this->iv;

       // Reset
       $this->engineInit();

       return $result;
    }

    function self_test() {
        $key = array(0, 1, 2, 3, 4, 5, 6, 7, 0, 1, 2, 3, 4, 5, 6, 7);
        $iv  = array(0, 1, 2, 3, 4, 5, 6, 7, 0, 1, 2, 3, 4, 5, 6, 7);
        $input = array(
            0, 1, 2, 3, 4, 5, 6, 7, 0, 1, 2, 3, 4, 5, 6, 7,
            0, 1, 2, 3, 4, 5, 6, 7, 0, 1, 2, 3, 4, 5, 6, 7,
            0, 1, 2, 3, 4, 5, 6, 7, 0, 1, 2, 3, 4, 5, 6, 7 );

        $this->setIV($iv);
        $this->init(0, $key);
        $ct1 = $this->doFinal($input, 0, sizeOf($input) );
        $i = (sizeOf($input) / 2) - 3;

        $this->init(0, $key);
        $t1 = $this->update($input, 0, $i);
        $t2 = $this->doFinal($input, $i, sizeOf($input) - $i);
        $ct2 = array_merge($t1, $t2);
        if ($ct1 != $ct2)
            trigger_error("Self test failed.\n", E_USER_ERROR);
        echo "Self test PASSED!\n";
    }

}
?>
