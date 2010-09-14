<?php

require_once ( "kalturaSystemAction.class.php" );

class addUiconfAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();

		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2
		
		$this->partner_error  = null;
		
		if(isset($_POST['partnerId']) && $_POST['partnerId'] !== null && $_POST['partnerId'] !== '')
		{
			$partner = PartnerPeer::retrieveByPK($_POST['partnerId']);
			if($partner)
			{
				$uiConf = new uiConf();
				$uiConf->setPartnerId($_POST['partnerId']);
				$uiConf->setCreationMode(uiConf::UI_CONF_CREATION_MODE_ADVANCED);
				$uiConf->save();
				$this->redirect("system/editUiconf?id=".$uiConf->getId());
			}
			else
			{
				$this->partner_error = 'Partner ID '.$_POST['partnerId'].' not found !';
			}
		}
	}
}

?>