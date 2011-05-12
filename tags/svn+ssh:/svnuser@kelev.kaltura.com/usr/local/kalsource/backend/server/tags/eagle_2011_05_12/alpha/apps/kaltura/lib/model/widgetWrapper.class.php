<?php
class widgetWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "partnerId" );

	protected $regular_fields_ext = array ( "intId" , "sourceWidgetId" , "rootWidgetId" , "kshowId" , "entryId" ,
		"uiConfId", "customData" , "widgetHtml" , "partnerData" ,"securityType" , "securityPolicy");

	protected $detailed_fields_ext = array ( ) ;

	protected $detailed_objs_ext = array ( "kshow" , "entry" ,  "uiConf" );

	protected $objs_cache = array ( "kshow" => "kshow,kshowId" , "entry" => "entry,entryId" ,  "uiConf" => "uiConf,uiConfId" );

	protected $updateable_fields = array ( "kshowId" , "entryId" , "sourceWidgetId" , "uiConfId" , "customData" , "partnerData" , "securityType");
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "Widget",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
}
?>