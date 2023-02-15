<?php

class Form_KafkaNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::populateFromObject()
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		if (!($object instanceof Kaltura_Client_KafkaNotification_Type_KafkaNotificationTemplate))
			return;
		
		$this->addElement('text', 'topic_name', array(
			'label' => 'Topic Name: ',
			'filters' => array('StringTrim'),
			'readonly' => true,
		));
		
		$this->addElement('text', 'partition_key', array(
			'label' => 'partitionKey: ',
			'filters' => array('StringTrim'),
			'readonly' => true,
		));
		
		$format = new Kaltura_Form_Element_EnumSelect('message_format', array(
			'enum' => 'Kaltura_Client_KafkaNotification_Enum_KafkaNotificationFormat',
			'label' => 'Format:',
			'filters' => array('StringTrim'),
			'required' => true,
		));
		$this->addElements(array($format));
		
		$this->addElement('text', 'api_object_type', array(
			'label' => 'api Object Type:',
			'filters' => array('StringTrim'),
			'readonly' => true,
		));
		
		$this->addDisplayGroup(array('topic_name', 'partition_key', 'api_object_type', 'message_format'),
			'kafka_config',
			array(
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'frmAutomaticConfig'))),
				'legend' => 'Kafka Config',
			));
		
		parent::populateFromObject($object, $add_underscore);
		
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
	{
		$element = new Infra_Form_Html('http_title', array(
			'content' => '<b>Notification Handler Service  Details</b>',
		));
		$this->addElements(array($element));
		
		$this->addElement('select', 'api_object_type', array(
			'label' => 'Object Type (KalturaObject):',
			'default' => $eventNotificationTemplate->apiObjectType,
			'filters' => array('StringTrim'),
			'required' => true,
			'multiOptions' => array(
				'KalturaBaseEntry' => 'Base Entry',
				'KalturaDataEntry' => 'Data Entry',
				'KalturaDocumentEntry' => 'Document Entry',
				'KalturaMediaEntry' => 'Media Entry',
				'KalturaExternalMediaEntry' => 'External Media Entry',
				'KalturaLiveStreamEntry' => 'Live Stream Entry',
				'KalturaPlaylist' => 'Playlist',
				'KalturaCategory' => 'Category',
				'KalturaUser' => 'User',
				'KalturaGroupUser' => 'Group user',
				'KalturaCuePoint' => 'CuePoint',
				'KalturaAdCuePoint' => 'Ad Cue-Point',
				'KalturaAnnotation' => 'Annotation',
				'KalturaCodeCuePoint' => 'Code Cue-Point',
				'KalturaThumbCuePoint' => 'Thumb Cue-Point',
				'KalturaDistributionProfile' => 'Distribution Profile',
				'KalturaEntryDistribution' => 'Entry Distribution',
				'KalturaMetadata' => 'Metadata',
				'KalturaAsset' => 'Asset',
				'KalturaFlavorAsset' => 'Flavor Asset',
				'KalturaThumbAsset' => 'Thumbnail Asset',
				'KalturaAccessControlProfile' => 'Access Control',
				'KalturaBatchJob' => 'BatchJob',
				'KalturaBulkUploadResultEntry' => 'Bulk-Upload Entry Result',
				'KalturaBulkUploadResultCategory' => 'Bulk-Upload Category Result',
				'KalturaBulkUploadResultUser' => 'Bulk-Upload User Result',
				'KalturaBulkUploadResultCategoryUser' => 'Bulk-Upload Category - User Result',
				'KalturaCategoryUser' => 'Category - User',
				'KalturaConversionProfile' => 'Conversion Profile',
				'KalturaFlavorParams' => 'Flavor Params',
				'KalturaConversionProfileAssetParams' => 'Asset Params - Conversion Profile',
				'KalturaFlavorParamsOutput' => 'Flavor Params Output',
				'KalturaGenericsynDicationFeed' => 'Genericsyn Dication Feed',
				'KalturaPartner' => 'Partner',
				'KalturaPermission' => 'Permission',
				'KalturaPermissionItem' => 'Permission Item',
				'KalturaScheduler' => 'Scheduler',
				'KalturaSchedulerConfig' => 'Scheduler Config',
				'KalturaSchedulerStatus' => 'Scheduler Status',
				'KalturaSchedulerWorker' => 'Scheduler Worker',
				'KalturaStorageProfile' => 'Storage Profile',
				'KalturaThumbParams' => 'Thumbnail Params',
				'KalturaThumbParamsOutput' => 'Thumbnail Params Output',
				'KalturaUploadToken' => 'Upload Token',
				'KalturaUserLoginData' => 'User Login Data',
				'KalturaUserRole' => 'User Role',
				'KalturaWidget' => 'Widget',
				'KalturaCategoryEntry' => 'Category - Entry',
				'KalturaLiveStreamScheduleEvent' => 'Schedule Live-Stream Event',
				'KalturaRecordScheduleEvent' => 'Schedule Recorded Event',
				'KalturaLocationScheduleResource' => 'Schedule Location Resource',
				'KalturaLiveEntryScheduleResource' => 'Schedule Live-Entry Resource',
				'KalturaCameraScheduleResource' => 'Schedule Camera Resource',
				'KalturaScheduleEventResource' => 'Schedule Event-Resource',
				'KalturaClippingTaskEntryServerNode' => 'Clipping Task Entry-Server-Node',
				'KalturaVirtualEvent' => 'Virtual Event',
			),
		));

		$responseProfile = new Kaltura_Form_Element_ObjectSelect('response_profile_system_name', array(
			'label' => 'Response Profile:',
			'nameAttribute' => 'name',
			'service' => 'responseProfile',
			'pageSize' => 500,
			'impersonate' => $eventNotificationTemplate->partnerId,
			'addNull' => true,
		));
		$this->addElements(array($responseProfile));
	}
}