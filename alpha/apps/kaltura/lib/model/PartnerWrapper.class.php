<?php
class PartnerWrapper extends objectWrapperBase
{
	// TODO - make sure is never called from an object - secret and admin secret will leak out!
	protected $basic_fields = array ( "id" , "name" );

	protected $regular_fields_ext = array ( "url1" , "url2" , "appearInSearch" , "createdAt" , "adminName" ,
		"adminEmail" , "description" , "commercialUse" , "landingPage" , "userLandingPage" , "contentCategories" , "type" , "landingPage" , "userLandingPage" ,
		"phone" , "describeYourself" , "adultContent" , "defConversionProfileType" , "notify", "status",
		"allowQuickEdit" , "mergeEntryLists" , "notificationsConfig" , "maxUploadSize", "partnerPackage"
	);

	protected $detailed_fields_ext = array ("secret", "adminSecret" ) ;

	protected $detailed_objs_ext = array ( );

	protected $objs_cache = array ( );

	protected $updateable_fields = array ( "name"  , "url1" , "url2" , "appearInSearch" , "adminName" ,
		"adminEmail" , "description" , "commercialUse" , "landingPage" , "userLandingPage", "notificationsConfig",
		"notify", "allowMultiNotification" , "contentCategories" , "type" , 
		"landingPage" , "userLandingPage" , "phone" , "describeYourself" , "adultContent" , "defConversionProfileType",
		"allowQuickEdit" , "mergeEntryLists" , "maxUploadSize" );

	public function describe()
	{
		return
			array (
				"display_name" => "Partner",
				"desc" => ""
			);
	}

	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
}
?>