<?php


class ComcastStatusDetail extends SoapObject
{				
	const _APPLYINGDRM = 'ApplyingDRM';
					
	const _AUTHENTICATIONERROR = 'AuthenticationError';
					
	const _CANNOTDELIVERINPLAYLIST = 'CannotDeliverInPlaylist';
					
	const _CANNOTREFUND = 'CannotRefund';
					
	const _CANNOTSHARE = 'CannotShare';
					
	const _CATEGORYUPDATEERROR = 'CategoryUpdateError';
					
	const _CONTAINERERROR = 'ContainerError';
					
	const _DELETEDCATEGORY = 'DeletedCategory';
					
	const _DELETEDCONTENT = 'DeletedContent';
					
	const _DELETEDENDUSER = 'DeletedEndUser';
					
	const _DELETEDOWNER = 'DeletedOwner';
					
	const _DELETEDSERVER = 'DeletedServer';
					
	const _DISABLED = 'Disabled';
					
	const _DRMERROR = 'DRMError';
					
	const _DRMUNSUPPORTEDCODEC = 'DRMUnsupportedCodec';
					
	const _ENCODINGERROR = 'EncodingError';
					
	const _ENCODINGFILE = 'EncodingFile';
					
	const _EXPIRED = 'Expired';
					
	const _GENERATINGTHUMBNAIL = 'GeneratingThumbnail';
					
	const _INTRANSIT = 'InTransit';
					
	const _MISSINGBILLINGADDRESS = 'MissingBillingAddress';
					
	const _MISSINGCREDITCARD = 'MissingCreditCard';
					
	const _MISSINGVIDEO = 'MissingVideo';
					
	const _NOCHILDREN = 'NoChildren';
					
	const _NOCOMMONFORMATS = 'NoCommonFormats';
					
	const _NODELIVERY = 'NoDelivery';
					
	const _NONE = 'None';
					
	const _NOTAVAILABLE = 'NotAvailable';
					
	const _NOTFOUND = 'NotFound';
					
	const _NOTIFICATIONERROR = 'NotificationError';
					
	const _NOTPAIDINFULL = 'NotPaidInFull';
					
	const _OVERAMOUNTBILLABLE = 'OverAmountBillable';
					
	const _OVERMAXIMUMBITRATE = 'OverMaximumBitrate';
					
	const _PROCESSINGCREDITCARD = 'ProcessingCreditCard';
					
	const _PROCESSINGERROR = 'ProcessingError';
					
	const _PROTECTEDFILE = 'ProtectedFile';
					
	const _PUBLISHINGWEBSERVICE = 'PublishingWebService';
					
	const _PUBLISHINGWEBSERVICEERROR = 'PublishingWebServiceError';
					
	const _REQUIRESDRM = 'RequiresDRM';
					
	const _ROLLEDUPFROMCHOICE = 'RolledUpFromChoice';
					
	const _ROLLEDUPFROMCONTENT = 'RolledUpFromContent';
					
	const _ROLLEDUPFROMENDUSER = 'RolledUpFromEndUser';
					
	const _ROLLEDUPFROMENDUSERPERMISSION = 'RolledUpFromEndUserPermission';
					
	const _ROLLEDUPFROMENDUSERTRANSACTION = 'RolledUpFromEndUserTransaction';
					
	const _ROLLEDUPFROMLOCATION = 'RolledUpFromLocation';
					
	const _ROLLEDUPFROMMEDIAFILE = 'RolledUpFromMediaFile';
					
	const _ROLLEDUPFROMRELEASE = 'RolledUpFromRelease';
					
	const _ROLLEDUPFROMSERVER = 'RolledUpFromServer';
					
	const _SERVERMISMATCH = 'ServerMismatch';
					
	const _THUMBNAILERROR = 'ThumbnailError';
					
	const _TRANSFERERROR = 'TransferError';
					
	const _UNAPPROVEDCONTENT = 'UnapprovedContent';
					
	const _UNAVAILABLEAUDIOCODEC = 'UnavailableAudioCodec';
					
	const _UNAVAILABLEAUDIOCODECSETTINGS = 'UnavailableAudioCodecSettings';
					
	const _UNAVAILABLECONTENT = 'UnavailableContent';
					
	const _UNAVAILABLEOWNER = 'UnavailableOwner';
					
	const _UNAVAILABLESERVER = 'UnavailableServer';
					
	const _UNAVAILABLESETTINGS = 'UnavailableSettings';
					
	const _UNAVAILABLEVIDEOCODEC = 'UnavailableVideoCodec';
					
	const _UNAVAILABLEVIDEOCODECSETTINGS = 'UnavailableVideoCodecSettings';
					
	const _UNKNOWN = 'Unknown';
					
	const _UNKNOWNFORMAT = 'UnknownFormat';
					
	const _UPDATINGCATEGORY = 'UpdatingCategory';
					
	const _VERIFICATIONERROR = 'VerificationError';
					
	const _VERIFYING = 'Verifying';
					
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


