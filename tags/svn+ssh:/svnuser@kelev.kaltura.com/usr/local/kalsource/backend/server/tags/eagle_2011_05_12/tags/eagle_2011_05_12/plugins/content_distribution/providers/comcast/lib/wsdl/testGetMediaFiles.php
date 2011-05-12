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

require_once 'ComcastExpression.class.php';
require_once 'ComcastIDSet.class.php';
require_once 'ComcastBusinessObject.class.php';
require_once 'ComcastStatusObject.class.php';
require_once 'ComcastContent.class.php';
require_once 'ComcastMediaFile.class.php';
require_once 'ComcastMediaFileList.class.php';
require_once 'ComcastMediaFileTemplate.class.php';
require_once 'ComcastQuery.class.php';
require_once 'ComcastMediaFileSort.class.php';
require_once 'ComcastRange.class.php';
require_once 'ComcastMediaFileField.class.php';

require_once 'ComcastClient.php';
require_once 'ComcastMediaService.php';


$userName = 'roman.kreichman@kaltura.com'; 
$password = 'Roman1234';

$comcastMediaService = new ComcastMediaService($userName, $password);

$template = new ComcastMediaFileTemplate();
$template->fields[] = array();
$template->fields[] = ComcastMediaFileField::_ID;
$template->fields[] = ComcastMediaFileField::_STOREDFILENAME;

$query = new ComcastQuery();
$query->name = 'ByIDs';
$query->parameterNames = array('IDs');

$ids = new soapval('item', 'IDSet', array(1813810213), false, 'ns12');
$query->parameterValues = array($ids);

$sort = new ComcastMediaFileSort();
$sort->field = ComcastMediaFileField::_ID;
$sort->descending = true;

$range = new ComcastRange();
//$range->startIndex = 1;
//$range->endIndex = 10;

try
{
	$comcastMediaFileList = $comcastMediaService->getMediaFiles($template, $query, $sort, $range);
	var_dump($comcastMediaFileList);
}
catch(Exception $e)
{
	echo "Error: " . $e->getMessage();
	file_put_contents('err.log', $comcastMediaService->getError());
}
file_put_contents('request.xml', $comcastMediaService->request);
file_put_contents('response.xml', $comcastMediaService->responseData);
