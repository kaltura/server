<?php
class myMarketoUtils
{
	public static function sendRegistrationInformation($partner , $force_send = false )
	{
		if (!$force_send)
		{
			// this is for testing - use this only from .com NOT from all other environments
			if (kConf::get("www_host") !== "www.kaltura.com")
				return;
		}
		
		try 
		{
			$accessKey = kConf::get('marketo_access_key');
			$secretKey = kConf::get('marketo_secret_key');
			$soapEndPoint = 'https://na-g.marketo.com/soap/mktows/1_3';
			$marketo = new MarketoApiService($soapEndPoint.'?WSDL', array('location' => $soapEndPoint));
			$marketo->setCredentials($accessKey, $secretKey);
			
			$realDescription = '';
			$description = $partner->getDescription();
			if (substr_count($partner->getDescription(), 'KMC_SIGNUP|'))
			{
				$realDescription = str_replace('KMC_SIGNUP|', '', $partner->getDescription());
				$description = 'KMC_SIGNUP';
			}
			elseif (substr_count($partner->getDescription(), "\nWordpress all-in-one plugin"))
			{
				$str_index = strpos($partner->getDescription(), "\nWordpress all-in-one plugin");
				$description = str_replace("\n", '', substr($partner->getDescription(), $str_index));
				$realDescription = substr($partner->getDescription(), 0, $str_index);
			}
			
			$leadRecord = new LeadRecord();
			
			$leadRecord->Email = $partner->getAdminEmail();
			$leadRecord->leadAttributeList = new stdClass();
			
			$attributes = array();
			
			$attributes[] = $marketo->createAttribute('LastName', $partner->getAdminName());
			$attributes[] = $marketo->createAttribute('Kaltura_Partner_ID__c', $partner->getId());
			$attributes[] = $marketo->createAttribute('Company', $partner->getName());
			$attributes[] = $marketo->createAttribute('Kaltura_Partner_Activation_Type__c', $description);
			$attributes[] = $marketo->createAttribute('Kaltura_Content_Category__c', $partner->getContentCategories());
			$attributes[] = $marketo->createAttribute('Phone', $partner->getPhone());
			$attributes[] = $marketo->createAttribute('Description_Review__c', $realDescription);
			$attributes[] = $marketo->createAttribute('Kaltura_Describe_Yourself__c', $partner->getDescribeYourself());
			$attributes[] = $marketo->createAttribute('Website', $partner->getUrl1());
			
			$leadRecord->leadAttributeList->attribute = $attributes;
			
			$params = new ParamsSyncLead();
			$params->leadRecord = $leadRecord;
			
			$marketo->syncLead($params);
		}
		catch(Exception $ex)
		{
			KalturaLog::log("An error occured while creating a lead in Marketo " . $ex->getMessage());
		}
	}
}
?>