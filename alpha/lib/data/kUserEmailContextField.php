<?php
/**
 * Represents the current session user e-mail address context 
 * @package Core
 * @subpackage model.data
 */
class kUserEmailContextField extends kStringField
{
	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null) 
	{
		if(!$scope)
			$scope = new kScope();
			
		$kuser = kuserPeer::getKuserByPartnerAndUid($scope->getKs()->partner_id, $scope->getKs()->user);
		return $kuser->getEmail();
	}
}