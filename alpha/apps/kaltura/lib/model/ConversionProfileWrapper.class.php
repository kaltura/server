<?php
class ConversionProfileWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "partnerId" , "name" );

	protected $regular_fields_ext = array (  "profileType" , "commercialTranscoder" ,"width" , "height" , "aspectRatio" , "bypassFlv" , "useWithBulk" , 
		"createdAt" , "updatedAt"
		, "profileTypeSuffix" );

	protected $detailed_fields_ext = array ( ) ;

	protected $detailed_objs_ext = array ( "conversionParams" );

	protected $objs_cache = array (  );

	protected $updateable_fields = array ( "name" , "profileType" , "width" , "height" , "aspectRatio" , "bypassFlv" , "commercialTranscoder" , "useWithBulk"
		, "profileTypeSuffix" );	
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "ConversionProfile",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
}
?>