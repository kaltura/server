<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMailJobData extends KalturaJobData
{
	/**
	 * @var KalturaMailType
	 */
	public $mailType;

	/**
	 * @var int
	 */
    public $mailPriority;

    /**
	 * @var KalturaMailJobStatus
	 */
    public $status ;
    
	/**
	 * @var string
	 */
	public $recipientName;  

	/**
	 * @var string
	 */	
   	public $recipientEmail;
   	
	/**
	 * kuserId  
	 * @var int
	 */   	
    public $recipientId;
    
	/**
	 * @var string
	 */    
    public $fromName;
    
	/**
	 * @var string
	 */    
    public $fromEmail;
  
	/**
	 * @var string
	 */    
    public $bodyParams;

	/**
	 * @var string
	 */    
    public $subjectParams;  

	/**
 	* @var string
 	*/
    public $templatePath;

	/**
 	* @var int
 	*/
    public $culture;

	/**
 	* @var int
 	*/
    public $campaignId;

	/**
 	* @var int
 	*/
    public $minSendDate;
    
    /**
     * @var bool
     */
    public $isHtml;
    
	private static $map_between_objects = array
	(
		"mailType" ,
	    "mailPriority" ,
	    "status " ,
		"recipientName" ,  
	   	"recipientEmail" ,
	    "recipientId" ,
	    "fromName" ,
	    "fromEmail" ,
	    "bodyParams" ,
	    "subjectParams" ,  
	    "templatePath" ,
	    "culture" ,
	    "campaignId" ,
	    "minSendDate" ,
		"isHtml" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kMailJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>