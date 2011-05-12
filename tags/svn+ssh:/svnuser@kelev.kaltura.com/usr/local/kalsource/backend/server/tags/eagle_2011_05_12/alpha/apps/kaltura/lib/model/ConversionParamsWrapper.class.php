<?php
class ConversionParamsWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "partnerId" , "name" );

	protected $regular_fields_ext = array (  "enabled" , "profileType" , "profileTypeIndex" , "commercialTranscoder" ,
		"width" , "height" , "aspectRatio" , 
		"gopSize" , "bitrate" , "qscale" , "fileSuffix" , "createdAt" , "framerate" , "audioBitrate" , "audioSamplingRate" , "audioChannels");

	protected $detailed_fields_ext = array ( ) ;

	protected $detailed_objs_ext = array ( );

	protected $objs_cache = array (  );

	protected $updateable_fields = array ( "name" , "enabled" , "profileType" , "profileTypeIndex" , "commercialTranscoder" ,
		"width" , "height" , "aspectRatio" , 
		"gopSize" , "bitrate" , "qscale" , "fileSuffix" ,  "framerate" , "audioBitrate" , "audioSamplingRate" , "audioChannels" );
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "ConversionParams",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
}
?>