<?php


class ComcastEndUserTransactionType extends SoapObject
{				
	const _ADDED = 'Added';
					
	const _AUTOMATICALLY_RENEW = 'Automatically Renew';
					
	const _CUSTOM = 'Custom';
					
	const _DELETED = 'Deleted';
					
	const _DISABLED = 'Disabled';
					
	const _DO_NOT_RENEW = 'Do Not Renew';
					
	const _ENABLED = 'Enabled';
					
	const _EXPIRED = 'Expired';
					
	const _GOT_LICENSE = 'Got License';
					
	const _REFUND = 'Refund';
					
	const _RENEWED = 'Renewed';
					
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


