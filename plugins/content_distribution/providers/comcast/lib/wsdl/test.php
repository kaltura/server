<?php

date_default_timezone_set('America/New_York');

require_once 'C:\web\kaltura\infra\nusoap\nusoap.php';
require_once 'C:\web\kaltura\infra\nusoap\SoapTypes.php';

require_once 'ComcastClient.php';
require_once 'ComcastTypes.php';
require_once 'ComcastMediaService.php';


$userName = 'roman.kreichman@kaltura.com'; 
$password = 'Roman1234';

$mediaService = new ComcastMediaService($userName, $password);

$entryId = '0_myTest02';
$entryDuration = 300;
$thumbnailURL = 'http://...'; //72x92
$entryTitle = 'The test title';
$entryDescription = 'The test description';

$flavorAsset1_Bitrate = 400;
$flavorAsset1_Duration = 300;
$flavorAsset1_URL = 'http://...';
$flavorAsset1_Width = 480;
$flavorAsset1_Height = 650;

$flavorAsset2_Bitrate = 600;
$flavorAsset2_Duration = 300;
$flavorAsset2_URL = 'http://...';
$flavorAsset2_Width = 480;
$flavorAsset2_Height = 650;

$media = new ComcastMedia();
	
//$media->ID; // important for updates
//$media->template = new ComcastArrayOfMediaField();

$media->album = 'Fox Sports';
$media->airdate = time();
$media->author = 'Fox Sports';
$media->availableDate = time();
$media->categories = new ComcastArrayOfstring();
$media->categories[] = 'TV Full Episode';
$media->contentType = ComcastContentType::_VIDEO;
$media->copyright = '2004 FOX Interactive Television, LLC';
$media->expirationDate = time() + (60 * 60 * 24 * 30); // 30 days
$media->externalID = $entryId;
$media->formats = new ComcastArrayOfFormat();
$media->formats = ComcastFormat::_FLV;
$media->keywords = 'Fox Sports';
$media->language = ComcastLanguage::_ENGLISH;
$media->length = $entryDuration;
$media->rating = 'TV-14';
$media->thumbnailURL = $thumbnailURL; //72x92
$media->title = $entryTitle;
$media->description = $entryDescription;
$media->customData = new ComcastCustomData();

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Brand';
$customDataElement->value = 'HBO';
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'providerExternalId'; //Required, literal string -->
$customDataElement->value = '90745c5d4e00'; //Required, any single text/numeric string, must be unique to each individual asset -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Video Content Type'; //Required, literal string -->
$customDataElement->value = 'Long Form'; //Required, value values are Long Form OR Short Form, Long Form is to be applied to TV Full Episodes or Movie Full Feature, all other categories must be mapped to Short Form -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Headline'; //Required for Short Form assets -->
$customDataElement->value = 'Season 1, Episode 5'; //Required for Short Form assets, a plain text field that should mirror the title field -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Link Text'; //Required for Comcast.net assets -->
$customDataElement->value = '<![CDATA[E! News Now]]>'; //Required for .net assets, a plain text field that descripbes the link href below -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Link Href'; //Required for Comcast.net assets -->
$customDataElement->value = 'http://www.enews.com'; //Required for .net assets, should be the URL that your logo on the video channel directs to -->
$media->customData[] = $customDataElement;

//TV Series Matching ID -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Series ID'; //Required for TV, literal string -->
$customDataElement->value = '1975'; //Required for TV, The cPlatform, TMS, Baseline, IMDB or AMG series matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Series ID Type'; //Required for TV, literal string -->
$customDataElement->value = 'cPlatform'; //Required for TV, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Series Name'; //Required for TV, literal string -->
$customDataElement->value = 'The Sopranos'; //Required for TV, a plain text field -->
$media->customData[] = $customDataElement;

//TV Season Matching ID -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Season Number'; //Optional for TV, literal string -->
$customDataElement->value = '6'; //Optional for TV, any integer value -->
$media->customData[] = $customDataElement;

//TV Episode Matching ID -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Episode ID'; //Required for TV, literal string -->
$customDataElement->value = '34764'; //Required for TV, The cPlatform, TMS, Baseline, IMDB or AMG episode matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Episode ID Type'; //Required for TV, literal string -->
$customDataElement->value = 'cPlatform'; //Required for TV, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Episode Name'; //Required for TV, literal string -->
$customDataElement->value = 'Made In America'; //Required for TV, a plain text field -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Episode Number'; //Required for TV, literal string -->
$customDataElement->value = '6'; //Required for TV, any integer value -->
$media->customData[] = $customDataElement;

//Movie Matching ID -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Movie ID'; //Required for Movie, literal string -->
$customDataElement->value = '545454'; //Required for Movie, The cPlatform, TMS, Baseline, IMDB or AMG episode matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Movie ID Type'; //Required for Movie, literal string -->
$customDataElement->value = 'cPlatform'; //Required for Movie, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Movie Name'; //Required for Movie, literal string -->
$customDataElement->value = 'The Sopranos Made Up Movie'; //Required for Movie, a plain text field -->
$media->customData[] = $customDataElement;

