<?PHP

require 'StreamToken.php';
require 'TypeCToken.php';
require 'TypeDToken.php';
require 'TypeEToken.php';

class StreamTokenFactory
{
    var $mCodeVersion = "3.0.1";

    function getVersion() {
        return $this->mCodeVersion;
    }

    function makeTypeCToken($userPath,
            $userIp,$userProfile,$userPasswd,
            $userTime,$userWindow,$userDuration,$userPayload)
    {
        $token =  new TypeCToken($userPath, $userIp, $userProfile, $userPasswd,
            $userTime, $userWindow, $userDuration, $userPayload);

        return $token;
    }

    function makeTypeDToken($userPath,
            $userIp,$userProfile,$userPasswd,
            $userTime,$userWindow,$userDuration,$userPayload)
    {
        $token =  new TypeDToken($userPath, $userIp, $userProfile, $userPasswd,
            $userTime, $userWindow, $userDuration, $userPayload);

        return $token;
    }

    function makeTypeEToken($userPath,
            $userIp,$userProfile,$userPasswd,
            $userTime,$userWindow,$userDuration,$userPayload,$userKey)
    {

        $token =  new TypeEToken($userPath, $userIp, $userProfile, $userPasswd,
            $userTime, $userWindow, $userDuration, $userPayload, $userKey);

        return $token;
    }

    function getToken($tokenType,$userPath,
            $userIp,$userProfile,$userPasswd,
            $userTime,$userWindow,$userDuration,
            $userPayload,$userKey) {

        if( null == $tokenType ) {
            exit("Token type must be one of: c,d, or e.");
        }

        $token = null;

        switch($tokenType) {
            case "c":
                $token =  new TypeCToken($userPath, $userIp, $userProfile, $userPasswd,
                    $userTime, $userWindow, $userDuration, $userPayload);
                break;
            case "d":
                $token =  new TypeDToken($userPath, $userIp, $userProfile, $userPasswd,
                    $userTime, $userWindow, $userDuration, $userPayload);
                break;
            case "e":
                if( null == $userKey ) {
                    exit("Type E Token requires user key.  Use -k option.\n");
                }
                $token =  new TypeEToken($userPath, $userIp, $userProfile, $userPasswd,
                    $userTime, $userWindow, $userDuration, $userPayload, $userKey);
                break;
            default:
                exit("Token type must be one of: c,d, or e.\n");
        }

        return $token;
    }

}

?>
