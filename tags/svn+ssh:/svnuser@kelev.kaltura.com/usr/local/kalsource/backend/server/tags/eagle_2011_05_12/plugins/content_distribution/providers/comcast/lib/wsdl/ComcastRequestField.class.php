<?php


class ComcastRequestField extends SoapObject
{				
	const _AFFILIATE = 'affiliate';
					
	const _ASSETTYPE = 'assetType';
					
	const _AUTHOR = 'author';
					
	const _BITRATE = 'bitrate';
					
	const _BITRATEINKBPS = 'bitrateInKbps';
					
	const _BROWSER = 'browser';
					
	const _BUFFERING = 'buffering';
					
	const _CATEGORIES = 'categories';
					
	const _CONTENTCLASS = 'contentClass';
					
	const _CONTENTID = 'contentID';
					
	const _CONTENTIDFORGROUP = 'contentIDForGroup';
					
	const _CONTENTOWNER = 'contentOwner';
					
	const _CONTENTOWNERACCOUNTID = 'contentOwnerAccountID';
					
	const _CONTENTTYPE = 'contentType';
					
	const _COUNTRY = 'country';
					
	const _DELIVERY = 'delivery';
					
	const _ENCODINGPROFILE = 'encodingProfile';
					
	const _FORMAT = 'format';
					
	const _INPLAYLIST = 'inPlaylist';
					
	const _INPLAYLISTID = 'inPlaylistID';
					
	const _INPLAYLISTIDFORGROUP = 'inPlaylistIDForGroup';
					
	const _LANGUAGE = 'language';
					
	const _LENGTH = 'length';
					
	const _LENGTHPLAYED = 'lengthPlayed';
					
	const _LOADTIME = 'loadTime';
					
	const _NETWORK = 'network';
					
	const _NETWORKSERVERID = 'networkServerID';
					
	const _OPERATINGSYSTEM = 'operatingSystem';
					
	const _OUTLET = 'outlet';
					
	const _OUTLETACCOUNTID = 'outletAccountID';
					
	const _PLAYED = 'played';
					
	const _PLAYER = 'player';
					
	const _PORTAL = 'portal';
					
	const _QUALITY = 'quality';
					
	const _RATING = 'rating';
					
	const _REGION = 'region';
					
	const _REQUESTCOUNT = 'requestCount';
					
	const _REQUESTDATE = 'requestDate';
					
	const _REQUESTDATEONLY = 'requestDateOnly';
					
	const _REQUESTDAYOFWEEK = 'requestDayOfWeek';
					
	const _REQUESTHOUR = 'requestHour';
					
	const _REQUESTMONTH = 'requestMonth';
					
	const _REQUESTMONTHONLY = 'requestMonthOnly';
					
	const _SIZE = 'size';
					
	const _TITLE = 'title';
					
	const _TRACKINGCOUNT = 'trackingCount';
					
	const _USERNAME = 'userName';
					
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