//Advertising -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'C3 Airdate'; //Optional, literal string when an asset is to follow C3 behavior -->
$customDataElement->value = '2009-06-22T20:00:00-04'; //Optional, the linear airdate and time of the C3 asset, also the beginning of the C3 window. -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'BlackoutStartTimes'; //Optional, literal string must be present when an asset follows C3 behavior -->
$customDataElement->value = '270540,744780,1285850'; //Optional, a comma separated list of milliseconds OR HH:MM:SS of when black-out times begin. -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'BlackoutEndTimes'; //Optional, literal string must be present when an asset follows C3 behavior -->
$customDataElement->value = '273470,747350,1288650'; //Optional, a comma separated list of milliseconds OR HH:MM:SS of when black-out times end. -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'ChapterStartTimes'; //Optional, literal string -->
$customDataElement->value = '270540,744780,1285850'; //Optional, a comma separated list of milliseconds OR HH:MM:SS of when ad insertions are expected. -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Credit Roll Start'; //Required, literal string -->
$customDataElement->value = '1277000'; //Required, a single value in milliseconds OR HH:MM:SS of when credits begin. -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Adult'; //Optional, literal string -->
$customDataElement->value = 'Adult'; //Optional, valid values are: Adult, nonAdult -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'HD Override'; //Optional, literal string - coordinate with CIM before using -->
$customDataElement->value = 'none'; //Optional, valid values are: none, HD, SD -->
$media->customData[] = $customDataElement;

//People, up to 10 people can be associated with each media asset -->
$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 01 ID'; //Optional, literal string -->
$customDataElement->value = '72308'; //Optional, the cPlatform, TMS, Baseline, IMDB or AMG person matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 01 ID Type'; //Optional, literal string -->
$customDataElement->value = 'cPlatform'; //Optional, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 01 Name'; //Optional, literal string -->
$customDataElement->value = 'James Gandolfini'; //Optional, a plain text field -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 02 ID'; //Optional, literal string -->
$customDataElement->value = '44429'; //Optional, the cPlatform, TMS, Baseline, IMDB or AMG person matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 02 ID Type'; //Optional, literal string -->
$customDataElement->value = 'cPlatform'; //Optional, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 02 Name'; //Optional, literal string -->
$customDataElement->value = 'Lorraine Bracco'; //Optional, a plain text field -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 03 ID'; //Optional, literal string -->
$customDataElement->value = '39861'; //Optional, the cPlatform, TMS, Baseline, IMDB or AMG person matching ID -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 03 ID Type'; //Optional, literal string -->
$customDataElement->value = 'cPlatform'; //Optional, the ID space used, valid values are: cPlatform, TMS, Baseline, IMDB, AMG -->
$media->customData[] = $customDataElement;

$customDataElement = new ComcastCustomDataElement();
$customDataElement->title = 'Associated Person Addition 03 Name'; //Optional, literal string -->
$customDataElement->value = 'Edie Falco'; //Optional, a plain text field -->
$media->customData[] = $customDataElement;

$mediaFiles = new ComcastMediaFileList();

$mediaFile = new ComcastMediaFile();
//$mediaFile->template = new ComcastArrayOfMediaFileField();
$mediaFile->allowRelease = true;
$mediaFile->bitrate = $flavorAsset1_Bitrate;
$mediaFile->contentType = ComcastContentType::_VIDEO;
$mediaFile->format = ComcastFormat::_FLV;
$mediaFile->length = $flavorAsset1_Duration;
$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
$mediaFile->originalLocation = $flavorAsset1_URL;
$mediaFile->height = $flavorAsset1_Width;
$mediaFile->width = $flavorAsset1_Height;
$mediaFiles[] = $mediaFile;


$mediaFile = new ComcastMediaFile();
//$mediaFile->template = new ComcastArrayOfMediaFileField();
$mediaFile->allowRelease = true;
$mediaFile->bitrate = $flavorAsset2_Bitrate;
$mediaFile->contentType = ComcastContentType::_VIDEO;
$mediaFile->format = ComcastFormat::_FLV;
$mediaFile->length = $flavorAsset2_Duration;
$mediaFile->mediaFileType = ComcastMediaFileType::_INTERNAL;
$mediaFile->originalLocation = $flavorAsset2_URL;
$mediaFile->height = $flavorAsset2_Width;
$mediaFile->width = $flavorAsset2_Height;
$mediaFiles[] = $mediaFile;


$options = new ComcastAddContentOptions();
$options->generateThumbnail = false;
$options->publish = false;
$options->deleteSource = false;

$results = $mediaService->addContent($media, $mediaFiles, $options);

var_dump($results);

