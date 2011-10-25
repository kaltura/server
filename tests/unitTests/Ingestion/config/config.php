<?php
/*
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// ===================================================================================================
*/

?>
<?php
	class Config
	{
		const SERVER_URL = "http://kaltura";
		
		const PARTNER_ID = "106";
		const PARTNER_ADMIN_SECRET = "106";
		const PARTNER_USER_ID = 'IngestionTestUser';
		
		const FULL_CONVERSION_PROFILE_NAME = 'Full Conversion Profile';
		
		const ENTRY_NAME = 'ENTRY_NAME';
		const ENTRY_FILE_DATA = 'ENTRY_FILE_DATA';
		const ENTRY_TYPE = 'ENTRY_TYPE';
		
		const TESTS_DATA = 'testsData/';
		
		static $assets = array(
							array(self::ENTRY_NAME => 'Sample Kaltura Animated Logo - Ingestion Test 1',
								self::ENTRY_FILE_DATA => 'Sample Kaltura Animated Logo.flv',
								self::ENTRY_TYPE => KalturaMediaType::VIDEO),
							array(self::ENTRY_NAME => 'Test Audio - Ingestion Test 2',
								self::ENTRY_FILE_DATA => 'Test Audio.mp3',
								self::ENTRY_TYPE => KalturaMediaType::AUDIO),
							array(self::ENTRY_NAME => 'Test Image - Ingestion Test 2',
								self::ENTRY_FILE_DATA => 'Test Image.jpg',
								self::ENTRY_TYPE => KalturaMediaType::IMAGE)
						);
		
	
	}

