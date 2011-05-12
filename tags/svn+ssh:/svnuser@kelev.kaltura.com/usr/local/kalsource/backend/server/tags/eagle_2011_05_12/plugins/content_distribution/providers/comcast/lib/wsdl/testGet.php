<?php

date_default_timezone_set('America/New_York');

require_once 'C:\web\kaltura\infra\nusoap\class.nusoap_base.php';
require_once 'C:\web\kaltura\infra\nusoap\class.soap_transport_http.php';
require_once 'C:\web\kaltura\infra\nusoap\class.xmlschema.php';
require_once 'C:\web\kaltura\infra\nusoap\class.wsdl.php';
require_once 'C:\web\kaltura\infra\nusoap\class.soap_parser.php';
require_once 'C:\web\kaltura\infra\nusoap\class.soapclient.php';
require_once 'C:\web\kaltura\infra\nusoap\class.soap_val.php';
require_once 'C:\web\kaltura\infra\nusoap\SoapTypes.php';

require_once 'ComcastIDSet.class.php';
require_once 'ComcastBusinessObject.class.php';
require_once 'ComcastStatusObject.class.php';
require_once 'ComcastContent.class.php';
require_once 'ComcastMedia.class.php';
require_once 'ComcastMediaList.class.php';
require_once 'ComcastMediaTemplate.class.php';
require_once 'ComcastQuery.class.php';
require_once 'ComcastMediaSort.class.php';
require_once 'ComcastRange.class.php';
require_once 'ComcastMediaField.class.php';

require_once 'ComcastClient.php';
require_once 'ComcastMediaService.php';


$userName = 'roman.kreichman@kaltura.com'; 
$password = 'Roman1234';

$comcastMediaService = new ComcastMediaService($userName, $password);

$template = new ComcastMediaTemplate();
$template->fields[] = array();
$template->fields[] = ComcastMediaField::_ID;
$template->fields[] = ComcastMediaField::_PID;
$template->fields[] = ComcastMediaField::_REFRESHSTATUS;
$template->fields[] = ComcastMediaField::_STATUS;
$template->fields[] = ComcastMediaField::_STATUSDESCRIPTION;
$template->fields[] = ComcastMediaField::_STATUSDETAIL;
$template->fields[] = ComcastMediaField::_STATUSMESSAGE;
$template->fields[] = ComcastMediaField::_MEDIAFILEIDS;

$query = new ComcastQuery();
$query->name = 'ByIDs';
$query->parameterNames = array('IDs');

$ids = new soapval('item', 'IDSet', array(1821076214), false, 'ns12');
$query->parameterValues = array($ids);

$sort = new ComcastMediaSort();
$sort->field = ComcastMediaField::_ID;
$sort->descending = true;

$range = new ComcastRange();
//$range->startIndex = 1;
//$range->endIndex = 10;

try
{
	$comcastMediaList = $comcastMediaService->getMedia($template, $query, $sort, $range);
}
catch(Exception $e)
{
	echo "Error: " . $e->getMessage();
	file_put_contents('err.log', $comcastMediaService->getError());
}
file_put_contents('request.xml', $comcastMediaService->request);
file_put_contents('response.xml', $comcastMediaService->responseData);

var_dump($comcastMediaList);