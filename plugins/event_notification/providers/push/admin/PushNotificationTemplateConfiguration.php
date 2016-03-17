<?php
/**
 * @package plugins.pushNotification
* @subpackage admin
*/
class Form_PushNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
    protected function addTypeElements(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
    {
        $element = new Infra_Form_Html('http_title', array(
            'content' => '<b>Notification Handler Service  Details</b>',
        ));
        $this->addElements(array($element));
        
        $this->addElement('select', 'api_object_type', array(
            'label'			=> 'Object Type (KalturaObject):',
 			'default'       => $eventNotificationTemplate->apiObjectType,
            'filters'		=> array('StringTrim'),
            'required'		=> true,            
            'multiOptions' 	=> array(
                'KalturaBaseEntry' => 'Base Entry',
                'KalturaDataEntry' => 'Data Entry',
                'KalturaDocumentEntry' => 'Document Entry',
                'KalturaMediaEntry' => 'Media Entry',
                'KalturaExternalMediaEntry' => 'External Media Entry',
                'KalturaLiveStreamEntry' => 'Live Stream Entry',
                'KalturaPlaylist' => 'Playlist',
                'KalturaCategory' => 'Category',
                'KalturaUser' => 'User',
                'KalturaCuePoint' => 'CuePoint',
                'KalturaAdCuePoint' => 'Ad Cue-Point',
                'KalturaAnnotation' => 'Annotation',
                'KalturaCodeCuePoint' => 'Code Cue-Point',
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
            ),
        ));
    
        $this->addElement('select', 'object_format', array(
            'label'			=> 'Format:',
            'filters'		=> array('StringTrim'),
            'required'		=> true,
            'multiOptions' 	=> array(
                Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_JSON => 'JSON',
                Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_XML => 'XML',
                Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_PHP => 'PHP',
            ),
        ));

        $responseProfile = new Kaltura_Form_Element_ObjectSelect('response_profile_id', array(
        	'label' => 'Response Profile:',
        	'nameAttribute' => 'name',
        	'service' => 'responseProfile',
        	'pageSize' => 500,
        	'impersonate' => $eventNotificationTemplate->partnerId,
        ));
        $this->addElements(array($responseProfile));
    }    
}