<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_ENV', 'development');
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../vendor/ZendFramework/library'),
	get_include_path(),
)));
require_once 'Zend/Application.php';
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

// the needed resources
$frontController = $application->getBootstrap()->getResource('FrontController');
$view = $application->getBootstrap()->getResource('View');

// init request
$request = new Zend_Controller_Request_Http();
$request->setControllerName('partner-usage');
$request->setActionName('export-csv');
$fromDate = new Zend_Date();
$fromDate->setHour(0);
$fromDate->setMinute(0);
$fromDate->setSecond(0);
$fromDate->setDay(1);
$fromDate->addMonth(-1);
$request->setParam('from_date', $fromDate->getTimestamp()); // beginning of last month
$toDate = new Zend_Date($fromDate);
$toDate->addMonth(1);
$toDate->addSecond(-1);
$request->setParam('to_date', $toDate->getTimestamp()); // end of last month

// init response
$response = new Zend_Controller_Response_Cli();

// dispatch
$frontController->getDispatcher()->dispatch($request, $response);

// send mail
$config = Zend_Registry::get('config');
$sentToArray = explode(',', $config->settings->monthlyUsageSendTo);
$mail = new Zend_Mail();
$mail->setSubject($view->translate('Monthly Report'));
$mail->setFrom($config->settings->monthlyUsageSendFrom);
$mail->setBodyText($view->translate('CSV file attached.'));

// the attachment
$attachment = new Zend_Mime_Part($response->getBody());
$attachment->type = 'text/csv';
$attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
$attachment->encoding = Zend_Mime::ENCODING_BASE64;
$dateFormat = $view->translate('csv date');
$attachment->filename = 'Usage report '.$fromDate->toString($dateFormat).' to '.$toDate->toString($dateFormat).'.csv';

$mail->addAttachment($attachment);

if (count($sentToArray) > 0)
{
	foreach($sentToArray as $to)
	{
		$mail->addTo(trim($to));
	}
	
	$mail->send();
}