<?php

date_default_timezone_set('America/New_York');

require_once 'C:\web\kaltura\infra\nusoap\nusoap.php';
require_once 'C:\web\kaltura\infra\nusoap\SoapTypes.php';

require_once 'ComcastClient.php';
require_once 'ComcastTypes.php';
require_once 'ComcastMediaService.php';


$userName = 'roman.kreichman@kaltura.com'; 
$password = 'Roman1234';

$comcastMediaService = new ComcastMediaService($userName, $password);

$ids = array(1764309279);

$deletedIds = $comcastMediaService->deleteMedia($ids);
file_put_contents('err.log', $comcastMediaService->getError());
file_put_contents('request.xml', $comcastMediaService->request);
file_put_contents('response.xml', $comcastMediaService->responseData);

var_dump($deletedIds);
