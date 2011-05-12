<?php


class ComcastCustomFieldField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWEDTEXTVALUES = 'allowedTextValues';
					
	const _AVAILABLEONSHAREDCONTENT = 'availableOnSharedContent';
					
	const _DEFAULTTEXTVALUE = 'defaultTextValue';
					
	const _DESCRIPTION = 'description';
					
	const _FIELDNAME = 'fieldName';
					
	const _FIELDTYPE = 'fieldType';
					
	const _INCLUDEINFEEDS = 'includeInFeeds';
					
	const _INCLUDEINMETAFILES = 'includeInMetafiles';
					
	const _INCLUDEINRELEASES = 'includeInReleases';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LENGTH = 'length';
					
	const _LIMITTOAPIOBJECTS = 'limitToAPIObjects';
					
	const _LINESTODISPLAY = 'linesToDisplay';
					
	const _LOCKED = 'locked';
					
	const _NAMESPACE = 'namespace';
					
	const _NAMESPACEPREFIX = 'namespacePrefix';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _SHAREWITHACCOUNTIDS = 'shareWithAccountIDs';
					
	const _SHAREWITHACCOUNTS = 'shareWithAccounts';
					
	const _SHAREWITHALLACCOUNTS = 'shareWithAllAccounts';
					
	const _SHOWINMOREFIELDS = 'showInMoreFields';
					
	const _SUPPORTEDFORMATS = 'supportedFormats';
					
	const _TITLE = 'title';
					
	const _VERSION = 'version';
					
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


