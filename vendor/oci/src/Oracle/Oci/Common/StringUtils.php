<?php

namespace Oracle\Oci\Common;

use Exception;
use InvalidArgumentException;

class StringUtils
{
    public static function get_type_or_class($data)
    {
        $t = gettype($data);
        if ($t == "object") {
            return get_class($data);
        } elseif ($t == "resource") {
            return $t . " (" . get_resource_type($data) . ")";
        }
        return $t;
    }

    public static function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }

    public static function base64url_encode($data, $strict = false)
    {
        // Encode Base64 string
        $b64 = base64_encode($data);

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        return strtr($b64, '+/', '-_');
    }

    public static function base64_to_base64url($data, $strict = false)
    {
        // Convert Base64 Base64URL by replacing “+” with “-” and “/” with “_”
        return strtr($data, '+/', '-_');
    }

    public static function generateCallTrace()
    {
        // from https://www.php.net/manual/en/function.debug-backtrace.php#112238
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
    
        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
    
        return "\t" . implode("\n\t", $result);
    }

    public static function checkType($k, $v, $allowedParams) // : returns $v
    {
        if (!array_key_exists($k, $allowedParams)) {
            throw new InvalidArgumentException("Parameter '$k' invalid");
        }
        $allowedTypes = $allowedParams[$k];
        if ($allowedTypes == null) {
            // all allowed
            return $v;
        }
        if (is_array($allowedTypes)) {
            foreach ($allowedTypes as $at) {
                if (StringUtils::isType($v, $at)) {
                    return $v;
                }
            }
            throw new InvalidArgumentException("Parameter '$k' must be one of [" . implode(", ", $allowedTypes) . "], was " . StringUtils::get_type_or_class($v) . ".");
        } else {
            if (!StringUtils::isType($v, $allowedTypes)) {
                throw new InvalidArgumentException("Parameter '$k' must be a $allowedTypes, was " . StringUtils::get_type_or_class($v) . ".");
            }
            return $v;
        }
    }
    
    public static function isType($v, $allowedType) // : bool
    {
        if ($allowedType == "string") {
            return is_string($v);
        }
        return is_a($v, $allowedType);
    }

    public static function checkAllRequired($params, $requiredParams)
    {
        foreach ($requiredParams as $k) {
            if (!array_key_exists($k, $params)) {
                throw new InvalidArgumentException("The parameter '$k' is required.");
            }
        }
    }
}
