<?php
/**
 * KSR - Kaltura Screencast Recorder
 * This action is used for integrating the KSR widget into web pages, by returning a JS code that provides everything the integrator needs in order to load the widget
 * the KSR widget is a JAVA applet that allows the user to record the screen, and then it uploads the recording to Kaltura.
 * the JS code which is returned to the page is constructed from a template which is part of a version of the widget (e.g. flash/ksr/v1.0.32/js/*) and it is constructed with values stored in the uiconf XML.
 *
 * @package Core
 * @subpackage externalWidgets
 */

class ksrAction extends sfAction 
{
    const SOM_JS_FILENAME = 'som.js';
    const SOM_DETECT_JS_FILENAME = 'som-detect.js';
    const KALTURA_LIB_JS_FILENAME = 'lib.js';
    const KALTURA_LIB_API_JS_FILENAME = 'api.js';
    const JS_PATH_IN_JARS_FOLDER = 'js';
    
    private $jsTemplateParams = array(
        /** environment options **/
        'KALTURA_SERVER' => array( 'method' => '_getKalturaHost', ), // comes from local.ini
        'JAR_HOST_PATH' => array( 'method' => '_buildJarsHostPath' ), // CDN host + swf_url [ conf object +  ]
        'SOM_PARTNER_ID' => array( 'method' => '_getSomPartnerInfo', 'param' => 'id', ), // comes from local.ini
        'SOM_PARTNER_SITE' => array( 'method' => '_getSomPartnerInfo', 'param' => 'site', ), // comes from local.ini, empty by default
        'SOM_PARTNER_KEY' => array( 'method' => '_getSomPartnerInfo', 'param' => 'key', ),// comes from local.ini

        /** uiconf object originated options **/
        'SOM_JAR_RUN' => array( 'method' => '_getRunJarNameFromSwfUrl' ), // parse swf_url for filename.jar

        /** uiconf XML originated options **/
        'KALTURA_VIDEOBITRATE' => array( 'value' => 0, 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/videoBitRate', ),
        'KALTURA_CATEGORY' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/category', ),
        'KALTURA_CONVERSIONPROFILEID' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/conversionProfileId', ),
        'KALTURA_SUBMIT_TITLE_VALUE' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/title/value', ),
        'KALTURA_SUBMIT_DESCRIPTION_VALUE' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/description/value', ),
        'KALTURA_SUBMIT_TAGS_VALUE' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/tags/value', ),
        'KALTURA_SUBMIT_TITLE_ENABLED' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/title/enabled', ),
        'KALTURA_SUBMIT_DESCRIPTION_ENABLED' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/description/enabled', ),
        'KALTURA_SUBMIT_TAGS_ENABLED' => array( 'method' => '_getFromXml', 'param' => '/uiconf/kaltura/submit/tags/enabled', ),
        
        'KALTURA_ERROR_MESSAGES' => array( 'value' => '', 'method' => '_getErrorMessagesFromXml'),
        'SOM_CAPTURE_ID' => array( 'method' => '_getFromXml', 'param' => '/uiconf/som/captureId', ),
        'SOM_MAC_NAME' => array( 'method' => '_getFromXml', 'param' => '/uiconf/som/macName', ),
        'SOM_SIDE_PANEL_ONLY' => array(
            'value' => 'true', // default value here, due to JS no wrapped in quotes
            'method' => '_getFromXml', 'param' => '/uiconf/som/sidePanelOnly',
        ),
        'SOM_JARS' => array( 'method' => '_getJarsFromXml', ),
        'SOM_RECORDER_OPTIONS_SKIN0' => array( 'method' => '_getFromXml', 'param' => '/uiconf/som/recorderOptions/skin0', ),
        'SOM_RECORDER_OPTIONS_MAXCAPTURESEC' => array(
            'value' => 7200, // default value here, due to JS not wrapped in quotes
            'method' => '_getFromXml',  'param' => '/uiconf/som/recorderOptions/maxCaptureSec',
        ), 
    );

    private $uiconfObj;
    private $uiconfXmlObj;
    private $jsResult = '';

    /**
     * Will return a JS library for integrating the KSR (similar to HTML5 in concept)
     * uiconfId specifies from which uiconf to fetch different settings that should be replaced in the JS
     */
    public function execute()
    {
        // make sure output is not parsed as HTML
        header("Content-type: application/x-javascript");
        
        $uiconfId = $this->getRequestParameter("uiconfId"); // replace all $_GET with $this->getRequestParameter()
        // load uiconf from DB.

        $this->uiconfObj = uiConfPeer::retrieveByPK($uiconfId);
	if(!$this->uiconfObj)
	{
		KExternalErrors::dieError(KExternalErrors::UI_CONF_NOT_FOUND);
	}

	$ui_conf_swf_url = $this->uiconfObj->getSwfUrl();
	if (!$ui_conf_swf_url)
	{
		KExternalErrors::dieError(KExternalErrors::ILLEGAL_UI_CONF, "SWF URL not found in UI conf");
	}
        
        @libxml_use_internal_errors(true);
        try
        {
            $this->uiconfXmlObj = new SimpleXMLElement(trim($this->uiconfObj->getConfFile()));
        }
        catch(Exception $e)
        {
            KalturaLog::err("malformed uiconf XML - base64 encoded: [".base64_encode(trim($this->uiconfObj->getConfFile()))."]");
        }
        if(!($this->uiconfXmlObj instanceof SimpleXMLElement))
        {
            // no xml or invalid XML, so throw exception
            throw new Exception('uiconf XML is invalid');
        }
        // unsupress the xml errors
        @libxml_use_internal_errors(false);


        $this->_initReplacementTokens();;
        $this->_prepareLibJs();
        $this->_prepareJs();

        echo $this->jsResult;
	die;
    }
    
    private function _initReplacementTokens()
    {
        foreach($this->jsTemplateParams as $token => $settings)
        {
            if(!isset($settings['value'])) $this->jsTemplateParams[$token]['value'] = ''; // init empty value where needed
            $method = $settings['method'];
            $param = (isset($settings['param']))? $settings['param']: null;

            $value = $this->$method($param);
            if($value !== false)
            {
                $this->jsTemplateParams[$token]['value'] = $value;
            }
        }
    }

    private function _getJarsPathFromSwfUrl()
    {
        $lastSlash = strrpos($this->uiconfObj->getSwfUrl(), '/');
        return substr($this->uiconfObj->getSwfUrl(), 0, $lastSlash);
    }

    private function _getRunJarNameFromSwfUrl()
    {
        $lastSlash = strrpos($this->uiconfObj->getSwfUrl(), '/');
        return substr($this->uiconfObj->getSwfUrl(), $lastSlash+1);
    }

    
    private function _buildJarsHostPath()
    {
        $baseUrl = myPartnerUtils::getCdnHost($this->uiconfObj->getPartnerId());

        $jarPath = $this->_getJarsPathFromSwfUrl();

        $scheme = parse_url($jarPath, PHP_URL_SCHEME);
        if(!is_null($scheme)) // $jarsPath is absolute URL -just return it.
        {
            return $jarPath;
        }
        else
        {
            $jarPath = ltrim($jarPath, '/');
            $fullUrl = $baseUrl .'/'. $jarPath;;
            return $fullUrl;
        }
    }

    private function _getKalturaHost()
    {
        $proto='http';
        $kalturaHost = kConf::get('www_host');
        if (infraRequestUtils::getProtocol() == infraRequestUtils::PROTOCOL_HTTPS){
            $proto='https';
            if(kConf::hasParam('www_host_https')){
                $kalturaHost = kConf::get('www_host_https');
            }
        }
        $url = $proto .'://'. $kalturaHost;
        return $url;
    }

    private function _getSomPartnerInfo($what)
    {
        switch($what)
        {
            case 'id':   return kConf::get('ksr_id');
            case 'site': return kConf::get('ksr_site');
            case 'key':  return kConf::get('ksr_key');
        }
    }

    private function _getFromXml($xpath)
    {
        $xpathArr = $this->uiconfXmlObj->xpath($xpath);
        if (is_array($xpathArr) && count($xpathArr))
        {
            return (string)$xpathArr[0];
        }
        else
        {
            return false;
        }
    }

    private function _getJarsFromXml()
    {
        $jarsStr = '';
        $xpath = '/uiconf/jars/jar';

        $xpathArr = $this->uiconfXmlObj->xpath($xpath);
        if (is_array($xpathArr) && count($xpathArr))
        {
            foreach($xpathArr as $jar)
            {
                $jarsStr .= PHP_EOL."'".(string)$jar."',";
            }
            $jarsStr = rtrim($jarsStr, ',').PHP_EOL;
        }
        return $jarsStr;
    }

    // this is the only place where this code "knows" the JS because we want to loop dynamically over all error messages override in uiconf
    private function _getErrorMessagesFromXml()
    {
        $errormsgs = array();
        $xpath = '/uiconf/kaltura/errorMessages/*';

        $xpathArr = $this->uiconfXmlObj->xpath($xpath);
        if (is_array($xpathArr) && count($xpathArr))
        {
            foreach($xpathArr as $key => $msgNode)
            {
                $msgDetails = (array)$msgNode->children();
                if(isset($msgDetails['starts']) && isset($msgDetails['replace']))
                {
                    $starts = $msgDetails['starts'];
                    $replace = $msgDetails['replace'];
                    $errormsgs[] = 'name = "kaltura.error.messages.'.$key.'.starts";'.PHP_EOL;
                    $errormsgs[] = "kalturaScreenRecord.errorMessages[name] = '".$starts."';".PHP_EOL;
                    $errormsgs[] = 'name = "kaltura.error.messages.'.$key.'.replace";'.PHP_EOL;
                    $errormsgs[] = "kalturaScreenRecord.errorMessages[name] = '".$replace."';".PHP_EOL;
                }
            }
        }
        $returnStr = implode('', $errormsgs);
        return $returnStr;
    }

    private function _getJsFilesPath()
    {
        $jarsPath = $this->_getJarsPathFromSwfUrl();
        $scheme = parse_url($jarsPath, PHP_URL_SCHEME);
        if(!is_null($scheme))
        {
            // TODO - do we want to handle loading the JS file from remote URL?
            // or artenatively find a way to get them locally?
            throw new Exception("cannot load JS files from absolute URL");
        }

        // TODO - find a way to extract this value from an .ini file
        $baseServerPath = rtrim(myContentStorage::getFSContentRootPath(), '/').'/';
        return $baseServerPath.$jarsPath.'/'.self::JS_PATH_IN_JARS_FOLDER .'/';
    }
    
    private function _prepareLibJs()
    {
	$filePath = $this->_getJsFilesPath(). self::KALTURA_LIB_JS_FILENAME;
	if(!file_exists($filePath))
	{
		KExternalErrors::dieError(KExternalErrors::ILLEGAL_UI_CONF, "Required file is missing");
	}
        $this->jsResult = file_get_contents($filePath);

        foreach($this->jsTemplateParams as $token => $info)
        {
            $value = $info['value'];
            $this->jsResult = str_replace($token, $value, $this->jsResult);
        }
    }

    private function _prepareJs()
    {
	$baseFilePath = $this->_getJsFilesPath();
	$somDetectJsPath = $baseFilePath. self::SOM_DETECT_JS_FILENAME;
	$somJsPath = $baseFilePath. self::SOM_JS_FILENAME;
	$apiJsPath = $baseFilePath. self::KALTURA_LIB_API_JS_FILENAME;
	
	if(!file_exists($somDetectJsPath) || !file_exists($somJsPath) || !file_exists($apiJsPath))
	{
		KExternalErrors::dieError(KExternalErrors::ILLEGAL_UI_CONF, "Required file is missing");
	}
	
        $somDetectJs = file_get_contents($somDetectJsPath);
        $somJs = file_get_contents($somJsPath);
        $apiJs = file_get_contents($apiJsPath);
        $fullJs = $somDetectJs. PHP_EOL. $somJs . PHP_EOL . $this->jsResult . PHP_EOL . $apiJs;
        $this->jsResult = $fullJs;
    }
}
