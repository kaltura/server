<?php
class Kaltura_View_Helper_PackageNameById extends Zend_View_Helper_Abstract
{
	public function packageNameById($id)
	{
		return Infra_PartnerPackageHelper::getPackageNameById($id);
	}
}