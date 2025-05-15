<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface VendorServiceFeature extends BaseEnum
{
	const CAPTIONS                        = 1;
	const TRANSLATION                     = 2;
	const ALIGNMENT                       = 3;
	const AUDIO_DESCRIPTION               = 4;
	const CHAPTERING                      = 5;
	const INTELLIGENT_TAGGING             = 6;
	const DUBBING                         = 7;
	const LIVE_CAPTION                    = 8;
	const EXTENDED_AUDIO_DESCRIPTION      = 9;
	const CLIPS                           = 10;
	const LIVE_TRANSLATION                = 11;
	const QUIZ                            = 12;
	const SUMMARY                         = 13;
	const VIDEO_ANALYSIS                  = 14;
	const MODERATION                      = 15;
	const METADATA_ENRICHMENT             = 16;
	const SENTIMENT_ANALYSIS              = 17;
}
