<?php

namespace Oracle\Oci\Common;

class UserAgent
{
    protected static $ociPhpSdkVersion = "0.0.1";
    protected static $userAgentTemplate = "Oracle-PhpSDK/{ociPhpSdkVersion} (PHP/{phpVersion}; {os}){additionalClientUserAgent}{additionalUserAgentFromEnvVar}";
    /*?string*/ protected static $additionalClientUserAgent = null;
    /*string*/ protected static $userAgent;
    /*bool*/ protected static $wasInitialized = false;

    public static function getUserAgent() // : string
    {
        UserAgent::init(false);
        return UserAgent::$userAgent;
    }

    public static function init(/*bool*/ $initializeAnyway = true)
    {
        if (UserAgent::$wasInitialized && !$initializeAnyway) {
            return;
        }

        UserAgent::$wasInitialized = true;

        $str = str_replace("{ociPhpSdkVersion}", UserAgent::$ociPhpSdkVersion, UserAgent::$userAgentTemplate);
        $str = str_replace("{phpVersion}", phpversion(), $str);
        $str = str_replace("{os}", php_uname("s") . " " . php_uname("r") . " " . php_uname("m"), $str);
        $value = "";
        if (UserAgent::$additionalClientUserAgent != null) {
            $value = " " . UserAgent::$additionalClientUserAgent;
        }
        $str = str_replace("{additionalClientUserAgent}", $value, $str);
        $additionalUserAgentFromEnvVar = getenv("OCI_SDK_APPEND_USER_AGENT");
        $value = "";
        if ($additionalUserAgentFromEnvVar != null && $additionalUserAgentFromEnvVar != false) {
            $value = " " . $additionalUserAgentFromEnvVar;
        }
        $str = str_replace("{additionalUserAgentFromEnvVar}", $value, $str);

        UserAgent::$userAgent = $str;
    }

    public static function setAdditionalClientUserAgent(string $additionalClientUserAgent)
    {
        $str = trim($additionalClientUserAgent);
        if ($str == "") {
            $str = null;
        }
        UserAgent::$additionalClientUserAgent = $str;
        UserAgent::init();
    }
}
