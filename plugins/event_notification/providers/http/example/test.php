<?php
if(!isset($argv[1]))
	die('Please specify type to test (first argument)');

if(!isset($argv[2]))
	die('Please specify test URL (second argument)');
	
$type = $argv[1];
$url = $argv[2];
$data = null;

switch($type)
{
	case 'object':
		$data = 'data=O:23:"KalturaHttpNotification":7:{s:6:"object";O:17:"KalturaMediaEntry":56:{s:9:"mediaType";i:1;s:17:"conversionQuality";i:1198950;s:10:"sourceType";i:1;s:18:"searchProviderType";N;s:16:"searchProviderId";N;s:14:"creditUserName";N;s:9:"creditUrl";N;s:9:"mediaDate";N;s:7:"dataUrl";s:89:"http://devtests.kaltura.dev/p/496175/sp/49617500/flvclipper/entry_id/0_ioria6f9/version/0";s:15:"flavorParamsIds";N;s:5:"plays";i:0;s:5:"views";i:0;s:5:"width";N;s:6:"height";N;s:8:"duration";i:0;s:10:"msDuration";i:0;s:12:"durationType";N;s:2:"id";s:10:"0_ioria6f9";s:4:"name";s:12:"tantan_test1";s:11:"description";N;s:9:"partnerId";i:496175;s:6:"userId";s:30:"tan-tan.test114@mailinator.com";s:9:"creatorId";s:30:"tan-tan.test114@mailinator.com";s:4:"tags";N;s:9:"adminTags";N;s:10:"categories";N;s:13:"categoriesIds";N;s:6:"status";i:1;s:16:"moderationStatus";i:6;s:15:"moderationCount";i:0;s:4:"type";i:1;s:9:"createdAt";i:1375294649;s:9:"updatedAt";i:1375294660;s:4:"rank";d:0;s:9:"totalRank";i:0;s:5:"votes";i:0;s:7:"groupId";N;s:11:"partnerData";N;s:11:"downloadUrl";s:82:"http://devtests.kaltura.dev/p/496175/sp/49617500/raw/entry_id/0_ioria6f9/version/0";s:10:"searchText";s:49:"_PAR_ONLY_ _496175_ _MEDIA_TYPE_1|  tantan_test1 ";s:11:"licenseType";i:-1;s:7:"version";i:0;s:12:"thumbnailUrl";s:88:"http://devtests.kaltura.dev/p/496175/sp/49617500/thumbnail/entry_id/0_ioria6f9/version/0";s:15:"accessControlId";i:385732;s:9:"startDate";N;s:7:"endDate";N;s:11:"referenceId";N;s:16:"replacingEntryId";N;s:15:"replacedEntryId";N;s:17:"replacementStatus";i:0;s:16:"partnerSortValue";i:0;s:19:"conversionProfileId";i:1198950;s:11:"rootEntryId";s:10:"0_ioria6f9";s:19:"operationAttributes";a:0:{}s:17:"entitledUsersEdit";s:0:"";s:20:"entitledUsersPublish";s:0:"";}s:15:"eventObjectType";i:1;s:22:"eventNotificationJobId";i:12114;s:10:"templateId";i:336;s:12:"templateName";s:29:"Entry Status Changed - Object";s:18:"templateSystemName";s:32:"HTTP_ENTRY_STATUS_CHANGED_OBJECT";s:9:"eventType";i:3;}';		
		break;
		
	case 'json':
		$data = 'data={"object":{"mediaType":1,"conversionQuality":1198950,"sourceType":1,"searchProviderType":null,"searchProviderId":null,"creditUserName":null,"creditUrl":null,"mediaDate":null,"dataUrl":"http:\/\/devtests.kaltura.dev\/p\/496175\/sp\/49617500\/flvclipper\/entry_id\/0_ooau4f7g\/version\/0","flavorParamsIds":null,"plays":0,"views":0,"width":null,"height":null,"duration":0,"msDuration":0,"durationType":null,"id":"0_ooau4f7g","name":"tantan_test1","description":null,"partnerId":496175,"userId":"tan-tan.test114@mailinator.com","creatorId":"tan-tan.test114@mailinator.com","tags":null,"adminTags":null,"categories":null,"categoriesIds":null,"status":0,"moderationStatus":6,"moderationCount":0,"type":1,"createdAt":1375291924,"updatedAt":1375291926,"rank":0,"totalRank":0,"votes":0,"groupId":null,"partnerData":null,"downloadUrl":"http:\/\/devtests.kaltura.dev\/p\/496175\/sp\/49617500\/raw\/entry_id\/0_ooau4f7g\/version\/0","searchText":"_PAR_ONLY_ _496175_ _MEDIA_TYPE_1|  tantan_test1 ","licenseType":-1,"version":0,"thumbnailUrl":"http:\/\/devtests.kaltura.dev\/p\/496175\/sp\/49617500\/thumbnail\/entry_id\/0_ooau4f7g\/version\/0","accessControlId":385732,"startDate":null,"endDate":null,"referenceId":null,"replacingEntryId":null,"replacedEntryId":null,"replacementStatus":0,"partnerSortValue":0,"conversionProfileId":1198950,"rootEntryId":"0_ooau4f7g","operationAttributes":[],"entitledUsersEdit":"","entitledUsersPublish":""},"eventObjectType":1,"eventNotificationJobId":12085,"templateId":340,"templateName":"Entry Status Changed - JSON","templateSystemName":"HTTP_ENTRY_STATUS_CHANGED_JSON","eventType":3,"objectType":"KalturaHttpNotification"}';
		break;
		
	case 'xml':
		$data = 'data=<notification><objectType>KalturaHttpNotification</objectType><object><objectType>KalturaMediaEntry</objectType><mediaType>1</mediaType><conversionQuality>1198950</conversionQuality><sourceType>1</sourceType><searchProviderType></searchProviderType><searchProviderId></searchProviderId><creditUserName></creditUserName><creditUrl></creditUrl><mediaDate></mediaDate><dataUrl>http://devtests.kaltura.dev/p/496175/sp/49617500/flvclipper/entry_id/0_ooau4f7g/version/0</dataUrl><flavorParamsIds></flavorParamsIds><plays>0</plays><views>0</views><width></width><height></height><duration>0</duration><msDuration>0</msDuration><durationType></durationType><id>0_ooau4f7g</id><name>tantan_test1</name><description></description><partnerId>496175</partnerId><userId>tan-tan.test114@mailinator.com</userId><creatorId>tan-tan.test114@mailinator.com</creatorId><tags></tags><adminTags></adminTags><categories></categories><categoriesIds></categoriesIds><status>1</status><moderationStatus>6</moderationStatus><moderationCount>0</moderationCount><type>1</type><createdAt>1375291924</createdAt><updatedAt>1375291932</updatedAt><rank>0</rank><totalRank>0</totalRank><votes>0</votes><groupId></groupId><partnerData></partnerData><downloadUrl>http://devtests.kaltura.dev/p/496175/sp/49617500/raw/entry_id/0_ooau4f7g/version/0</downloadUrl><searchText>_PAR_ONLY_ _496175_ _MEDIA_TYPE_1|  tantan_test1 </searchText><licenseType>-1</licenseType><version>0</version><thumbnailUrl>http://devtests.kaltura.dev/p/496175/sp/49617500/thumbnail/entry_id/0_ooau4f7g/version/0</thumbnailUrl><accessControlId>385732</accessControlId><startDate></startDate><endDate></endDate><referenceId></referenceId><replacingEntryId></replacingEntryId><replacedEntryId></replacedEntryId><replacementStatus>0</replacementStatus><partnerSortValue>0</partnerSortValue><conversionProfileId>1198950</conversionProfileId><rootEntryId>0_ooau4f7g</rootEntryId><operationAttributes></operationAttributes><entitledUsersEdit></entitledUsersEdit><entitledUsersPublish></entitledUsersPublish></object><eventObjectType>1</eventObjectType><eventNotificationJobId>12091</eventNotificationJobId><templateId>341</templateId><templateName>Entry Status Changed - XML</templateName><templateSystemName>HTTP_ENTRY_STATUS_CHANGED_XML</templateSystemName><eventType>3</eventType></notification>';
		break;
		
	case 'fields':
		$fields = array(
			'test1' => 123,
			'test2' => 'abc',
		);
		$data = http_build_query($fields, null, '&');
		break;
		
	case 'text':
		$data = "Test free text, may contain = sign\nIt may also contain new lines";
		break;
		
	default: 
		die("Type [$type] is not supported, use one of the following: object, json, xml, fields, text");
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

var_dump($response);
