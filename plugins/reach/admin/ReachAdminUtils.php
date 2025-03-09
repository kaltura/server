<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachAdminUtils
{
	public static function getCatalogItemObjectNames($serviceFeature)
	{
		switch ($serviceFeature)
		{
			case Kaltura_Client_Reach_Enum_VendorServiceFeature::CAPTIONS:
				return array('VendorCaptionsCatalogItem', 'VendorCaptionsCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::TRANSLATION:
				return array('VendorTranslationCatalogItem', 'VendorTranslationCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::ALIGNMENT:
				return array('VendorAlignmentCatalogItem', 'VendorAlignmentCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::AUDIO_DESCRIPTION:
				return array('VendorAudioDescriptionCatalogItem', 'VendorAudioDescriptionCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::EXTENDED_AUDIO_DESCRIPTION:
				return array('VendorExtendedAudioDescriptionCatalogItem', 'VendorExtendedAudioDescriptionCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::CHAPTERING:
				return array('VendorChapteringCatalogItem', 'VendorChapteringCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::INTELLIGENT_TAGGING:
				return array('VendorIntelligentTaggingCatalogItem', 'VendorIntelligentTaggingCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::DUBBING:
				return array('VendorDubbingCatalogItem', 'VendorDubbingCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_CAPTION:
				return array('VendorLiveCaptionCatalogItem', 'VendorLiveCaptionCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::LIVE_TRANSLATION:
				return array('VendorLiveTranslationCatalogItem', 'VendorLiveTranslationCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::CLIPS:
				return array('VendorClipsCatalogItem', 'VendorClipsCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::QUIZ:
				return array('VendorQuizCatalogItem', 'VendorQuizCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::SUMMARY:
				return array('VendorSummaryCatalogItem', 'VendorSummaryCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::VIDEO_ANALYSIS:
				return array('VendorVideoAnalysisCatalogItem', 'VendorVideoAnalysisCatalogItemFilter');

			case Kaltura_Client_Reach_Enum_VendorServiceFeature::MODERATION:
				return array('VendorModerationCatalogItem', 'VendorModerationCatalogItemFilter');
		}
		return array('VendorCatalogItem', 'VendorCatalogItemFilter');
	}

	public static function getCatalogItemTypeName($serviceFeature)
	{
		list($catalogItemName, ) = ReachAdminUtils::getCatalogItemObjectNames($serviceFeature);
		return "Kaltura_Client_Reach_Type_$catalogItemName";
	}

	public static function getCatalogItemFilterTypeName($serviceFeature)
	{
		list(, $catalogItemFilterName) = ReachAdminUtils::getCatalogItemObjectNames($serviceFeature);
		return "Kaltura_Client_Reach_Type_$catalogItemFilterName";
	}

	public static function getCatalogItemFilter($serviceFeature)
	{
		$catalogItemFilterName = ReachAdminUtils::getCatalogItemFilterTypeName($serviceFeature);
		return new $catalogItemFilterName();
	}
}
