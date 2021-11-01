<?php

namespace Oracle\Oci\Common;

use InvalidArgumentException;

class ConfigFile
{
    const DEFAULT_PROFILE_NAME = "DEFAULT";
    /*string*/ protected $profileName;
    // map from profileName -> propertyName -> property
    protected $allProperties;

    private function __construct(
        $allProperties,
        /*string*/
        $profileName
    ) {
        $this->allProperties = $allProperties;
        $this->profileName = $profileName;
    }

    public static function loadDefault(/*string*/ $profileName = null) // : ConfigFile
    {
        return ConfigFile::loadFromFile(ConfigFile::getUserHome() . DIRECTORY_SEPARATOR . ".oci" . DIRECTORY_SEPARATOR . "config", $profileName);
    }

    public static function loadFromFile(/*string*/ $fileName, /*string*/ $profileName = null) // : ConfigFile
    {
        return ConfigFile::loadFromStringArray(explode(PHP_EOL, file_get_contents($fileName)));
    }

    public static function loadFromString(/*string*/ $str, /*string*/ $profileName = null) // : ConfigFile
    {
        return ConfigFile::loadFromStringArray(explode(PHP_EOL, $str), $profileName);
    }

    public static function loadFromStringArray(/*string*/ $str, /*string*/ $profileName = null) // : ConfigFile
    {
        if ($profileName == null) {
            $profileName = ConfigFile::DEFAULT_PROFILE_NAME;
        }

        $allProperties = [];
        $properties = null;
        $lineNumber = 0;
        $currentProfile = ConfigFile::DEFAULT_PROFILE_NAME;
        foreach ($str as $line) {
            ++$lineNumber;
            $line = trim($line);
            if (strlen($line) == 0 || substr($line, 0, 1) == "#") {
                continue;
            }
            if (substr($line, 0, 1) == "[" && substr($line, -1) == "]") {
                // profile
                if ($properties != null && count($properties) > 0) {
                    $allProperties[$currentProfile] = $properties;
                }
                $currentProfile = trim(substr($line, 1, -1));
                if (strlen($currentProfile) == 0) {
                    throw new InvalidArgumentException("Line $lineNumber contained a blank profile name ('[]' or only whitespace between brackets)");
                }
                if (array_key_exists($currentProfile, $allProperties)) {
                    $properties = $allProperties[$currentProfile];
                } else {
                    $properties = [];
                }
            } else {
                $equalsPos = strpos($line, "=");
                if ($equalsPos == false) {
                    // base 1
                    throw new InvalidArgumentException("Line $lineNumber did not contain a 'key = value' pair (no '='): $line");
                }
                $key = trim(substr($line, 0, $equalsPos));
                if (strlen($key) == 0) {
                    throw new InvalidArgumentException("Line $lineNumber contained a blank key ('=' or only whitespace before the '=')");
                }
                $value = trim(substr($line, $equalsPos + 1));
                $properties[$key] = $value;
            }
        }
        if ($properties != null && count($properties) > 0) {
            $allProperties[$currentProfile] = $properties;
        }

        return new ConfigFile($allProperties, $profileName);
    }

    public function get(/*string*/ $propertyName) // : ?string
    {
        if ($this->profileName != null) {
            // not default
            if (!array_key_exists($this->profileName, $this->allProperties)) {
                throw new InvalidArgumentException("Profile '{$this->profileName}' does not exist in this config file.");
            }
            $properties = $this->allProperties[$this->profileName];
            if (array_key_exists($propertyName, $properties)) {
                return $properties[$propertyName];
            }
        }

        // default, or not found in specified profile
        if (array_key_exists(ConfigFile::DEFAULT_PROFILE_NAME, $this->allProperties)) {
            $properties = $this->allProperties[ConfigFile::DEFAULT_PROFILE_NAME];
            if (array_key_exists($propertyName, $properties)) {
                return $properties[$propertyName];
            }
            // not found in default profile
            return null;
        }

        // no default profile found
        return null;
    }

    public function __toString()
    {
        $str = "";
        if (array_key_exists(ConfigFile::DEFAULT_PROFILE_NAME, $this->allProperties)) {
            $str .= "[" . ConfigFile::DEFAULT_PROFILE_NAME . "]" . PHP_EOL;
            foreach ($this->allProperties[ConfigFile::DEFAULT_PROFILE_NAME] as $key => $value) {
                $str .= $key . "=" . $value . PHP_EOL;
            }
        }
        foreach ($this->allProperties as $profileName => $properties) {
            if ($profileName == ConfigFile::DEFAULT_PROFILE_NAME) {
                continue;
            }
            $str .= "[" . $profileName . "]" . PHP_EOL;
            foreach ($properties as $key => $value) {
                $str .= $key . "=" . $value . PHP_EOL;
            }
        }
        return $str;
    }

    public static function getUserHome() // : string
    {
        // getenv('HOME') isn't set on windows and generates a Notice.
        $home = getenv('HOME');
        if (empty($home)) {
            if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
                // home on windows
                $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
            }
        }
        return empty($home) ? null : $home;
    }
}
