<?php
/**
 * @package    Core
 * @subpackage KMCNG
 */
class getpartnerAction extends kalturaAction
{
	const BASE64_ENCODE_CHARS_REGEX = "/^[a-zA-Z0-9\/\+\=]+$/";

	public function execute ( )
	{
		//prevent script injections - allow only base64_encode chars , which is used when creating A new hash key
        $passHashparam = $this->getRequestParameter( "setpasshashkey" );
        $hashKeyErrorCode = null;

		if ($passHashparam) {

			if (!preg_match(self::BASE64_ENCODE_CHARS_REGEX , $passHashparam))
                $hashKeyErrorCode = KExternalErrors::INVALID_HASH;
			try {
				if (!UserLoginDataPeer::isHashKeyValid($passHashparam)) {
					$hashKeyErrorCode = kUserException::NEW_PASSWORD_HASH_KEY_INVALID;
				}
			}
			catch (kCoreException $e) {
				$hashKeyErrorCode = $e->getCode();
			}
		} else {
            $hashKeyErrorCode = KExternalErrors::INVALID_HASH;
		}

		echo json_encode(array('errorCode' =>  $hashKeyErrorCode));
	}

}
