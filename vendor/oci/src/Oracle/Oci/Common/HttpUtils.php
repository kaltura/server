<?php

namespace Oracle\Oci\Common;

use DateTime;
use GuzzleHttp\Exception\BadResponseException;
use InvalidArgumentException;

class HttpUtils
{
    public static function addToArray(&$queryMap, $paramName, /*string*/ $value)
    {
        if (array_key_exists($paramName, $queryMap)) {
            $oldValue = $queryMap[$paramName];
            if (is_array($oldValue)) {
                $oldValue[] = $value;
                $queryMap[$paramName] = $oldValue;
            } else {
                $queryMap[$paramName] = [$oldValue, $value];
            }
        } else {
            $queryMap[$paramName] = $value;
        }
    }

    public static function encodeArray(&$queryMap, /*string*/ $paramName, $array, /*string*/ $collectionFormat)
    {
        if ($array == null || empty($array)) {
            return;
        }
        switch ($collectionFormat) {
            case "csv":
                $sep = ',';
                break;
            case "ssv":
                $sep = ' ';
                break;
            case "tsv":
                $sep = "\t";
                break;
            case "pipes":
                $sep = '|';
                break;
            default:
                $collectionFormat = "multi";
                break;
        }
        if ($collectionFormat == "multi") {
            foreach ($array as $item) {
                HttpUtils::addToArray($queryMap, $paramName, HttpUtils::attemptEncodeParam($item));
            }
        } else {
            $result = "";
            foreach ($array as $item) {
                if (strlen($result) > 0) {
                    $result = $result . $sep;
                }
                $result = $result . HttpUtils::attemptEncodeParam($item);
            }
            HttpUtils::addToArray($queryMap, $paramName, $result);
        }
    }

    public static function encodeMap(&$queryMap, /*string*/ $paramName, /*?string*/ $prefix, $map)
    {
        if ($prefix == null) {
            $prefix = "";
        }
        if ($map != null) {
            foreach ($map as $key => $value) {
                HttpUtils::encodeMapParamValue($queryMap, $prefix . $key, $value);
            }
        }
    }

    public static function encodeMapParamValue(&$queryMap, /*string*/ $prefixedKey, $value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeParam($item));
            }
        } else {
            HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeParam($value));
        }
    }

    public static function attemptEncodeParam($value) // : string
    {
        if ($value instanceof DateTime) {
            return $value->format(HttpUtils::$RFC3339_EXTENDED);
        }
        return strval($value);
    }

    public static $RFC3339_EXTENDED = "Y-m-d\TH:i:s.uP";

    public static function orNull($params=[], $paramName, $required = false)
    {
        // PHP 5.6 does not have the ?? operator
        if (array_key_exists($paramName, $params)) {
            return $params[$paramName];
        }
        if ($required) {
            throw new InvalidArgumentException("The parameter '$paramName' is required");
        }
        return null;
    }

    public static function queryMapToString($queryMap) // : string
    {
        // It is not straight-forward to get repeated query parameters to work in the OCI way using Guzzle.
        // Instead of
        //     ?key=value&key=other
        // Guzzle by default produces
        //     ?key[0]=value&key[1]=other
        // Instead, we build our own query string.
        $str = '';
        foreach ($queryMap as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $str .= '&' . $key . '=' . $item;
                }
            } else {
                $str .= '&' . $key . '=' . $value;
            }
        }
        if (strlen($str) > 0) {
            $str[0] = '?';
        }
        return $str;
    }

    public static function processBadResponseException(&$e)
    {
        // BadResponseException includes 4xx and 5xx exceptions
        if ($e instanceof BadResponseException) {
            $__response = $e->getResponse();
            throw new OciBadResponseException($__response);
        }
        // We'll directly throw ConnectException, RequestException (excluding BadResponseException)
        throw $e;
    }
}
