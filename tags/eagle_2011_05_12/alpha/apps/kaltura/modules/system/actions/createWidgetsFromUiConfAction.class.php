<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class createWidgetsFromUiConfAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		myDbHelper::$use_alternative_con = null;
		
		// ajax stuff are here because there are too many actions in system module
		$ajax = $this->getRequestParameter("ajax", null);
		if ($ajax !== null)
		{
			switch($ajax)
			{
				case "getPartnerName":
					$name = "Unknown Partner";
					$partnerId = $this->getRequestParameter("id");
					$partner = PartnerPeer::retrieveByPK($partnerId);
					
					if ($partner)
					{
						$name = $partner->getName();
					}
					
					return $this->renderText(json_encode($name));
				case "validateWidgetIds":
					$widgetIds = explode(",",$this->getRequestParameter("widgetIds"));
					$existingIds = array(); 
					if (is_array($widgetIds))
					{
						$widgets = widgetPeer::retrieveByPKs($widgetIds);
						if ($widgets) 
						{
							
							foreach($widgets as $widget)
							{
								$id = $widget->getId();
								if (in_array($id, $widgetIds))
								{
										$existingIds[] = $id;				
								}
							}
						}
					}
					return $this->renderText(json_encode($existingIds));
			}
		}
		// end of ajax stuff
		
		$uiConfId = $this->getRequestParameter("uiConfId");
		$uiConf = uiConfPeer::retrieveByPK($uiConfId);
		
		if ($this->getRequest()->getMethodName() == "POST")
		{
			 $numOfWidgets = (integer)$this->getRequestParameter("numOfWidgets");
			 $startIndex = (integer)$this->getRequestParameter("startIndex");
			 $partnerId = $this->getRequestParameter("partnerId");
			 $uiConfId = $this->getRequestParameter("uiConfId");
			 $entryId = $this->getRequestParameter("entryId");
			 $securityType = $this->getRequestParameter("securityType");
			 $securityPolicy = $this->getRequestParameter("securityPolicy");
			 $prefix = $this->getRequestParameter("prefix");
			 if ($prefix)
			     $prefix = "_";
		     else
		         $prefix = "";
			 
			 
			 for($i = $startIndex; $i < $startIndex + $numOfWidgets; $i++)
			 {
					$widget = new widget();
					if (!$i)
					    $widget->setId($prefix.$partnerId."_".$uiConfId);
				    else
			        	$widget->setId($prefix.$partnerId."_".$uiConfId."_".$i);
					$widget->setUiConfId($uiConfId);
					$widget->setPartnerId($partnerId);
					$widget->setSubpId($partnerId*100);
					$widget->setEntryId($entryId);
					$widget->setSecurityType($securityType);
					$widget->setSecurityPolicy($securityPolicy);
					$widget->save();
			 }
			 $this->redirect("system/editUiconf?id=".$uiConfId);
		}
		
		if ($uiConf)
		{
			$partner = PartnerPeer::retrieveByPK($uiConf->getPartnerId());	
		}
		else
		{
			$uiConf = new uiConf();
			$partner = new Partner();
		}
			
		
		$this->widget = new widget();;
		$this->uiConf = $uiConf;
		$this->partner = $partner;
	}
}

?>