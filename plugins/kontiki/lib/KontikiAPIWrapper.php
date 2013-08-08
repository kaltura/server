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
    
    public  $entryPoint;
    
	public function __construct($entryPoint)
	{
		$this->entryPoint = $entryPoint;
	}

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * @var KalturaBaseEntry $entry
     * @var KalturaFlavorAsset $flavorAsset
     * 
     * @return SimpleXMLElement
     */
	public function addKontikiVideoContentResource ($serviceToken, $uploadMoid, KalturaBaseEntry $entry, KalturaFlavorAsset $asset)
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
        
        $url = $this->entryPoint."/metadata/content?_method=POST&_ctype=xml&_data=$data&auth=$serviceToken";
		
		return $this->execAPICall($url);
	}

	/**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return SimpleXMLElement
     */
	public function getKontikiContentResource ($serviceToken, $contentMoid)
	{
		$url = $this->entryPoint."/metadata/content/$contentMoid;uploads=true?auth=$serviceToken";

        return $this->execAPICall($url);
	}

	/**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return SimpleXMLElement
     */
	public function addKontikiUploadResource ($serviceToken, $contentUrl)
	{
	    //hard-coded temporary value - testing environments inaccessible to Kontiki for pull
	    //$contentUrl = 'http://sites.google.com/site/demokmc/Home/titanicin5seconds.flv';
		$data = "<ns2:upload xmlns:ns2='http://api.kontiki.com'><sourceURL>$contentUrl</sourceURL></ns2:upload>";
		$data = base64_encode($data);
		$data = urlencode($data);

		$url = $this->entryPoint."/upload/init/pull?_method=POST&_ctype=xml&_data={$data}&auth=$serviceToken";
		return  $this->execAPICall($url);
	}

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * 
     * @return SimpleXMLElement
     */
    public function deleteKontikiContentResource ($serviceToken, $contentMoid)
    {
        $url = $this->entryPoint . "/metadata/content/$contentMoid?grid=true&_method=DELETE&auth=$serviceToken";
        return $this->execAPICall($url);
    }

    /**
     * @var string $serviceToken
     * @var string $contentMoid
     * @var int $timeout
     * 
     * @return SimpleXMLElement
     */
	public function getPlaybackResource ($serviceToken, $contentMoid, $timeout = null)
	{
	    $url = $this->entryPoint . "/playback/video/$contentMoid?". ($timeout ? "timeout=$timeout" : "" ) . "&auth=$serviceToken";
        return  $this->execAPICall($url);
	}
	
	/**
	 * @var string $url
	 * 
	 * @return string
	 */
	protected function execAPICall($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
		
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		if (!$res || ($httpcode < 200 || $httpcode >300))
			return null;
		
		return  $res ? new SimpleXMLElement($res) : true;
	}
}