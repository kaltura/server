<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create email-notification and custom-data profile on partner 99
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

const FEATURE_KALTURA_LIVE_MONITOR = 'FEATURE_KALTURA_LIVE_MONITOR';

function createMetadataProfile()
{
	$metadataProfile = new MetadataProfile();
	$metadataProfile->setPartnerId(99);
	$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
	$metadataProfile->setName('Live Stream Monitoring');
	$metadataProfile->setSystemName('LiveMonitor');
	$metadataProfile->setDescription('Email notification flag indicating a 24/7 live-entry should be monitored.');
	$metadataProfile->setObjectType(MetadataObjectType::ENTRY);
	$metadataProfile->setRequiredCopyTemplatePermissions(FEATURE_KALTURA_LIVE_MONITOR);
	$metadataProfile->save();
	
	$xsdData = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<xsd:element name="metadata">
		<xsd:complexType>
			<xsd:sequence>
				<xsd:element name="Monitor" minOccurs="1" maxOccurs="1">
					<xsd:annotation>
						<xsd:documentation></xsd:documentation>
						<xsd:appinfo>
							<label>Monitor 24/7</label>
							<key>monitor</key>
							<searchable>true</searchable>
							<description>Send E-mail notification if live-stream broadcast stopped</description>
						</xsd:appinfo>
					</xsd:annotation>
					<xsd:simpleType>
						<xsd:restriction base="listType">
							<xsd:enumeration value="on"/>
							<xsd:enumeration value="off"/>
						</xsd:restriction>
					</xsd:simpleType>
				</xsd:element>
			</xsd:sequence>
		</xsd:complexType>
	</xsd:element>
	<xsd:simpleType name="listType">
		<xsd:restriction base="xsd:string"/>
	</xsd:simpleType>
</xsd:schema>';
	
	$key = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
	kFileSyncUtils::file_put_contents($key, $xsdData);
	
	kMetadataManager::parseProfileSearchFields($metadataProfile->getPartnerId(), $metadataProfile);
}

