<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaDocCommentParser
{
    const DOCCOMMENT_READONLY = "/\\@readonly/i";
    const DOCCOMMENT_INSERTONLY = "/\\@insertonly/i";
    const DOCCOMMENT_WRITEONLY = "/\\@writeonly/i";
    
    const DOCCOMMENT_PARAM = '/\@param (\w*) \$__NAME__ ?(.*)/';
    const DOCCOMMENT_REPLACENET_PARAM_NAME = '__NAME__';
    
    const DOCCOMMENT_ALIAS = '/\@alias (\w*)/';
    const DOCCOMMENT_VOLATILE = "/\\@volatile/i";
    
    const DOCCOMMENT_VAR_TYPE = "/\\@var (\\w*)/";
    const DOCCOMMENT_LINK = "/\\@link (.*)/";
    const DOCCOMMENT_DESCRIPTION = " /\\*\\s*?[^@]+/";
    const DOCCOMMENT_RETURN_TYPE = "/\\@return (\\w*)/";
    
    const DOCCOMMENT_SERVICE_NAME =  "/\\@service\\s?(\\w*)/";
    
    const DOCCOMMENT_ACTION = "/\\@action\\s?(\\w*)/";
    const DOCCOMMENT_ACTION_ERRORS = "/\\@throws (.*)::(.*)/";
    
    const DOCCOMMENT_PACKAGE = "/\\@package ([.\\w]*)/";
    const DOCCOMMENT_SUBPACKAGE = "/\\@subpackage ([.\\w]*)/";
    
    const DOCCOMMENT_CLIENT_GENERATOR = "/\\@clientgenerator (\\w*)/";
    
    const DOCCOMMENT_FILTER = "/\\@filter ([\\w\\,\\s]*)/";
    
    const DOCCOMMENT_ABSTRACT = "/\\@abstract/i";
    
    const DOCCOMMENT_DEPRECATED = "/\\@deprecated/i";
    
    const DOCCOMMENT_DEPRECATION_MESSAGE = "/\\@deprecated (.*)/";

    const DOCCOMMENT_SERVER_ONLY = "/\\@serverOnly/i";
    
    const DOCCOMMENT_DYNAMIC_TYPE = "/\\@dynamicType (\\w*)/i";
    
    const DOCCOMMENT_PERMISSIONS = "/\\@requiresPermission ([\\w\\,\\s]*)/";
    
    const DOCCOMMENT_VALIDATE_USER = "/\\@validateUser\\s+(\\w+)\\s+(\\w+)\\s+(\\w+)\\s*(\\w*)/";
    
    const DOCCOMMENT_ALIAS_ACTION = "/\\@actionAlias\\s(\\w+\\.\\w+)/";
    
    const DOCCOMMENT_DISABLE_TAGS = "/\\@disableTags ([\\w\\,\\s\\d]*)/";
    
    const DOCCOMMENT_VALIDATE_CONSTRAINT = "/\\@CONSTRAINT\\s+([\\w.]+\\s+)?(\\d+)/";

    const DOCCOMMENT_DISABLE_RELATIVE_TIME = "/\\@disableRelativeTime \\$(\\w*)/";
    
    const DOCCOMMENT_KS_OPTIONAL = "/\\@ksOptional/i";
    
    const DOCCOMMENT_KS_IGNORED = "/\\@ksIgnored/i";
    

    const MIN_LENGTH_CONSTRAINT = "minLength";
    const MAX_LENGTH_CONSTRAINT = "maxLength";
    const MIN_VALUE_CONSTRAINT = "minValue";
    const MAX_VALUE_CONSTRAINT = "maxValue";
    
    /**
     * @var bool
     */
    public $readOnly;
    
    /**
     * @var bool
     */
    public $writeOnly;
    
    /**
     * @var bool
     */
    public $volatile;
    
    /**
     * @var bool
     */
    public $insertOnly;
    
    /**
     * @var string
     */
    public $param = "";
    
    /**
     * @var string
     */
    public $paramDescription = "";
    
    /**
     * @var string
     */
    public $varType;
    
    /**
     * @var string
     */
    public $alias;
    
    /**
     * @var string
     */
    public $returnType;

    /**
     * @var string
     */
    public $link;

    /**
     * @var string
     */
    public $description;
    
    /**
     * @var string 
     */
    public $serviceName;
    
    /**
     * @var string
     */
    public $action;
    
    /**
     * @var string
     */
    public $package;
    
    /**
     * @var string
     */
    public $subpackage;
    
    /**
     * @var string
     */
    public $clientgenerator;
    
    /**
     * @var string 
     */
    public $filter;
    
    /**
     * @var bool
     */
    public $abstract = false;
    
    /**
     * @var bool
     */
    public $deprecated = false;
    
    /**
     * @var string
     */
    public $deprecationMessage = null;

    /**
     * @var bool
     */
    public $serverOnly = false;
    
    /**
     * @var array
     */
    public $errors;
    
    /**
     * @var string
     */
    public $dynamicType;
    
    /**
     * @var string
     */
    public $permissions;
    
    /**
     * @var string
     */
    public $validateUserObjectClass = null;
    
    /**
     * @var string
     */
    public $validateUserIdParamName = null;
    
    /**
     * @var string
     */
    public $validateUserPrivilege = null;

	/**
     * @var string comma seperated validateUser options
     */
    public $validateOptions = null;
    
    /**
     * @var array
     */
    public $validateConstraints = array();
    
    /**
     * @var string
     */
    public $actionAlias = null;
    
    
    /**
     * @var array
     */
    public $disableTags = null;

    /**
     * True - required
     * False - ignored
     * Null - optional
     * @var bool
     */
    public $ksNeeded = true;
    
    /**
     * Parse a docComment
     *
     * @param string $comment
      @param array $replacements Optional associative array for replacing values in search patterns
     * @return array
     */
    function __construct($comment , $replacements = null)
    {
    	$this->readOnly = preg_match( self::DOCCOMMENT_READONLY, $comment);
        $this->insertOnly = preg_match( self::DOCCOMMENT_INSERTONLY, $comment);
        $this->writeOnly = preg_match( self::DOCCOMMENT_WRITEONLY, $comment);
        $this->volatile = preg_match( self::DOCCOMMENT_VOLATILE, $comment);
        $this->abstract = preg_match( self::DOCCOMMENT_ABSTRACT, $comment);
        $this->deprecated = preg_match( self::DOCCOMMENT_DEPRECATED, $comment);
        $this->serverOnly = preg_match( self::DOCCOMMENT_SERVER_ONLY, $comment);

        if(preg_match( self::DOCCOMMENT_KS_IGNORED, $comment))
        {
        	$this->ksNeeded = false;
        }
        elseif(preg_match( self::DOCCOMMENT_KS_OPTIONAL, $comment))
        {
        	$this->ksNeeded = null;
        }
        
        $result = null;
        if (is_array($replacements) && key_exists(self::DOCCOMMENT_REPLACENET_PARAM_NAME, $replacements))
        {
            $pattern = str_replace(self::DOCCOMMENT_REPLACENET_PARAM_NAME, $replacements[self::DOCCOMMENT_REPLACENET_PARAM_NAME], self::DOCCOMMENT_PARAM);
            if (preg_match( $pattern, $comment, $result ))
            {
                $this->param = $result[1];
                $this->paramDescription = $result[2];
            }
        }
        
        $result = null;
        if (preg_match( self::DOCCOMMENT_ALIAS, $comment, $result ))
            $this->alias = $result[1];
        
        $result = null;
        if (preg_match( self::DOCCOMMENT_VAR_TYPE, $comment, $result ))
            $this->varType = $result[1];
            
        $result = null;
        if (preg_match( self::DOCCOMMENT_DESCRIPTION, $comment, $result ))
        {
            $this->description = preg_replace("/(\\*\\s*)/", "", $result[0]);
        }
            
        if ($this->deprecated)
        {
        	$result = null;
	        if (preg_match( self::DOCCOMMENT_DEPRECATION_MESSAGE, $comment, $result))
	        	$this->deprecationMessage = $result[1];
        }
            
        $result = null;
        if (preg_match( self::DOCCOMMENT_LINK, $comment, $result ))
            $this->link = $result[1];
        
        $result = null;
        if (preg_match(self::DOCCOMMENT_RETURN_TYPE, $comment, $result))
            $this->returnType = $result[1];
        
        $result = null;
        if (preg_match(self::DOCCOMMENT_SERVICE_NAME, $comment, $result))
            $this->serviceName = preg_replace("/[^a-zA-Z0-9_]/", "", $result[1]); // remove not allowed characters
            
        $result = null;
        if (preg_match(self::DOCCOMMENT_ACTION, $comment, $result))
            $this->action = preg_replace("/[^a-zA-Z0-9_]/", "", $result[1]); // remove not allowed characters
            
        $result = null;
        if (preg_match(self::DOCCOMMENT_PACKAGE, $comment, $result))
            $this->package = $result[1];
        
        $result = null;
        if (preg_match(self::DOCCOMMENT_SUBPACKAGE, $comment, $result))
            $this->subpackage = $result[1];
            
        $result = null;
        if (preg_match(self::DOCCOMMENT_CLIENT_GENERATOR, $comment, $result))
            $this->clientgenerator = $result[1];
            
       	$result = null;
        if (preg_match(self::DOCCOMMENT_FILTER, $comment, $result))
        	$this->filter = $result[1];
        	
        $result = null;
        if (preg_match(self::DOCCOMMENT_DYNAMIC_TYPE, $comment, $result))
            $this->dynamicType = $result[1];
            
        $result = null;
        if (preg_match(self::DOCCOMMENT_PERMISSIONS, $comment, $result))
        	$this->permissions = $result[1]; 
        
        $result = null;
        if (preg_match(self::DOCCOMMENT_ALIAS_ACTION, $comment, $result))
        	$this->actionAlias = $result[1]; 	
            
        $result = null;
        if (preg_match(self::DOCCOMMENT_DISABLE_TAGS, $comment, $result))
        	$this->disableTags = explode(",", $result[1]);
        	
        $result = null;
        if (preg_match(self::DOCCOMMENT_VALIDATE_USER, $comment, $result))
        {
        	$this->validateUserObjectClass = $result[1];
        	$this->validateUserIdParamName = $result[2];
        	if(isset($result[3]) && strlen($result[3]))
			{
				$this->validateUserPrivilege = $result[3];
				if(isset($result[4]) && strlen($result[4]))
					$this->validateOptions = $result[4];
			}

        } 
        
        self::fillConstraint($comment, self::MIN_LENGTH_CONSTRAINT);
       	self::fillConstraint($comment, self::MAX_LENGTH_CONSTRAINT);
        self::fillConstraint($comment, self::MIN_VALUE_CONSTRAINT);
        self::fillConstraint($comment, self::MAX_VALUE_CONSTRAINT);
        
        $result = null;
        $error_array = array();
        if (preg_match_all(self::DOCCOMMENT_ACTION_ERRORS, $comment, $result))
        {
            foreach($result[1] as $index => $errorClass)
            {
            	$error = trim($result[2][$index]);
	            $apiErrorsReflected = new ReflectionClass($errorClass);
	            $apiErrors = $apiErrorsReflected->getConstants();
            
	            if(isset($apiErrors[$error]))
	            {
                	$error_array[] = array($error, $apiErrors[$error], $errorClass);
	            }
	            else
	            {
	            	KalturaLog::err("Constant [$error] not found in class [$errorClass]");
	            }
            }
        }
        $this->errors = $error_array;

		$this->disableRelativeTimeParams = $this->getDisableRelativeTimeParams($comment);
     }
     
     private function fillConstraint($comment, $constraintName) {
     	
     	$result = null;
     	$constraintRegex = self::DOCCOMMENT_VALIDATE_CONSTRAINT;
     	$constraintRegex = str_replace("CONSTRAINT", $constraintName, $constraintRegex);
     	if (preg_match_all($constraintRegex, $comment, $result)) {
     		$size = count($result[0]);
     		for($i = 0 ; $i < $size ; $i = $i += 1) {
     			$field = trim($result[1][$i]);
     			
     			if(!array_key_exists($field, $this->validateConstraints))
     				$this->validateConstraints[$field] = array();
     			
     			$this->validateConstraints[$field][$constraintName] = $result[2][$i];
     		}
     	}
     }

	private function getDisableRelativeTimeParams($comment)
	{
		$array = array();
		if (preg_match_all(self::DOCCOMMENT_DISABLE_RELATIVE_TIME, $comment, $result))
		{
			foreach($result[1] as $paramName)
			{
				$array[] = $paramName;
			}
		}

		return $array;
	}
}
