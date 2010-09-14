<?php
class uiConfWrapper extends objectWrapperBase
{
        protected $basic_fields = array ( "id"  , "partnerId");

        protected $regular_fields_ext = array ( "objType" , "objTypeAsString" , "name" , "width" , "height" ,
                "htmlParams", "swfUrl" , "confFilePath" , "confFile" ,   "confVars" , "useCdn" , "tags" , "swfUrlVersion" , "createdAt" , "updatedAt" ,
        		"autoplay" , "automuted" ,"creationMode" );

        protected $detailed_fields_ext = array ( "confFileFeatures" , ) ;

        protected $detailed_objs_ext = array ( );

        protected $updateable_fields = array ( "name" , "objType" , "width" , "height" ,
                "htmlParams", "swfUrl" ,"creationMode" ,  "swfUrlVersion" , /*"confFilePath"  , */ "confFile" , "confFileFeatures", "confVars" , "useCdn" , "tags" ,
        		"autoplay" , "automuted"  );
        
        protected $objs_cache = array ( );

		public function describe() 
		{
			return 
				array (
					"display_name" => "UiConf",
					"desc" => ""
				);
		}
	
        public function getUpdateableFields()
        {
        	return $this->updateable_fields;
        }
}
?>
