<?php
/**
 * @ignore
 */
class KalturaDocCommentParser
{
    const DOCCOMMENT_READONLY = "/\\@readonly/i";
    const DOCCOMMENT_INSERTONLY = "/\\@insertonly/i";
    const DOCCOMMENT_WRITEONLY = "/\\@writeonly/i";
    
    const DOCCOMMENT_PARAM = '/\@param (\w*) \$__NAME__ ?(.*)/';
    const DOCCOMMENT_REPLACENET_PARAM_NAME = '__NAME__';
    
    const DOCCOMMENT_VAR_TYPE = "/\\@var (\\w*)/";
    const DOCCOMMENT_LINK = "/\\@link (.*)/";
    const DOCCOMMENT_DESCRIPTION = "/\\* [a-zA-Z\\-\\s].*/";
    const DOCCOMMENT_RETURN_TYPE = "/\\@return (\\w*)/";
    
    const DOCCOMMENT_SERVICE_NAME =  "/\\@service\\s?(\\w*)/";
    
    const DOCCOMMENT_ACTION = "/\\@action\\s?(\\w*)/";
    const DOCCOMMENT_ACTION_ERRORS = "/\\@throws (.*)::(.*)/";
    
    const DOCCOMMENT_PACKAGE = "/\\@package (\\w*)/";
    const DOCCOMMENT_SUBPACKAGE = "/\\@subpackage (\\w*)/";
    
    const DOCCOMMENT_CLIENT_GENERATOR = "/\\@clientgenerator (\\w*)/";
    
    const DOCCOMMENT_FILTER = "/\\@filter ([\\w\\,\\s]*)/";
    
    const DOCCOMMENT_ABSTRACT = "/\\@abstract/i";
    
    const DOCCOMMENT_DEPRECATED = "/\\@deprecated/i";
    
    const DOCCOMMENT_SERVER_ONLY = "/\\@serverOnly/i";
    
    const DOCCOMMENT_DYNAMIC_TYPE = "/\\@dynamicType (\\w*)/i";
    
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
     * Parse a docComment
     *
     * @param string $comment
     * @param array $replacements Optional associative array for replacing values in search patterns
     * @return array
     */
    function KalturaDocCommentParser($comment , $replacements = null)
    {
        $this->readOnly = preg_match( self::DOCCOMMENT_READONLY, $comment);
        $this->insertOnly = preg_match( self::DOCCOMMENT_INSERTONLY, $comment);
        $this->writeOnly = preg_match( self::DOCCOMMENT_WRITEONLY, $comment);
        $this->abstract = preg_match( self::DOCCOMMENT_ABSTRACT, $comment);
        $this->deprecated = preg_match( self::DOCCOMMENT_DEPRECATED, $comment);
        $this->serverOnly = preg_match( self::DOCCOMMENT_SERVER_ONLY, $comment);

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
        if (preg_match( self::DOCCOMMENT_VAR_TYPE, $comment, $result ))
            $this->varType = $result[1];
            
        $result = null;
        if (preg_match_all( self::DOCCOMMENT_DESCRIPTION, $comment, $result ))
            $this->description = preg_replace("/(\\*\\s*)/", "", implode("\n", $result[0]));
            
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
                	$error_array[] = array ( $error , $apiErrors[$error]);
	            }
	            else
	            {
	            	KalturaLog::err("Constant [$error] not found in class [$errorClass]");
	            }
            }
        }
        $this->errors = $error_array;        
     }
}