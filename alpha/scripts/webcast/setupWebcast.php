<?php
require_once(__DIR__ . '/../bootstrap.php');

$partner=0;

function getDynamicEnumId($enumName, $valueName, $pluginName)
{
	$dynamicEnum = DynamicEnumPeer::retrieveByPluginConstant($enumName, $valueName, $pluginName);
	if ( is_null($dynamicEnum) )
	{
		return null;
	}
	return $dynamicEnum->getId();
}

function retrieveEventNotificationTemplateBySystemName ($systemName, $excludeId = null, $partnerIds = null, PropelPDO $con = null)
{
	$criteria = new Criteria ( EventNotificationTemplatePeer::DATABASE_NAME );
	$criteria->add ( EventNotificationTemplatePeer::SYSTEM_NAME, $systemName );
	if ($excludeId)
	    $criteria->add( EventNotificationTemplatePeer::ID, $excludeId, Criteria::NOT_EQUAL);
	
	// use the partner ids list if given
	if (!$partnerIds)
	{
	    $partnerIds = array (kCurrentContext::getCurrentPartnerId());
	}
	
	$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, array_map('strval',  $partnerIds), Criteria::IN);
	$criteria->addDescendingOrderByColumn(EventNotificationTemplatePeer::PARTNER_ID);
	return EventNotificationTemplatePeer::doSelectOne($criteria);
}


function setWebcastEventNotificationTemplateParams ($systemName, $partner, $apiObjectType, $enumId, $responseProfileSystemName = null)
{
	$eventNotificationTemplate = retrieveEventNotificationTemplateBySystemName($systemName,null,array($partner));
	if(is_null($eventNotificationTemplate))
	{
		KalturaLog::warning("Template $systemName was not found in partner $partner");
		return null;
	}

	$eventNotificationTemplate->setApiObjectType($apiObjectType);
	$eventNotificationTemplate->setObjectFormat(1);

	$notificationParameters = $eventNotificationTemplate->getUserParameters();
	$newNotificationParameters = array();

	if(!is_null($enumId))
	{
		foreach($notificationParameters as $notificationParameter)
		{
			/* @var $notificationParameter kEventNotificationParameter */
			if(!is_null($notificationParameter->getKey()) && 
				($notificationParameter->getKey() === 'trigger_cuepoint' || $notificationParameter->getKey() ===  'cue_point_type')
				)
			{
				$notificationParameter->setValue(new kStringValue($enumId));	
			}
			$newNotificationParameters[] = $notificationParameter;
		}
		$eventNotificationTemplate->setUserParameters($newNotificationParameters);
	}
	else
	{
		KalturaLog::warning('Empty enumId was passed');
	}


	if(!is_null($responseProfileSystemName))
	{
		$responseProfile = ResponseProfilePeer::retrieveBySystemName($responseProfileSystemName);
		if(!is_null($responseProfile))
		{
			$responseProfileId = $responseProfile->getId();
			$eventNotificationTemplate->setResponseProfileId($responseProfileId);
		}
	}

	$eventNotificationTemplate->save();
	return $eventNotificationTemplate->getId();
}

$codeEnumId = getDynamicEnumId('CuePointType', 'Code', 'codeCuePoint');
$thumbEnumId = getDynamicEnumId('CuePointType', 'Thumb', 'thumbCuePoint');
$annotationEnumId = getDynamicEnumId('CuePointType', 'Annotation', 'annotation');

setWebcastEventNotificationTemplateParams('THUMB_CUE_POINT_READY_NOTIFICATION', $partner, 'KalturaThumbCuePoint', $thumbEnumId);
setWebcastEventNotificationTemplateParams('SLIDE_VIEW_CHANGE_CODE_CUE_POINT',   $partner, 'KalturaCodeCuePoint',  $codeEnumId);
setWebcastEventNotificationTemplateParams('POLLS_PUSH_NOTIFICATIONS',           $partner, 'KalturaCodeCuePoint',  $codeEnumId,       'pollVoteResponseProfile');
setWebcastEventNotificationTemplateParams('CODE_QNA_NOTIFICATIONS',             $partner, 'KalturaCodeCuePoint',  $codeEnumId,       'QandA');
setWebcastEventNotificationTemplateParams('PUBLIC_QNA_NOTIFICATIONS',           $partner, 'KalturaAnnotation',    $annotationEnumId, 'QandA');
setWebcastEventNotificationTemplateParams('USER_QNA_NOTIFICATIONS',             $partner, 'KalturaAnnotation',    $annotationEnumId, 'QandA');

KalturaLog::notice('Script Done');
