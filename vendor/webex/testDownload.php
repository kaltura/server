<?php
require_once(__DIR__ . '/../nusoap/class.nusoap_base.php');
require_once(__DIR__ . '/../nusoap/class.soap_transport_http.php');
require_once(__DIR__ . '/../nusoap/class.xmlschema.php');
require_once(__DIR__ . '/../nusoap/class.wsdl.php');
require_once(__DIR__ . '/../nusoap/class.soapclient.php');
require_once(__DIR__ . '/../nusoap/class.soap_parser.php');
require_once(__DIR__ . '/../nusoap/SoapTypes.php');

require_once(__DIR__ . '/nbr/WebexStorageService.class.php');

$siteId = 0; // webex site id
$username = ''; // webex username
$password = ''; // webex password

$recordId = 30083582; // good id
//$recordId = 36462807; // bad id

$storageService = new WebexStorageService();
$storageService->setDownloadDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'tmp');

$ticket = $storageService->getStorageAccessTicket($siteId, $username, $password);

$file = $storageService->downloadNBRStorageFile($siteId, $recordId, $ticket);
var_dump($file);