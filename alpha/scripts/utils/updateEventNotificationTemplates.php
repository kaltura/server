<?php
if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php updateEventNotificationTemplates.php {systemName} <specificPartner> <dryRun>\n";
	exit;
}
$systemName = $argv[1];
$specificPartnerId = isset($argv[2]) ? $argv[2]: null;
$dryRun = (!isset($argv[$argc-1]) || $argv[$argc-1] != 'realrun');


require_once(__DIR__ . '/../bootstrap.php');

$map = array(
   'kFlowHelper::createBulkUploadLogUrl($scope->getEvent()->getBatchJob())' =>
	   '!is_null($scope->getEvent()->getBatchJob()) ? kFlowHelper::createBulkUploadLogUrl($scope->getEvent()->getBatchJob()) : \'\'',
   'kCurrentContext::getCurrentKsKuser()->getPuserId()' =>
	   '!is_null(kCurrentContext::getCurrentKsKuser()) ? kCurrentContext::getCurrentKsKuser()->getPuserId() : \'\'',
   'entryPeer::retrieveByPk($scope->getEvent()->getObject()->getEntryId())->getName()' =>
	   '!is_null(entryPeer::retrieveByPk($scope->getEvent()->getObject()->getEntryId())) ? entryPeer::retrieveByPk($scope->getObject()->getEntryId())->getName() : \'\'',
   '$scope->getEvent()->getObject()->getentry()->getPartner()->getAdminSecret()' =>
	   '!is_null($scope->getObject()->getentry()->getPartner()) ? $scope->getObject()->getentry()->getPartner()->getAdminSecret() : \'\'',
   '$scope->getEvent()->getObject()->getEntry()->getPartner()->getAdminSecret()'  =>
	   '!is_null($scope->getObject()->getEntry()->getPartner()) ? $scope->getObject()->getEntry()->getPartner()->getAdminSecret() : \'\'',
   'PartnerPeer::retrieveByPK($scope->getEvent()->getObject()->getPartnerId())->getAdminSecret()' =>
	   '!is_null(PartnerPeer::retrieveByPK($scope->getObject()->getPartnerId())) ? PartnerPeer::retrieveByPK($scope->getObject()->getPartnerId())->getAdminSecret() : \'\'',
   'PartnerPeer::retrieveByPK($scope->getObject()->getPartnerId())->getAdminSecret()' =>
	   '!is_null(PartnerPeer::retrieveByPK($scope->getObject()->getPartnerId())) ? PartnerPeer::retrieveByPK($scope->getObject()->getPartnerId())->getAdminSecret() : \'\'',
   '$scope->getEvent()->getBatchJob()->getPartner()->getAdminEmail()' =>
	   '!is_null($scope->getEvent()->getBatchJob()->getPartner()) ? $scope->getEvent()->getBatchJob()->getPartner()->getAdminEmail() : \'\'',
   'kuserPeer::getKuserByPartnerAndUid($scope->getPartnerId(), $scope->getEvent()->getBatchJob()->getData()->getUserId())->getEmail()' =>
	   '!is_null(kuserPeer::getKuserByPartnerAndUid($scope->getPartnerId(), $scope->getEvent()->getBatchJob()->getData()->getUserId())) ? kuserPeer::getKuserByPartnerAndUid($scope->getPartnerId(), $scope->getEvent()->getBatchJob()->getData()->getUserId())->getEmail() : \'\'',
   '$scope->getEvent()->getObject()->getkuser()->getFirstName() . \' \' . $scope->getEvent()->getObject()->getkuser()->getLastName()' =>
	   '!is_null($scope->getObject()->getkuser()) ? $scope->getObject()->getkuser()->getFirstName() . \' \' . $scope->getObject()->getkuser()->getLastName() : \'\'',
   '$scope->getEvent()->getObject()->getkuser()->getEmail()' => '!is_null($scope->getObject()->getkuser()) ? $scope->getObject()->getkuser()->getEmail() : \'\'',
);

function getTemplates($systemName, $specificPartnerId)
{
	$criteria = new Criteria();
	EventNotificationTemplatePeer::setUseCriteriaFilter(false);
	$criteria->add ( EventNotificationTemplatePeer::SYSTEM_NAME, $systemName);
	$criteria->add (EventNotificationTemplatePeer::STATUS, array(EventNotificationTemplateStatus::ACTIVE, EventNotificationTemplateStatus::DISABLED) , Criteria::IN);
	if ($specificPartnerId)
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, $specificPartnerId);

	return EventNotificationTemplatePeer::doSelect($criteria);
}

function updateTemplatesCode($templates, $dryRun)
{
	global $map;
	foreach ($templates as $template)
	{
		$templateId = $template->getId();
		/**
		* @var EventNotificationTemplate $template
		*/
		$contentParams = $template->getContentParameters();
		$templateChanged = false;
		echo "Checking template id".$templateId." contentParams\n";
		foreach ($contentParams as $param)
		{
			/**
			* @var kEventNotificationParameter $param
			*/
			$paramValue = $param->getValue();
			if($paramValue && isset($map[$paramValue->getCode()]))
			{
				$templateChanged = true;
				echo "old value for ". $param->getKey()." is [".print_r($paramValue,true)."]\n";
				$code = $map[$paramValue->getCode()];
				$param->getValue()->setCode($code); //todo
				echo "new value for ". $param->getKey()." is [".print_r($param->getValue(),true)."]\n";
			}
		}

		$eventConditions = $template->getEventConditions();
		echo "Checking template id".$templateId." eventConditions\n";
		foreach ($eventConditions as $eventCondition)
		{
			$field = $eventCondition->getField();
			$eventCondDescription = $eventCondition->getDescription();
			if($field && isset($map[$field->getCode()]))
			{
				$templateChanged = true;
				echo "old code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
				$field->setCode($map[$field->getCode()]);
				echo "new code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
			}
		}
		if($templateChanged && !$dryRun)
		{
			$template->save();
		}
	}
}

$templates = getTemplates($systemName,$specificPartnerId);
if (!$templates)
{
   die ("No templates found\n");
}
updateTemplatesCode($templates, $dryRun);

