<?php
/**
 * Update media space email notification templates to use bcc instead of to
 */

require_once (__DIR__ . '/../../bootstrap.php');

$criteria = new Criteria();
$criteria->add(EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::DELETED, Criteria::NOT_EQUAL);
$criteria->add(EventNotificationTemplatePeer::SYSTEM_NAME, array('New_Item_Pending_Moderation', 'New_Item_Pending_Moderation_2', 'Entry_Was_Added_To_Channel'), Criteria::IN);

$criteria->addAscendingOrderByColumn(EventNotificationTemplatePeer::ID);
$criteria->setLimit(100);

$eventNotificationTemplates = EventNotificationTemplatePeer::doSelect($criteria);
$lastId = 0;
$count = 0;
while($eventNotificationTemplates){
	foreach($eventNotificationTemplates as $eventNotificationTemplate)
	{
		/* @var $eventNotificationTemplate EmailNotificationTemplate */
		
		$lastId = $eventNotificationTemplate->getId();

		$categoryId = new kEvalStringField();
		$categoryId->setCode('$scope->getEvent()->getObject()->getCategoryId()');
		$categoryUserFilter = new categoryKuserFilter();		
		if($eventNotificationTemplate->getSystemName() == 'Entry_Was_Added_To_Channel')
		{
			$categoryUserFilter->set('_matchor_permission_names', 'CATEGORY_SUBSCRIBE');
		}
		else
		{
			$categoryUserFilter->set('_matchor_permission_names', 'CATEGORY_MODERATE');
		}
		$bcc = new kEmailNotificationCategoryRecipientProvider();
		$bcc->setCategoryId($categoryId);
		$bcc->setCategoryUserFilter($categoryUserFilter);
		$eventNotificationTemplate->setBcc($bcc);
		$eventNotificationTemplate->setTo(null);
		$eventNotificationTemplate->save();
		$count++;
	}
	
	$criteria->add(EventNotificationTemplatePeer::ID, $lastId, Criteria::GREATER_THAN);
	$eventNotificationTemplates = EventNotificationTemplatePeer::doSelect($criteria);
}
KalturaLog::log('Done: updated '.$count.' templates');

