<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class XsltTesterApiAction extends KalturaApplicationPlugin
{
    protected $client;

	public function __construct()
	{
		$this->action = 'listDistributionProfiles';
		$this->label = null;
		$this->rootLabel = null;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_BASE);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
        $entryId = $action->getRequest()->getParam('entry-id');
        $xslt = $action->getRequest()->getParam('xslt');

        $this->client = Infra_ClientHelper::getClient();
        $xml = $this->client->media->getMrss($entryId);
        $xslParams = array();
        $xslParams['entryDistributionId'] = '';
        $xslParams['distributionProfileId'] = '';
        ob_start();
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);

        $xsltDoc = new DOMDocument();
        $xsltDoc->loadXML($xslt);

        $xslt = new XSLTProcessor();
        $xslt->registerPHPFunctions(); // it is safe to register all php fuctions here
        $xslt->setParameter('', $xslParams);
        $xslt->importStyleSheet($xsltDoc);

        $ob = ob_get_clean();
        ob_end_clean();
        if ($ob)
            $action->getHelper('json')->direct(array('error' => $ob));

        $obj = array('result' => $xslt->transformToXml($xmlDoc));
        $action->getHelper('json')->direct($obj);
	}
}