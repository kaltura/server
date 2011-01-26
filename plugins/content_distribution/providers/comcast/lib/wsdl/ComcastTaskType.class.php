<?php


class ComcastTaskType extends SoapObject
{				
	const _ADDEXTERNALRELEASE = 'AddExternalRelease';
					
	const _COPYFILE = 'CopyFile';
					
	const _DELETEEXTERNALRELEASE = 'DeleteExternalRelease';
					
	const _DELETEFILE = 'DeleteFile';
					
	const _ENCODEFILE = 'EncodeFile';
					
	const _FETCHFILE = 'FetchFile';
					
	const _GENERATETHUMBNAIL = 'GenerateThumbnail';
					
	const _MOVEFILE = 'MoveFile';
					
	const _PROTECTFILE = 'ProtectFile';
					
	const _PUBLISHCONTENT = 'PublishContent';
					
	const _SENDDIAGNOSTICS = 'SendDiagnostics';
					
	const _SENDNOTIFICATION = 'SendNotification';
					
	const _SETEXTERNALRELEASE = 'SetExternalRelease';
					
	const _UPDATEFILELAYOUT = 'UpdateFileLayout';
					
	const _VERIFYFILE = 'VerifyFile';
					
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


