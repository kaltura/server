<?php


class ComcastEncodingProfileField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _AUDIOBITRATE = 'audioBitrate';
					
	const _AUDIOBITRATEMODE = 'audioBitrateMode';
					
	const _AUDIOBITSPERSAMPLE = 'audioBitsPerSample';
					
	const _AUDIOCHANNELS = 'audioChannels';
					
	const _AUDIOCODECID = 'audioCodecID';
					
	const _AUDIOCODECTITLE = 'audioCodecTitle';
					
	const _AUDIOSAMPLERATE = 'audioSampleRate';
					
	const _AVAILABLEONSHAREDCONTENT = 'availableOnSharedContent';
					
	const _CONTENTTYPE = 'contentType';
					
	const _CORRECTFORREPEATEDFRAMES = 'correctForRepeatedFrames';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLED = 'disabled';
					
	const _ENCODINGPROVIDER = 'encodingProvider';
					
	const _EXTERNALENCODINGPROFILEID = 'externalEncodingProfileID';
					
	const _FILEEXTENSION = 'fileExtension';
					
	const _FORMAT = 'format';
					
	const _HINTING = 'hinting';
					
	const _IMAGEHEIGHT = 'imageHeight';
					
	const _IMAGEQUALITY = 'imageQuality';
					
	const _IMAGEWIDTH = 'imageWidth';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LOCKED = 'locked';
					
	const _MAXIMUMAUDIOBITRATE = 'maximumAudioBitrate';
					
	const _MAXIMUMAUDIOBUFFERING = 'maximumAudioBuffering';
					
	const _MAXIMUMPACKETDURATION = 'maximumPacketDuration';
					
	const _MAXIMUMPACKETSIZE = 'maximumPacketSize';
					
	const _MAXIMUMVIDEOBITRATE = 'maximumVideoBitrate';
					
	const _MAXIMUMVIDEOBUFFERING = 'maximumVideoBuffering';
					
	const _OPTIMIZEFORENCODINGSPEED = 'optimizeForEncodingSpeed';
					
	const _OPTIMIZEFORPORTABLEDEVICES = 'optimizeForPortableDevices';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _REFRESHSTATUS = 'refreshStatus';
					
	const _STATUS = 'status';
					
	const _STATUSDESCRIPTION = 'statusDescription';
					
	const _STATUSDETAIL = 'statusDetail';
					
	const _STATUSMESSAGE = 'statusMessage';
					
	const _TITLE = 'title';
					
	const _TOTALBITRATE = 'totalBitrate';
					
	const _VERSION = 'version';
					
	const _VIDEOBITRATE = 'videoBitrate';
					
	const _VIDEOBITRATEMODE = 'videoBitrateMode';
					
	const _VIDEOCODECID = 'videoCodecID';
					
	const _VIDEOCODECTITLE = 'videoCodecTitle';
					
	const _VIDEOFRAMERATE = 'videoFrameRate';
					
	const _VIDEOKEYFRAMEINTERVAL = 'videoKeyFrameInterval';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


