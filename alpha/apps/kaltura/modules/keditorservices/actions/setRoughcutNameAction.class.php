<?php
require_once ( "myKshowUtils.class.php");
require_once ( "defKeditorservicesAction.class.php");
class setRoughcutNameAction extends defKeditorservicesAction
{
	protected function executeImpl( kshow $kshow, entry &$entry )
	{
		$this->res = "";
		
		$likuser_id = $this->getLoggedInUserId();
		
		if ( $likuser_id != $entry->getKuserId())
		{
			// ERROR - attempting to update an entry which doesnt belong to the user
			return "<xml>!</xml>";//$this->securityViolation( $kshow->getId() );
		}
		
		$name = @$_GET["RoughcutName"];
		
		$entry->setName($name);
		$entry->save();
		
		//myEntryUtils::createWidgetImage($entry, false);
		
		$this->name = $name;
	}
}

?>