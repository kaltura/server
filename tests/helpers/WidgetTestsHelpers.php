<?php
class WidgetTestsHelpers
{
	static function createDummyWidget($uiConfId = null)
	{
	    $widgetService = KalturaTestsHelpers::getServiceInitializedForAction("widget", "add", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getNormalKs());   
		return $widgetService->addAction(self::prepareWidget($uiConfId));
	}
	
	static function getUiConfId()
	{
		return self::createUiConf()->id;
	}
	
	static function createUiConf()
	{
	    $uiConfService = KalturaTestsHelpers::getServiceInitializedForAction("uiconf", "add", KalturaTestsHelpers::getPartner()->getId(), null, KalturaTestsHelpers::getAdminKs());   
		return $uiConfService->addAction(self::prepareUiConf());
	}
	
	static function prepareUiConf($uiConf = null)
	{
		if(is_null($uiConf))
		{
			$uiConf = new KalturaUiConf();
			$uiConf->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$uiConf->objTypeAsString = KalturaTestsHelpers::getRandomString(10);
			$uiConf->createdAt = time();
			$uiConf->updatedAt = time();
		}
		else
		{
			$uiConf->partnerId = null;
			$uiConf->objTypeAsString = null;
			$uiConf->confFilePath = null;
			$uiConf->createdAt = null;
			$uiConf->updatedAt = null;
		}

		$uiConf->objType = KalturaUiConfObjType::PLAYER;
		$uiConf->creationMode = KalturaUiConfCreationMode::WIZARD;
		$uiConf->name = KalturaTestsHelpers::getRandomString(10);
		$uiConf->description = KalturaTestsHelpers::getRandomString(10);
		$uiConf->width = KalturaTestsHelpers::getRandomNumber(100, 300);
		$uiConf->height = KalturaTestsHelpers::getRandomNumber(100, 300);
		$uiConf->htmlParams = KalturaTestsHelpers::getRandomString(10);
		$uiConf->swfUrl = KalturaTestsHelpers::getRandomString(10);
		$uiConf->confFile = KalturaTestsHelpers::getRandomString(10);
		$uiConf->confFileFeatures = KalturaTestsHelpers::getRandomString(10);
		$uiConf->confVars = KalturaTestsHelpers::getRandomString(10);
		$uiConf->useCdn = 0;
		$uiConf->tags = KalturaTestsHelpers::getRandomString(10) . ',' . KalturaTestsHelpers::getRandomString(10);
		$uiConf->swfUrlVersion = KalturaTestsHelpers::getRandomString(10);
		
		return $uiConf;
	}
	
	static function prepareWidget($uiConfId = null, $widget = null)
	{
		if(is_null($uiConfId))
			$uiConfId = self::getUiConfId();
			
		if(is_null($widget))
		{
			$widget = new KalturaWidget();
			$widget->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$widget->createdAt = time();
			$widget->updatedAt = time();
		}
		else
		{
			$widget = clone $widget;
			
			$widget->id = null;
			$widget->partnerId = null;
			$widget->createdAt = null;
			$widget->updatedAt = null;
			$widget->widgetHTML = null;
		}
		
		$widget->uiConfId = $uiConfId;
		$widget->entryId = MediaTestsHelpers::getDummyEntryId();
		$widget->securityType = KalturaWidgetSecurityType::NONE;
		$widget->securityPolicy = 0;
		$widget->partnerData = KalturaTestsHelpers::getRandomString(10);
		
		return $widget;
	}
	
	static function assertUiConf(KalturaUiConf $expected, $actual)
	{
		PHPUnit_Framework_Assert::assertType("KalturaUiConf", $actual);
		
		PHPUnit_Framework_Assert::assertEquals($expected->partnerId, $actual->partnerId);
		PHPUnit_Framework_Assert::assertEquals($expected->name, $actual->name);
		PHPUnit_Framework_Assert::assertEquals($expected->description, $actual->description);
		PHPUnit_Framework_Assert::assertEquals($expected->objType, $actual->objType);
//		PHPUnit_Framework_Assert::assertEquals($expected->objTypeAsString, $actual->objTypeAsString);
		PHPUnit_Framework_Assert::assertEquals($expected->width, $actual->width);
		PHPUnit_Framework_Assert::assertEquals($expected->height, $actual->height);
		PHPUnit_Framework_Assert::assertEquals($expected->htmlParams, $actual->htmlParams);
//		PHPUnit_Framework_Assert::assertEquals($expected->swfUrl, $actual->swfUrl);
//		PHPUnit_Framework_Assert::assertEquals($expected->confFilePath, $actual->confFilePath);
		PHPUnit_Framework_Assert::assertEquals($expected->confFile, $actual->confFile);
		PHPUnit_Framework_Assert::assertEquals($expected->confFileFeatures, $actual->confFileFeatures);
		PHPUnit_Framework_Assert::assertEquals($expected->confVars, $actual->confVars);
		PHPUnit_Framework_Assert::assertEquals($expected->useCdn, $actual->useCdn);
		PHPUnit_Framework_Assert::assertEquals($expected->tags, $actual->tags);
		PHPUnit_Framework_Assert::assertEquals($expected->swfUrlVersion, $actual->swfUrlVersion);
		PHPUnit_Framework_Assert::assertEquals($expected->createdAt, $actual->createdAt);
		PHPUnit_Framework_Assert::assertEquals($expected->creationMode, $actual->creationMode);
	}
	
	static function assertWidget(KalturaWidget $expected, $actual)
	{
		PHPUnit_Framework_Assert::assertType("KalturaWidget", $actual);
		
		PHPUnit_Framework_Assert::assertEquals($expected->partnerId, $actual->partnerId);
		PHPUnit_Framework_Assert::assertEquals($expected->entryId, $actual->entryId);
		
		PHPUnit_Framework_Assert::assertEquals($expected->securityType, $actual->securityType);
		PHPUnit_Framework_Assert::assertEquals($expected->partnerData, $actual->partnerData);
	} 
}