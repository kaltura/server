<?php
class kPlayReadyAESContentKeyGenerator
{
	const DRM_AES_KEYSIZE_128 = 16;    
	
	private static function generateGuid($prefix)
	{
		mt_srand((double) microtime() * 10000);
		$charid = strtoupper(md5(uniqid($prefix, true)));
			     
		$guid = substr($charid,  0, 8) . '-' .
				substr($charid,  8, 4) . '-' .
				substr($charid, 12, 4) . '-' .
				substr($charid, 16, 4) . '-' .
				substr($charid, 20, 12);
				
		return $guid;
	}

	private static function guidToByteArray($guid)
	{
		$hexStr = str_replace('-','',$guid);
		$c = explode('-',chunk_split($hexStr,2,'-'));
		$hexArr = array($c[3],$c[2],$c[1],$c[0],$c[5],$c[4],$c[7],$c[6],$c[8],$c[9],$c[10],$c[11],$c[12],$c[13],$c[14],$c[15]);
		$guidBytes = '';
		for ($i = 0; $i < 16; ++$i) 
		{
		    $num = hexdec($hexArr[$i]);
		    $guidBytes .= chr($num);
		}
		
		return $guidBytes;
	}

	public static function generatePlayReadyContentKey($keySeed, $keyId)
	{		
		$keyIdAsBytes = self::guidToByteArray($keyId);
		
		$truncatedKeySeed = substr($keySeed, 0, 60);
		$truncatedKeySeed = pack("H*", $truncatedKeySeed);
		
		//Create sha_A_bytes buffer.  It is the SHA of the truncatedKeySeed and the keyIdAsBytes
		$shaAHash = hash('sha256', $truncatedKeySeed.$keyIdAsBytes, true);
		$sha_A_bytes = unpack("C*", $shaAHash);
		
	    //Create sha_B_Output buffer.  It is the SHA of the truncatedKeySeed, the keyIdAsBytes, and the truncatedKeySeed again.
	    $shaBHash = hash('sha256', $truncatedKeySeed.$keyIdAsBytes.$truncatedKeySeed, true);
		$sha_B_bytes = unpack("C*", $shaBHash);
		
		//Create sha_C_Output buffer.  It is the SHA of the truncatedKeySeed, the keyIdAsBytes, the truncatedKeySeed again, and the keyIdAsBytes again.
		$shaCHash = hash('sha256', $truncatedKeySeed.$keyIdAsBytes.$truncatedKeySeed.$keyIdAsBytes, true);
		$sha_C_bytes = unpack("C*", $shaCHash);
		
		$contentKey='';
	    for ($i = 1; $i <= self::DRM_AES_KEYSIZE_128; $i++)
	    {
	    	$num = 
	    	$sha_A_bytes[$i] ^ $sha_A_bytes[$i + self::DRM_AES_KEYSIZE_128] 
	    	^ $sha_B_bytes[$i] ^ $sha_B_bytes[$i + self::DRM_AES_KEYSIZE_128] 
	    	^ $sha_C_bytes[$i] ^ $sha_C_bytes[$i + self::DRM_AES_KEYSIZE_128];
	        $contentKey .= chr($num);
	    }
	
	    return base64_encode($contentKey);
	}
	
	public static function generatePlayReadyKeyId()
	{
		return self::generateGuid(kCurrentContext::$host);
	}
}