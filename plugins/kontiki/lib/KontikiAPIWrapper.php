<?php
/**
 * Class provides wrap for Kontiki API resources
 * @package plugin.kontiki
 * @subpackage lib
 */
class KontikiAPIWrapper
{
    const CONTENT_TYPE_VOD = 'CONTENT';
    
    const CONTENT_TYPE_LIVE = 'LIVE_EVENT';
    
    const FORMAT_TYPE_ORIGINAL = 'ORIGINAL';
    
    const FORMAT_TYPE_LIVE = 'NETPUB';
    
    const FORMAT_TYPE_IOS = 'IOS';
    
    public static $entryPoint;
    
	public static function createKontikiToken ($serviceToken, $actAsUser)
	{
        // $url = self::$entryPoint."/auth/login?serviceTokenId=$serviceToken&actAsUser=$actAsUser";
        // $curlWrapper = new KCurlWrapper($url);
        // $authResponse = $curlWrapper->exec();
//         
        // if (!$authResponse)
        // {
            // throw new kCoreException();
        // }
	}

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * @var KalturaBaseEntry $entry
     * @var KalturaFlavorAsset $flavorAsset
     * 
     * @return string
     */
	public static function addKontikiVideoContentResource ($serviceToken, $uploadMoid, KalturaBaseEntry $entry, KalturaFlavorAsset $asset)
	{
        $data = "<ns:content xmlns:ns='http://api.kontiki.com'>
        <contentType>". self::CONTENT_TYPE_VOD ."</contentType>
        <ignoreTranscoding>false</ignoreTranscoding>
        <encrypted>false</encrypted>
        <title>".$entry->name."</title>
        <description>" . ($entry->description ? $entry->description : $entry->id) . "</description>
        <format>
            <uploadMoid>$uploadMoid</uploadMoid>
            <type>" .self::FORMAT_TYPE_ORIGINAL ."</type>
            <bitRate>".$asset->bitrate."</bitRate>
            <height>".$asset->height."</height>
            <width>".$asset->width."</width>
        </format>
        </ns:content>";
        
        $data = base64_encode($data);
        $data = urlencode($data);
        
        $url = self::$entryPoint."/metadata/content?_method=POST&_ctype=xml&_data=$data&auth=$serviceToken";
        $curlWrapper = new KCurlWrapper($url);
        $resultHeader = $curlWrapper->getHeader();

        return $curlWrapper->exec();
	}

	/**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return string
     */
	public static function getKontikiContentResource ($serviceToken, $contentMoid)
	{
		$url = self::$entryPoint."/metadata/content/$contentMoid;uploads=true?auth=$serviceToken";
		$curlWrapper = new KCurlWrapper($url);
        $resultHeader = $curlWrapper->getHeader();

        return $curlWrapper->exec();
	}

	/**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return string
     */
	public static function addKontikiUploadResource ($serviceToken, $contentUrl)
	{
	    //hard-coded temporary value - testing environments inaccessible to Kontiki for pull
	    //$contentUrl = 'http://sites.google.com/site/demokmc/Home/titanicin5seconds.flv';
		$data = "<ns2:upload xmlns:ns2='http://api.kontiki.com'><sourceURL>$contentUrl</sourceURL></ns2:upload>";
		$data = base64_encode($data);
		$data = urlencode($data);

		$url = self::$entryPoint."/upload/init/pull?_method=POST&_ctype=xml&_data={$data}&auth=$serviceToken";
		$curlWrapper = new KCurlWrapper($url);
		$resultHeader = $curlWrapper->getHeader();
KalturaLog::info("upload result headers: " . print_r($resultHeader, true));
		
		return $curlWrapper->exec();
	}

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return string
     */
    public static function deleteKontikiContentResource ($serviceToken, $contentMoid)
    {
        $url = self::$entryPoint . "/metadata/content/$contentMoid?grid=true&_method=DELETE&auth=$serviceToken";
        $curlWrapper = new KCurlWrapper($url);
        $resultHeader = $curlWrapper->getHeader();

        return $curlWrapper->exec();
    }

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * @var int $timeout
     * 
     * @return string
     */
	public static function getPlaybackUrn ($serviceToken, $contentMoid, $timeout = null)
	{
	    $url = self::$entryPoint . "/playback/video/$contentMoid?timeout=$timeout&auth=$serviceToken";
        $curlWrapper = new KCurlWrapper($url);
        $result = $curlWrapper->exec();
        if (!$result)
            KExternalErrors::dieError(KExternalErrors::BAD_QUERY);
        
        $resultAsXml = new SimpleXMLElement($result);
        return strval($resultAsXml->urn);
	}
}