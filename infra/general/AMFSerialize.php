<?php
/*
 Copyright 2006 Iván Montes

 This file is part of FLV tools for PHP (FLV4PHP from now on).

 FLV4PHP is free software; you can redistribute it and/or modify it under the 
 terms of the GNU General Public License as published by the Free Software 
 Foundation; either version 2 of the License, or (at your option) any later 
 version.

 FLV4PHP is distributed in the hope that it will be useful, but WITHOUT ANY 
 WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
 A PARTICULAR PURPOSE. See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with 
 FLV4PHP; if not, write to the Free Software Foundation, Inc., 51 Franklin 
 Street, Fifth Floor, Boston, MA 02110-1301, USA
*/


/**
 * Serializes a PHP variable as an AMF stream
 *
 */
class FLV_Util_AMFSerialize {

    private $isLittleEndian;

    /**
     * Class constructor
     * 
     */
    function __construct( )
    {
        //calculate endianness of the CPU
        $this->isLittleEndian = ( pack('s', 1) == pack('v', 1) );
    }
  
    /**
     * Serializes a PHP variable into an AMF stream
     *
     * @param mixed $var    The variable to serialize
     * @param bool  $skipMark   if true won't add the datatype mark
     * @return The AMF stream
     */
    function serialize( $var, $skipMark = false )
    {
        // process objects as hashed arrays
        if (is_object($var))
            $var = (array)$var;
            
        if (is_array($var))
        {
            // find out if the array is numeric or associative
            $numeric = true;
            foreach ( $var as $k=>$v )
            {
                if (!is_numeric($k))
                {
                    $numeric = false;
                    break;
                }
            }
            
            if ($numeric)
            {
                $data = ($skipMark?'':"\x0A") . pack('N', count($var));
                foreach ( $var as $v )
                {
                    $data .= $this->serialize( $v );    
                }
            } else {

                $data = ($skipMark?'':"\x08") . pack('N', count($var));             
                foreach ( $var as $k=>$v )
                {
                    $data .= $this->serialize((string)$k, true);
                    $data .= $this->serialize($v);
                }
                // end of sequence mark : empty string and 0x09 byte
                $data .= $this->serialize('', true);
                $data .= "\x09";
            }
                
            return $data;
            
        } else if (is_null($var)) {
            
            return ($skipMark?'':"\x05");
            
        } else if (is_bool($var)) {
            
            return ($skipMark?'':"\x01") . ( $var ? "\x01" : "\x00" );
            
        } else if (is_numeric($var)) {
        
            $number = pack('d', $var);
            
            //reverse bytes if we are in little-endian hardware
            if ($this->isLittleEndian)
            {
              $number = strrev( $number );
            }
            
            return ($skipMark?'':"\x00") . $number;
            
        } else if (is_string($var)) {

            // check for a date
            if (preg_match('/^([0-9]{4})-?([0-9]{2})-?([0-9]{2})T([0-9]{2}):?([0-9]{2}):?([0-9]{2})(?:\.([0-9]{1,3}))?([Z+-])([0-9:]*)$/', trim($var), $m))
            {
                $seconds = mktime( $m[4], $m[5], $m[6], $m[2], $m[3], $m[1] );
                $ms = $seconds * 1000 + $m[7];
                                
                if ($m[9])
                {                   
                    $ls = explode(':', $m[9]);
                    $tz = ($m[9]=='-' ? '-' : '') + $ls[0] * 60 + $ls[1];
                    $tz = pack('s', $tz);
                    if ($this->isLittleEndian)
                        $tz = strrev($tz);
                }
                
                return ($skipMark?'':"\x0B") . $this->serialize((float)$ms, true) . pack('n', $tz);
            }
                    
            //we could push this upto 65536 I think but I feel safer like this
            if (strlen($var) < 32768)
            {
                return ($skipMark?'':"\x02") . pack('n', strlen($var)) . $var;              
            } else {                
                return ($skipMark?'':"\x0C") . pack('N', strlen($var)) . $var;
            }
        } else {
            
            //if the datatype is not supported use a null value
            return $this->serialize( NULL );
            
        }
    }
}
?>