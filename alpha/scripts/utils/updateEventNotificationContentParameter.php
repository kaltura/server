<?php
if($argc < 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php updateEventNotificationContentParameter.php [templateId] [partner_id]\n";
	exit;
}
$templateId = $argv[1];
$partnerId = isset($argv[2]) ? $argv[2]: null;
echo "[START] for partner $partnerId with template id $templateId \n";

require_once(__DIR__ . '/../bootstrap.php');

$criteria = new Criteria();
EventNotificationTemplatePeer::setUseCriteriaFilter(false);
$criteria->add ( EventNotificationTemplatePeer::ID, $templateId);
$criteria->add ( EventNotificationTemplatePeer::PARTNER_ID, $partnerId);
$criteria->add (EventNotificationTemplatePeer::STATUS, array(EventNotificationTemplateStatus::ACTIVE, EventNotificationTemplateStatus::DISABLED) , Criteria::IN);
try{
	$templates = EventNotificationTemplatePeer::doSelect($criteria);
	if(!isset($templates[0])) {
		throw new Exception("cannot find template");
	}
	$template = $templates[0];
	/**
	 * @var EventNotificationTemplate $template
	 */
	$templateId = $template->getId();
	$contentParams = (array)$template->getContentParameters();
	if(empty($contentParams)) {
		throw new Exception("no contentParams found");
	}
	$kEventNotificationParameter = new kEventNotificationParameter();
	$kEventNotificationParameter->setKey('entry_id');
	$kEventNotificationParameter->setValue(new kEvalStringField());
	$kEventNotificationParameter->setDescription('Entry id');
	$contentParams[] = $kEventNotificationParameter;
	$template->setContentParameters($contentParams);
	$rowAffected = $template->save();
	echo "[SUCCESS] for partner $partnerId with template id $templateId, rowAffected: $rowAffected \n";
}
catch(Exception $e) {
	echo "[FAILURE] for partner $partnerId with template id $templateId, Msg:".$e->getMessage() . "\n";
}
