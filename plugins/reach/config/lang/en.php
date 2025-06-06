<?php

$serviceFeatureNames = ReachPlugin::getServiceFeatureNames();
$englishServiceNames = array();
foreach ($serviceFeatureNames as $serviceFeatureName)
{
	$englishServiceNames["Kaltura_Client_Reach_Enum_VendorServiceFeature::$serviceFeatureName"] = $serviceFeatureName;
}

$englishArray = array(
	'Kaltura_Client_Reach_Enum_VendorServiceType::HUMAN' => 'HUMAN',
	'Kaltura_Client_Reach_Enum_VendorServiceType::MACHINE' => 'MACHINE',

	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::BEST_EFFORT' => 'BEST_EFFORT',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::IMMEDIATE' => 'IMMEDIATE',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::ONE_BUSINESS_DAY' => 'ONE_BUSINESS_DAY',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::TWO_BUSINESS_DAYS' => 'TWO_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::THREE_BUSINESS_DAYS' => 'THREE_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::FOUR_BUSINESS_DAYS' => 'FOUR_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::FIVE_BUSINESS_DAYS' => 'FIVE_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::SIX_BUSINESS_DAYS' => 'SIX_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::SEVEN_BUSINESS_DAYS' => 'SEVEN_BUSINESS_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::THIRTY_MINUTES' => 'THIRTY_MINUTES',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::TWO_HOURS' => 'TWO_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::THREE_HOURS' => 'THREE_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::SIX_HOURS' => 'SIX_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::EIGHT_HOURS' => 'EIGHT_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::TWELVE_HOURS' => 'TWELVE_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::TWENTY_FOUR_HOURS' => 'TWENTY_FOUR_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::FORTY_EIGHT_HOURS' => 'FORTY_EIGHT_HOURS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::FOUR_DAYS' => 'FOUR_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::FIVE_DAYS' => 'FIVE_DAYS',
	'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime::TEN_DAYS' => 'TEN_DAYS',

	'Kaltura_Client_Reach_Type_VendorCatalogItemPricing' => "Default Pricing",
	'Kaltura_Client_Enum_NullableBoolean::FALSE_VALUE' => "FALSE",
	'Kaltura_Client_Enum_NullableBoolean::NULL_VALUE' => "NULL VALUE",
	'Kaltura_Client_Enum_NullableBoolean::TRUE_VALUE' => "TRUE",

	'Kaltura_Client_Reach_Enum_VendorCatalogItemSignLanguageOutputFormat::ASPECT_RATIO_16_9' => "16:9",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemSignLanguageOutputFormat::ASPECT_RATIO_4_3' => "4:3",

	'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat::SRT' => "SRT",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat::DFXP' => "DFXP",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemOutputFormat::VTT' => "VTT",

	'Kaltura_Client_Reach_Enum_VendorTaskProcessingRegion::US' => "US",
	'Kaltura_Client_Reach_Enum_VendorTaskProcessingRegion::EU' => "EU",
	'Kaltura_Client_Reach_Enum_VendorTaskProcessingRegion::CA' => "CA",

	'Kaltura_Client_Reach_Enum_VendorCatalogItemStatus::DEPRECATED' => "Deprecated",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemStatus::DELETED' => "Deleted",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemStatus::ACTIVE' => "Enabled",

	'Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_HOUR' => "Price Per Hour",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_MINUTE' => "Price Per Minute",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_SECOND' => "Price Per Second",
	'Kaltura_Client_Reach_Enum_VendorCatalogItemPriceFunction::PRICE_PER_TOKEN' => "Price Per Token",

	'Kaltura_Client_Reach_Enum_ReachProfileType::FREE_TRIAL' => "Free Trial",
	'Kaltura_Client_Reach_Enum_ReachProfileType::PAID' => "Paid",

	'Kaltura_Client_Reach_Enum_ReachProfileStatus::DISABLED' => "Disabled",
	'Kaltura_Client_Reach_Enum_ReachProfileStatus::DELETED' => "Deleted",
	'Kaltura_Client_Reach_Enum_ReachProfileStatus::ACTIVE' => "Enabled",

	'Kaltura_Client_Reach_Type_TimeRangeVendorCredit' => "Time Ranged Credit",
	'Kaltura_Client_Reach_Type_ReoccurringVendorCredit' => "Recurring Credit",
	'Kaltura_Client_Reach_Type_VendorCredit' => "Generic Credit",
	'Kaltura_Client_Reach_Type_UnlimitedVendorCredit' => "Unlimited Credit",

	'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::MONTHLY' => "MONTHLY",
	'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::DAILY' => "DAILY",
	'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::WEEKLY' => "WEEKLY",
	'Kaltura_Client_Reach_Enum_VendorCreditRecurrenceFrequency::YEARLY' => "YEARLY",

	'Kaltura_Client_Reach_Enum_ReachProfileContentDeletionPolicy::DO_NOTHING' => "DO_NOTHING",
	'Kaltura_Client_Reach_Enum_ReachProfileContentDeletionPolicy::DELETE_ONCE_PROCESSED' => "DELETE_ONCE_PROCESSED",
	'Kaltura_Client_Reach_Enum_ReachProfileContentDeletionPolicy::DELETE_AFTER_WEEK' => "DELETE_AFTER_WEEK",
	'Kaltura_Client_Reach_Enum_ReachProfileContentDeletionPolicy::DELETE_AFTER_MONTH' => "DELETE_AFTER_MONTH",
	'Kaltura_Client_Reach_Enum_ReachProfileContentDeletionPolicy::DELETE_AFTER_THREE_MONTHS' => "DELETE_AFTER_THREE_MONTHS",

	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN' => "English",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN_US' => "English (American)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN_GB' => "English (British)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::NL' => "Dutch",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::FR' => "French",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::DE' => "German",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::IT' => "Italian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ES' => "Spanish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ES_XL' => "Spanish (Latin America)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::AR' => "Arabic",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ZH' => "Chinese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::CMN' => "Mandarin Chinese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::YUE' => "Cantonese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::HE' => "Hebrew",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::HI' => "Hindi",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::JA' => "Japanese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::KO' => "Korean",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::PT' => "Portuguese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::RU' => "Russian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::TR' => "Turkish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::TH' => "Thai",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SV' => "Swedish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::DA' => "Danish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::NO' => "Norwegian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::FI' => "Finnish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::IS' => "Icelandic",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::PL' => "Polish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::IN' => "Indonesian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EL' => "Greek",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::RO' => "Romanian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::HU' => "Hungarian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::GA' => "Irish",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::GD' => "Gaelic (Scottish)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::CY' => "Welsh",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::UR' => "Urdu",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::TA' => "Tamil",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ML' => "Malayalam",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ZU' => "Zulu",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::VI' => "Vietnamese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::UK' => "Ukrainian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::FR_CA' => "French (Canada)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ZH_TW' => "Taiwanese Mandarin",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::PT_BR' => "Portuguese (Brazil)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::CA' => "Catalan",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::CS' => "Czech",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::AF' => "Afrikaans",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::BG' => "Bulgarian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ET' => "Estonian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::LV' => "Latvian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::LT' => "Lithuanian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SK' => "Slovak",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::BA' => "Bashkir",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EU' => "Basque",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::BE' => "Belarusian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::HR' => "Croatian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EO' => "Esperanto",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::FA' => "Farsi",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::GL' => "Galician",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::IA' => "Interlingua",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::MS' => "Malay",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::MR' => "Marathi",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::MN' => "Mongolian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SL' => "Slovenian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::TE' => "Telugu",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::UG' => "Uyghur",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::EN_AU' => "English (Australian)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::AZ' => "Azerbaijani",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::BN' => "Bengali",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::MY' => "Burmese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::BS' => "Bosnian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::KA' => "Georgian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::GU' => "Gujarati",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::JV' => "Javanese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::KN' => "Kannada",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::KK' => "Kazakh",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::KM' => "Khmer",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::LO' => "Lao",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::MK' => "Macedonian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::NE' => "Nepali",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::FA_IR' => "Persian (Iran)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::PA' => "Punjabi",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SR' => "Serbian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SI' => "Sinhala",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SK_SK' => "Slovakian",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SU' => "Sudanese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::SW' => "Swahili",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::TL' => "Tagalog (Filipino)",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::UZ' => "Uzbek",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::XH' => "Xhosa",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ZH_CN' => "Simplified Chinese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::ZH_HK' => "Traditional Chinese",
	'Kaltura_Client_Reach_Enum_CatalogItemLanguage::AUTO_DETECT' => "Auto Detect",

	'Kaltura_Client_Reach_Enum_CatalogItemSignLanguage::ENGLISH_ASL' => "English (ASL)",
	'Kaltura_Client_Reach_Enum_CatalogItemSignLanguage::ENGLISH_BSL' => "English (BSL)",

	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::PENDING' => 'Pending',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::READY' => 'Ready',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::PROCESSING' => 'Processing',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::PENDING_MODERATION' => 'Pending Moderation',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::REJECTED' => 'Rejected',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::ERROR' => 'Error',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::SCHEDULED' => 'Scheduled',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::ABORTED' => 'Aborted',
	'Kaltura_Client_Reach_Enum_EntryVendorTaskStatus::PENDING_ENTRY_READY' => 'Pending Entry Ready',

	'Kaltura_Client_Reach_Enum_VendorCatalogItemStage::PRODUCTION' => 'Production',
	'Kaltura_Client_Reach_Enum_VendorCatalogItemStage::QA' => 'QA',

	'Kaltura_Client_Reach_Enum_VendorVideoAnalysisType::OCR' => 'OCR',
	'Kaltura_Client_Reach_Enum_VendorDocumentEnrichmentType::MD_CONVERSION' => 'Markdown Conversion',
);


return array_merge($englishServiceNames, $englishArray);
