<?php

require_once ( "kalturaSystemAction.class.php" );

class kcwUiConfEditorAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2
		
		$this->saved = false;
		
		$uiConfId = $this->getRequestParameter("id");
		$this->uiConf = uiConfPeer::retrieveByPK($uiConfId);
		
		if ($this->uiConf->getObjType() != uiConf::UI_CONF_TYPE_CW)
			die("Not a CW UIConf!");
		
		if ($this->uiConf->getCreationMode() != uiConf::UI_CONF_CREATION_MODE_ADVANCED)
			die("Creation mode must be advanced!"); 
		
		if ($this->getRequest()->getMethodName() == "POST")
		{
			$this->uiConf->setConfFile($this->getRequestParameter("confFile"));
			$this->uiConf->save();
			$this->redirect("system/editUiconf?id=".$this->uiConf->getId());
		}
	}
}

?>