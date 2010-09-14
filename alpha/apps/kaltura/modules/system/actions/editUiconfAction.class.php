<?php

require_once ( "kalturaSystemAction.class.php" );

class editUiconfAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2
		
		$this->saved = false;
		
		$uiConfId = $this->getRequestParameter("id");
		if($uiConfId)
		{
			$uiConf = uiConfPeer::retrieveByPK($uiConfId);
		}
		else
		{
			$uiConf = new uiConf();
		}
		if ($this->getRequest()->getMethodName() == "POST")
		{
			$uiConf->setPartnerId($this->getRequestParameter("partnerId"));
			$uiConf->setSubpId($uiConf->getPartnerId() * 100);
			$uiConf->setObjType($this->getRequestParameter("type"));
			$uiConf->setName($this->getRequestParameter("name"));
			$uiConf->setWidth($this->getRequestParameter("width"));
			$uiConf->setHeight($this->getRequestParameter("height"));
			$uiConf->setCreationMode($this->getRequestParameter("creationMode"));
			$uiConf->setSwfUrl($this->getRequestParameter("swfUrl"));
			$uiConf->setConfVars($this->getRequestParameter("confVars"));
			$useCdn = $this->getRequestParameter("useCdn");
			$uiConf->setUseCdn(($useCdn === "1") ? true : false);
			$uiConf->setDisplayInSearch($this->getRequestParameter("displayInSearch"));
			$uiConf->setConfVars($this->getRequestParameter("confVars"));
			$uiConf->setTags($this->getRequestParameter("tags"));
			/* files: */
			if ($uiConf->getCreationMode() != uiConf::UI_CONF_CREATION_MODE_MANUAL)
			{
				$confFile = $this->getRequestParameter("uiconf_confFile");
				$confFileFeatures = $this->getRequestParameter("uiconf_confFileFeatures");
				
				if ($uiConf->getConfFile() != $confFile || $uiConf->getConfFileFeatures() != $confFileFeatures)
				{
					$uiConf->setConfFile($confFile);
					$uiConf->setConfFileFeatures($confFileFeatures);
				}
			}
			
			$uiConf->save();
			$this->saved = true;
		}
		
		// so script won't die when uiconf is missing
		if (!$uiConf)
			$uiConf = new uiConf();
			
		$partner = PartnerPeer::retrieveByPK($uiConf->getPartnerId());
		$types = $uiConf->getUiConfTypeMap();
		
		$c = new Criteria();
		$c->add(widgetPeer::UI_CONF_ID, $uiConf->getId());
		$c->setLimit(1000);
		$widgets = widgetPeer::doSelect($c);
		$widgetsPerPartner = array();
		$this->tooMuchWidgets = false;
		if (count($widgets) == 1000)
		{
		    $this->tooMuchWidgets = true;
		}
		else if ($widgets)
		{
			foreach($widgets as $widget)
			{
				if (!array_key_exists($widget->getPartnerId(), $widgetsPerPartner)) 
					$widgetsPerPartner[$widget->getPartnerId()] = 0;
					
				$widgetsPerPartner[$widget->getPartnerId()]++;
			}
		}
		
		// find the FileSync
		$fileSyncs = array();
		$fileSyncs[] = array("key" => $uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA));
		$fileSyncs[] = array("key" => $uiConf->getSyncKey(uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES));
		foreach($fileSyncs as &$fileSync)
		{
			$fileSync["fileSyncs"] = FileSyncPeer::retreiveAllByFileSyncKey($fileSync["key"]);
		} 
		$this->fileSyncs = $fileSyncs;
		$this->widgetsPerPartner = $widgetsPerPartner;
		$this->directoryMap = $uiConf->getDirectoryMap();
		$this->swfNames = $uiConf->getSwfNames();
		$this->types = $types;
		$this->uiConf = $uiConf;
		$this->partner = $partner;
	}
}

?>