function createEmailNotification()
{
	$eventConditions = array();
	
	$liveStatusField = new kEvalBooleanField();
	$liveStatusField->setCode('$scope->getEvent()->getObject() instanceof LiveEntry && !$scope->getEvent()->getObject()->canPlayLive() && $scope->getEvent()->getObject()->isCustomDataModified(null, \'mediaServers\')');
	$liveStatusCondition = new kEventFieldCondition();
	$liveStatusCondition->setDescription('Live Status Modified');
	$liveStatusCondition->setField($liveStatusField);
	$eventConditions[] = $liveStatusCondition;
	
	$liveStatusCondition = new kMatchMetadataCondition();
	$liveStatusCondition->setDescription('Metadata monitor field is on');
	$liveStatusCondition->setXPath('Monitor');
	$liveStatusCondition->setProfileSystemName('LiveMonitor');
	$liveStatusCondition->setValues(array('on'));
	$eventConditions[] = $liveStatusCondition;
	
	$toEmailNotificationRecipients = array();
	
	$toEmailNotificationRecipient = new kEmailNotificationRecipient();
	$toEmailNotificationRecipient->setEmail(new kStringValue('kalturasupport@kaltura.com'));
	$toEmailNotificationRecipient->setName(new kStringValue('Kaltura Customer Care'));
	$toEmailNotificationRecipients[] = $toEmailNotificationRecipient;
	
//	$toEmailNotificationEmail = new kEvalStringField();
//	$toEmailNotificationEmail->setCode('$scope->getEvent()->getObject()->getkuser() ? $scope->getEvent()->getObject()->getkuser()->getEmail() : \'\'');	
//	$toEmailNotificationName = new kEvalStringField();
//	$toEmailNotificationName->setCode('$scope->getEvent()->getObject()->getkuser() ? $scope->getEvent()->getObject()->getkuser()->getScreenName() : \'\'');
//	$toEmailNotificationRecipient = new kEmailNotificationRecipient();
//	$toEmailNotificationRecipient->setEmail($toEmailNotificationEmail);
//	$toEmailNotificationRecipient->setName($toEmailNotificationName);
//	$toEmailNotificationRecipients[] = $toEmailNotificationRecipient;
	
	$toEmail = new kEmailNotificationStaticRecipientProvider();
	$toEmail->setEmailRecipients($toEmailNotificationRecipients);

	$contentParameters = array();
	
	$eventNotificationValue = new kEvalStringField();
	$eventNotificationValue->setCode('kConf::get(\'partner_notification_email\')');
	$eventNotificationParameter = new kEventNotificationParameter();
	$eventNotificationParameter->setKey('from_email');
	$eventNotificationParameter->setDescription('kConf: Partner Notification E-Mail');
	$eventNotificationParameter->setValue($eventNotificationValue);
	$contentParameters[] = $eventNotificationParameter;
	
	$eventNotificationValue = new kEvalStringField();
	$eventNotificationValue->setCode('kConf::get(\'partner_notification_name\')');
	$eventNotificationParameter = new kEventNotificationParameter();
	$eventNotificationParameter->setKey('from_name');
	$eventNotificationParameter->setDescription('kConf: Partner Notification Name');
	$eventNotificationParameter->setValue($eventNotificationValue);
	$contentParameters[] = $eventNotificationParameter;
	
	$eventNotificationValue = new kEvalStringField();
	$eventNotificationValue->setCode('$scope->getEvent()->getObject()->getPartnerId()');
	$eventNotificationParameter = new kEventNotificationParameter();
	$eventNotificationParameter->setKey('partner_id');
	$eventNotificationParameter->setDescription('Partner ID');
	$eventNotificationParameter->setValue($eventNotificationValue);
	$contentParameters[] = $eventNotificationParameter;
	
	$eventNotificationValue = new kEvalStringField();
	$eventNotificationValue->setCode('$scope->getEvent()->getObject()->getId()');
	$eventNotificationParameter = new kEventNotificationParameter();
	$eventNotificationParameter->setKey('entry_id');
	$eventNotificationParameter->setDescription('Entry ID');
	$eventNotificationParameter->setValue($eventNotificationValue);
	$contentParameters[] = $eventNotificationParameter;
	
	$eventNotificationValue = new kEvalStringField();
	$eventNotificationValue->setCode('$scope->getEvent()->getObject()->getName()');
	$eventNotificationParameter = new kEventNotificationParameter();
	$eventNotificationParameter->setKey('entry_name');
	$eventNotificationParameter->setDescription('Entry Name');
	$eventNotificationParameter->setValue($eventNotificationValue);
	$contentParameters[] = $eventNotificationParameter;
	
	$emailNotification = new EmailNotificationTemplate();
	$emailNotification->setPartnerId(99);
	$emailNotification->setStatus(EventNotificationTemplateStatus::ACTIVE);
	$emailNotification->setName('Is Live Entry Still Alive');
	$emailNotification->setSystemName('EMAIL_LIVE_ENTRY_NOT_ALIVE');
	$emailNotification->setDescription('Email notification template to be sent when a 24/7 live-entry stopped broadcasting.');
	$emailNotification->setAutomaticDispatchEnabled(true);
	$emailNotification->setEventType(EventNotificationEventType::OBJECT_CHANGED);
	$emailNotification->setObjectType(EventNotificationEventObjectType::ENTRY);
	$emailNotification->setEventConditions($eventConditions);
	$emailNotification->setFormat(EmailNotificationFormat::HTML);
	$emailNotification->setSubject('[Kaltura] - Live-Entry [{partner_id}/{entry_id}] stopped broadcasting.');
	$emailNotification->setBody("Partner ID: {partner_id}<br/>\nEntry ID: {entry_id}<br/>\nEntry Name: {entry_name}<br/>\n");
	$emailNotification->setFromEmail('{from_email}');
	$emailNotification->setFromName('{from_name}');
	$emailNotification->setTo($toEmail);
	$emailNotification->setContentParameters($contentParameters);
	$emailNotification->setRequiredCopyTemplatePermissions(FEATURE_KALTURA_LIVE_MONITOR);
	$emailNotification->save();
}
	
createMetadataProfile();
createEmailNotification();
