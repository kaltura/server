<?php

/*--------------------------------------------------------------------------
 About the class - class.MySpace.php

 This class is used to add data to existing MySpace.com user profile using
 PHP cURL. User is authenticated with their login information and data
 - in form of plain text or HTML code - is saved with the existing profile
 data. User selects the section of their profile where the data is to be
 added by this class.

 Version 1.0, Created 04/09/2006

 Author  - Ehsan Haque
 Web     - http://ehsan.bdwebwork.com/

 Bug Fixed & Updated by - MA Razzaque Rupom
 Web     - http://www.rupom.info

 ### Updated by Eugene Medynskiy (eugene.medynskiy@kaltura.com)
 ### Changes:
 01/18/2007: Changed var to public/private, where appropriate.
 01/18/2007: Commented out line 642. Not setting CURL headers.
 01/22/2007: Changer user-agent to modern firefox.
 01/22/2007: addDataURI/getDataURI are now set dynamically after logging in.
 01/22/2007: Added new errors to handle page parsing errors and cookie writing errors.
 01/22/2007: Stronger cURL error handling.
 01/22/2007: New data is added to the end of a given section.
 01/25/2007: <textarea> tags on the Edit Profile page can now span multiple lines.
 01/25/2007: Can now locate the URL for the Edit Profile page using either the "Skip this Advertisement" or "Edit Profile" links.

 06/12/2007: modified AboutMeText -> AboutMeTextBox
 License: GPL
 --------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
 Public Variables - Alphabetical List

 + cookieJarPath ----------------  Absolute path to write Cookie file
 + data          ----------------  Data to be added
 + email         ----------------  Email for Login
 + isUsingProxy  ----------------  Specifies if the server is using proxy
 + password      ----------------  Password for Login
 + proxyHost     ----------------  Host address for proxy
 + proxyPort     ----------------  Port number for proxy
 + section       ----------------  Section to add data
 --------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
 Private Variables - Alphabetical List

 + addDataURI          ----------  URL to be used to post data
 + allData             ----------  Existing profile data from MySpace.com
 + cookie              ----------  Set to 1 (true) if cookie is to be used
 in header
 + cookieFile          ----------  Temporary cookie file name
 + cookieFileJar       ----------  Temporary file where cookie is saved
 + follow              ----------  Set to 1 (true) if header redirection(s)
 are to be performed
 + getDataURI          ----------  URL to retrieve existing data
 + loginURI            ----------  URL for login to MySpace.com
 + postFields          ----------  Set the fields to be sent via POST as name=value
 + proxy               ----------  Proxy server address as host:port
 + referer             ----------  Referer URL used by cURL
 + returnTransfer      ----------  Set to 1 (true) if the output is to
 saved in as a string
 + sectionFieldName    ----------  Form field name for selected section
 + showHeader          ----------  Set to 1 (true) if header information
 is to be printed in the output
 + statusMsg           ----------  Success or Failure message
 + url                 ----------  URL to be used by cURL
 + userAgent           ----------  User agent used by cURL
 + usePostField        ----------  Set to 1 (true) if any data is
 sent via POST
 --------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
 Functions - Alphabetical List
 --------------------------------------------------------------------------*/

class MySpace
{
	/*--------------------------------------------------------------------------
  Public Variables
  --------------------------------------------------------------------------*/


	/**
  * MySpace.com Email address for login
  * @public
  * @var string
  */
	public $email              = "";

	/**
  * MySpace.com Password for login
  * @public
  * @var string
  */
	public $password           = "";

	/**
  * MySpace.com Section for data addition
  * @public
  * @var string
  */
	public $section            = "";

	/**
  * Data that is to be added to MySpace.com selected Section
  * @public
  * @var string
  */
	public $data               = "";

	/**
  * Abosolute path to save the cookie
  * Default value is DOCUMENT_ROOT
  * @public
  * @var string
  */
	public $cookieJarPath      = "";

	/**
  * Specifies if Proxy server required as Gateaway
  * Default value is false
  * @public
  * @var boolean
  */
	public $isUsingProxy       = false;

	/**
  * Proxy host name
  * @public
  * @var string
  */
	public $proxyHost          = "";

	/**
  * Proxy port number
  * @public
  * @var int
  */
	public $proxyPort          = 0;

	public  $err_code = 0;
	/*--------------------------------------------------------------------------
  Private Variables
  --------------------------------------------------------------------------*/

	/**
  * URL to Authenticate user on MySpace.com
  * @private
  * @var string
  */
	#  private $loginURI           = "http://login.myspace.com/index.cfm?fuseaction=login.process&MyToken=a0ef3ff6-5b8c-4d7b-9fdb-38c2e5df01f5";
	private $loginURI           = "http://login.myspace.com/index.cfm?fuseaction=login.process";

	/**
  * URL to Authenticate user on MySpace.com
  * @private
  * @var string
  */
	#  private $addDataURI          = "http://profileedit.myspace.com/Modules/ProfileEdit/Pages/Interests.aspx?";//fuseaction=profile.interests&MyToken=16699ec3-b0e5-40ff-9266-bb32a1ab9f50";
	private $addDataURI          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests&MyToken=ac27e4e0-51bc-4b18-8a41-b5a46a9aee28";

	/**
  * URL to Authenticate user on MySpace.com
  * @private
  * @var string
  */
	#  private $getDataURI          = "http://profileedit.myspace.com/Modules/ProfileEdit/Pages/Interests.aspx?";//fuseaction=profile.interests&MyToken=16699ec3-b0e5-40ff-9266-bb32a1ab9f50";
	private $getDataURI          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests&MyToken=ac27e4e0-51bc-4b18-8a41-b5a46a9aee28";

	/**
  * URL to be used by cURL
  * @private
  * @var string
  */
	private $url                = "";

	/**
  * User agent (used to trick Yahoo!)
  * @private
  * @var string
  */
	private $userAgent          = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061208 Firefox/2.0.0.1";

	/**
  * Referer URL (used to trick Yahoo!)
  * @private
  * @var string
  */
	private $referer            = "http://www.myspace.com";

	/**
  * Specifies whether output includes the header
  * @private
  * @var int
  */
	private $showHeader         = 0;

	/**
  * Specifies if cURL should follow the redirected URL
  * @private
  * @var int
  */
	private $follow             = 0;

	/**
  * Specifies if cURL should save the output to a string
  * @private
  * @var int
  */
	private $returnTransfer     = 1;

	/**
  * Specifies number of post fields to pass
  * @private
  * @var int
  */
	private $usePostField       = 0;

	/**
  * Specify fields to send via POST method as key=value
  * @private
  * @var string
  */
	private $postFields         = "";

	/**
  * All profile data from MySpace.com
  * @private
  * @var array
  */
	private $allData            = array();

	/**
  * Specify field name for the selected Section
  * @private
  * @var string
  */
	private $sectionFieldName   = "";

	/**
  * File where Cookie is temporarily saved
  * @private
  * @var string
  */
	private $cookieFileJar      = "";

	/**
  * Cookie File that is read by service process
  * This carries same value as $cookieFileJar
  * @private
  * @var string
  */
	private $cookieFile         = "";

	/**
  * Specifies if Cookie is to be in header
  * @private
  * @var int
  */
	private $cookie             = 0;

	/**
  * Proxy address as proxy.host:port
  * @private
  * @var string
  */
	private $proxy              = "";

	/**
  * Status message as found from the profile update page
  * @private
  * @var string
  */
	private $statusMsg          = "";

	private $hit_num 			= 1;

	private $hit_stack = array();
	
	private $hit_results = array();

	private $log;
	
	private $errorCount = 0;
	
	private static $cookiePath;
	
	
	
	/*--------------------------------------------------------------------------
  Function Definitions
  --------------------------------------------------------------------------*/

	private static function init ()
	{
		// use the tmp directory 
		// create it if does not exist
		kFile::fullMkdir ( self::getMySpaceCookiePath() . ".txt" );
	}
	
	public static function  getMySpaceCookiePath()
	{
		$tmp_dir = getenv ("TEMP");
		if ( empty ( $tmp_dir ) ) $tmp_dir = "/tmp";
		
		self::$cookiePath = kFile::fixPath( $tmp_dir . DIRECTORY_SEPARATOR . "kaltura/myspace/cookies" );
		return self::$cookiePath ;
	}
	
	public function getHitStack()
	{
		return $this->hit_stack;
	}

	function execService ($email, $password, $section, $data)
	{
		$this->log = new mySpaceLog ( $email );
		$this->log ( "Data to inject\n" . $data . "\n" );

		$result = $this->execServiceImpl ($email, $password, $section, $data);
		
		if ( true || $this->errorCount > 0 )
		{
			$this->log->write();
		}
	}
	/**
  * Executes the Service
  * @param string $login Username of user's MySpace.com Account
  * @param string $password Password of the user's MySpace.com Account
  * @param string $section Section where user's information will be added
  * @param string $data Data that is to be added to the selected section
  * @return array|false
  */
	function execServiceImpl ($email, $password, $section, $data)
	{
		self::init();
		
		$email      = trim($email);
		$password   = trim($password);

		
		if (empty($email))
		{
			$this->setError("provide_email");
			return false;
		}

		if (empty($password))
		{
			$this->setError("provide_pass");
			return false;
		}

		if (empty($section))
		{
			$this->setError("select_section");
			return false;
		}

		if (empty($data))
		{
			$this->setError("empty_data");
			return false;
		}

		$this->email      = $email;
		$this->password   = $password;
		$this->section    = $section;
		$this->data       = $data;

		$this->setSectionFieldName();


		// Instructs to authenticate user on MySpace.com
		$this->auth       = $this->doAuthentication();

		$this->log  ( "Result of doAuthentication: " . $this->auth  . " \n" );
		if ($this->auth)
		{
			// Adds data to the selected secion
			$this->doAddData();
		}

		// Unlinks the cookie file
		$this->unlinkFile($this->cookieFileJar);
	}

	/**
  * Sets Service URL
  * @return void
  */
	function setSectionFieldName()
	{
		if (empty($this->section))
		{
			$this->setError("select_section");
			return false;
		}

		//$this->sectionFieldName   = 'ctl00$ctl00$Main$ProfileEditContent$editInterests$';
		$this->sectionFieldName   = 'ctl00$ctl00$cpMain$ProfileEditContent$editInterests$';

		// Sets the URL depending on the choosen service
		switch ($this->section)
		{
			case 'aboutme'      : $this->sectionFieldName   .= "AboutMeTextBox"; break; // Added "Box"

			case 'liketomeet'   : $this->sectionFieldName   .= "LikeToMeetText"; break;

			case 'general'      : $this->sectionFieldName   .= "GeneralText"; break;

			case 'music'        : $this->sectionFieldName   .= "MusicText"; break;

			case 'movies'       : $this->sectionFieldName   .= "MoviesText"; break;

			case 'television'   : $this->sectionFieldName   .= "TelevisionText"; break;

			case 'books'        : $this->sectionFieldName   .= "BooksText"; break;

			case 'heroes'       : $this->sectionFieldName   .= "HeroesText"; break;
		}
	}

	/**
  * Authenticates user on MySpace.com
  * @return boolean
  */
	function doAuthentication()
	{
		// Instructs to initialize cURL session
		$this->initCurl();

		// Sets the URL for authentication purpose
		$this->url              = $this->loginURI;

		// Sets the number of fields to send via HTTP POST
		$this->usePostField     = 1;

		// Sets the fields to be sent via HTTP POST as key=value
		$this->postFields       = "email=$this->email&password=$this->password&Remember=&NextPage=fuseaction=profile.interests";
		$this->follow = 1;

		// Instructs to set Cookie Jar
		if ($this->setCookieJar() == FALSE) {
			$this->setError("cookie_error");
			return false;
		}

		// Checks if the cURL options are all set properly
		if ($this->setCurlOption())
		{
			// Instructs to execute cURL session
			$this->hit_stack [] = "doAuthentication";
			$this->execCurl();

			// Checks if any cURL error is generated
			if ($this->getCurlError())
			{
				$this->unlinkFile($this->cookieFileJar);
				$this->setError("curl_error");
				return false;
			}

			$this->log  ( "step: 1\n" );
			// Checks if the authentication failed, either invalid login or username is not registered
			if (preg_match("/Must Be Logged-In/i", $this->outputContent))
			{
				// Instructs to close cURL session
				$this->closeCurl();

				// Unlinks the cookie file
				$this->unlinkFile($this->cookieFileJar);

				$this->setError("invalid_login");
				return false;
			}

			$this->log  ( "step: 2\n" );
			// Get the URL we are supposed to use for the Edit Profile action
			$link = self::getLinkString( $this->outputContent , "Skip this Advertisement");
			
			if ( $link != NULL )
			{
				$href = self::getHrefValue( $link );
				
				if ( ! kString::beginsWith( $href , "http") )
				{
					$href = "http://login.myspace.com/index.cfm?" . $href;
				}
				$this->log  ( "step: 3, Skip this Advertisement\n** href ** . $href " );

				$this->addDataURI = $href;
				$this->getDataURI = $href;
				$this->url = $href;
//				$this->postFields = str_replace ( "fuseaction=profile.interests" , "fuseaction=user" , $this->postFields );

				$this->follow = 1;
				$this->setCurlOption();
				
				// have to follow an object that moved
				$this->execCurl();
				// Checks if any cURL error is generated
				if ($this->getCurlError())
				{
					$this->unlinkFile($this->cookieFileJar);
					$this->setError("curl_error");
					return false;
				}
				
				$this->log  ( "step: 200, After - Skip this Advertisement\n" );
				
				$link = self::getLinkString( $this->outputContent , "here" , "Object moved to");
				if ( $link != NULL )
				{
					$this->log  ( "step: 300, Object moved to\n" );
					$href = self::getHrefValue( $link );
										
					$this->addDataURI = $href;
					$this->getDataURI = $href;
				}
				else
				{
					$this->log  ( "step: 400, cannot follow href\n" );
				}
			}

			$this->log  ( "step: 4\n" );
			// Try finding an 'Edit Profile' link

			$link = self::getLinkString( $this->outputContent , "Edit Profile" );
			$refinedURL = self::getHrefValue( $link );
			
			if ( $refinedURL == NULL )
			{
				$this->log  ( "step: 7\n" );
				$this->setError("parse_error");
				return false;
			}
			$this->log  ( "step: 6\n " );
			$this->addDataURI = $refinedURL;
			$this->getDataURI = $refinedURL;

			$this->log  ( "addDataURI: " . $this->addDataURI . " getDataURI: " . $this->getDataURI . "\n" );
		

			$this->log  ( "step: 9\n" );
			$this->closeCurl();
		}

		unset($this->outputContent);

		$this->log  ( "step: 10- ended doAuthentication successfully\n" );
		return true;
	}

	/**
  * Adds the profile data on MySpace.com
  * @return array|false
  */
	function doAddData()
	{
		$this->log  ( "step: 20, before getAllData\n" );
		$this->getAllData();

		if (empty($this->allData))
		{
			$this->log  ( "step: 21\n" );
			if($this->errorCount == 0)
			{
				$this->log  ( "step: 22\n" );
				$this->setError("profile_data_empty");
			}

			$this->log  ( "step: 23\n" );
			return false;
		}

		$this->log  ( "step: 24, after getAllData\n" );
		$this->initCurl();
		$this->url              = $this->addDataURI;

		$this->log  ( "step: 25\n" );
		#    $this->referer          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests&MyToken=a0ef3ff6-5b8c-4d7b-9fdb-38c2e5df01f5";
		#    $this->referer          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests";
		$this->referer = $this->addDataURI;

		$this->log  ( "step: 26\n" );
		$this->usePostField     = 1;

		$this->header           = 0;

		$this->log  ( "step: 30 - all post data to send before updating: " . print_r ( $this->allData , true ) . "\n\n" );
		
		$this->log ( "Trying to inject data into " . $this->sectionFieldName );
		
		foreach ($this->allData as $key => $value)
		{
			if ( $key == $this->sectionFieldName )
			{
				$addValue = urlencode( $value . "<br>" . $this->data ) ;
				$this->log ( "Injected data into $key, appended to \n" . $value . "\nNew value\n" . $addValue );
			}
			else
			{
				$addValue  = urlencode($value);
			}
			$postFields[]         = "$key=$addValue";
		}

		$this->postFields       = implode("&", $postFields);

		$this->cookieFile       = $this->cookieFileJar;

		$this->follow           = 1;

		if ($this->setCurlOption())
		{
			$this->hit_stack [] = "doAddData";
			$this->execCurl();

			// Checks if any cURL error is generated
			if ($this->getCurlError())
			{
				$this->unlinkFile($this->cookieFileJar);
				$this->setError("curl_error");
				return false;
			}

			// Pattern to match the confirmation message
			$pattern      = "/profile\supdated/i";

			preg_match($pattern, $this->outputContent, $match);

			if (!empty($match))
			{
				$this->log ( "Profile updated successfully");
				$this->setStatusMessage(true);
			}
			else
			{
				$this->log ( "Did not find 'Profile Updated' in result");
				
				$pattern      = "/Security Measure/i";
				preg_match($pattern, $this->outputContent, $match);
				
				if (!empty($match))
				{
					$this->log ( "Profile updated successfully");
					$this->setError("security_measures");
					$this->setStatusMessage(false);
				}
				
				$this->setStatusMessage(false);
			}

			$this->closeCurl();

			return true;
		}

		return false;
	}

	/**
  * Gets the profile data from MySpace.com
  * @return array|false
  */
	function getAllData()
	{
		$this->initCurl();
		$this->url              = $this->getDataURI;
		$this->referer          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests&MyToken=320d729a-883e-492b-8d4d-8593ef45b63e";
		#    $this->referer          = "http://profileedit.myspace.com/index.cfm?fuseaction=profile.interests";
		$this->header           = 0;
		$this->cookieFile       = $this->cookieFileJar;
		$this->follow           = 0;


			
		$this->log  ( "step: 50, getAllData start\n" );
		if ($this->setCurlOption())
		{
			$this->log  ( "step: 51\n" );
			$this->hit_stack [] = "getAllData";
			$this->execCurl();

			$this->log  ( "step: 52\n" );
			// Checks if any cURL error is generated
			if ($this->getCurlError())
			{
				$this->log  ( "step: 53\n" );
				$this->unlinkFile($this->cookieFileJar);
				$this->setError("curl_error");
				return false;
			}

			$this->log  ( "step: 54, getAllData - fetched field page successfully\n" );

			// Searching for HIDDEN INPUT tags
			$patternHiddens   = "/\<input+.+hidden+.+>/iU";
			preg_match_all($patternHiddens, $this->outputContent, $matchHiddens);

			$this->log  ( "step: 55\n" );
			if (!empty($matchHiddens[0]))
			{
				$this->log  ( "step: 56\n" );
				// Processing HIDDEN INPUT tags as name = value
				foreach ($matchHiddens[0] as $key => $value)
				{
					$patterForName    = "/name=\"\S*\"/iU";
					preg_match($patterForName, $value, $matchName);
					$refinedName = str_replace("\"", "", str_replace("name=", "", $matchName[0]));

					$patterForValue   = "/value=\"\S*\"/iU";
					preg_match($patterForValue, $value, $matchValue);
					if ( !empty ( $matchValue[0] ))
					{
						$refinedValue = str_replace("\"", "", str_replace("value=", "", $matchValue[0]));
					}
					else
					{
						$refinedValue = "";
					}
					if ((preg_match("/VIEWSTATE/i", $refinedName)) || (preg_match("/hash/iU", $refinedName)))
					{
						$result[$refinedName] = $refinedValue;
					}
				}
			} else {
				$this->log  ( "step: 57\n" );
				$this->unlinkfile($this->cookieFileJar);
				$this->setError("parse_error");
				return false;
			}

			$this->log  ( "step: 58, getAllData added hidden fields\n" );

			// Searching for SUBMIT INPUT tags
			$patternSubmits   = "/\<input+.+submit+.+>/iU";
			preg_match_all($patternSubmits, $this->outputContent, $matchSubmits);

			$this->log  ( "step: 59\n" );
			if (!empty($matchSubmits[0]))
			{
				$this->log  ( "step: 60\n" );
				// Processing SUBMIT INPUT tags as name = value
				foreach ($matchSubmits[0] as $key => $value)
				{
					$patterForName    = "/name=\"\S*\"/iU";
					preg_match($patterForName, $value, $matchName);
					if(!isset($matchName[0])) continue;
					$refinedName = str_replace("\"", "", str_replace("name=", "", $matchName[0]));

					$patterForValue   = "/value=\".*?\"/iU";
					preg_match($patterForValue, $value, $matchValue);
					$refinedValue = str_replace("\"", "", str_replace("value=", "", $matchValue[0]));

					if (preg_match("/save/iU", $refinedValue))
					{
						$result[$refinedName] = $refinedValue;
					}
				}
			} else {
				$this->log  ( "step: 61\n" );
				$this->unlinkfile($this->cookieFileJar);
				$this->setError("parse_error");
				return false;
			}

			$this->log  ( "step: 62, getAllData added submit \n" );


			// write the result to file  
			// TODO - remove !!
/*			
			$file_name = "C:\\web\\kaltura\\alpha\\test\\dummy\\myspace_result_of_hit_2" ; 
			if ( file_exists( $file_name . ".txt")) 	kFile::moveFile ( $file_name . ".txt" , $file_name . time() . ".txt" );
			kFile::setFileContent( "C:\\web\\kaltura\\alpha\\test\\dummy\\myspace_result_of_hit_2.txt" , $this->outputContent);
	*/		
			
			// add all the inputs
			// search for all inputs of type text
			$patternInputs = "/<input.*?name=\"([^\\\"]*)\".*?type=\"text\".*?value=\"([^\\\"]*)\"[^>]*>/imU";
			preg_match_all($patternInputs, $this->outputContent, $matchInputs);

			$this->hit_stack [] = "$patternInputs: " . print_r ( $matchInputs, true );

			$this->log  ( "step: 63\n" );
			if (!empty($matchInputs[0]))
			{
				$this->log  ( "step: 64\n" );
				// Processing TEXTAREA tags as name = value
				$this->appendNameValue ( $matchInputs , $result );
			} else {
				// if there are no results - it might be that there are input fields with no value
				$this->hit_stack [] =  "Warning: pattern\n"  . $patternInputs . "\n" ; //. $this->outputContent;
			}

			
			// Searching for TEXTAREA tags
/*			$patternTAs = "/<textarea[^>]+name.*>(.*)<\/textarea>/imUs"; */
			$patternTAs = "/<textarea name=\"([^\"]*)\".*>(.*)<\/textarea>/imUs"; 
			preg_match_all($patternTAs, $this->outputContent, $matchTAs);

			$this->hit_stack [] = "patternTAs: " . print_r ( $matchTAs, true );

			$this->log  ( "step: 63\n" );
			if (!empty($matchTAs[0]))
			{
				$this->log  ( "step: 64\n" );
				// Processing TEXTAREA tags as name = value
				$this->appendNameValue ( $matchTAs , $result );
				//$this->appendNameValueOldAndBad ( $matchTAs , $matchName , $result );
				
			} else {

				$this->hit_stack [] =  "Failed: pattern\n"  . $patternTAs . "\n" ; //. $this->outputContent;
					
				$this->unlinkfile($this->cookieFileJar);
				$this->setError("parse_error");
				return false;
			}

			
			$this->closeCurl();

			$this->allData = $result;

			$this->log  ( "step: 65, getAllData ended successfully\n" );
			
			return true;
		}

		$this->log  ( "step: 70\n" );
		return false;
	}

	// assume : 
	// $matchTAs[0] - array of whole strings
	// $matchTAs[1] - array of names
	// $matchTAs[2] - array of values
	private function appendNameValue ( $matchTAs , &$result )
	{
		foreach ($matchTAs[0] as $index => $whole_string )
		{
			$refinedName = $matchTAs[1][$index];
			$refinedValue = $matchTAs[2][$index];
			$result[$refinedName] = $refinedValue;
		}
	}
	
	private function appendNameValueOldAndBad ( $matchTAs , $matchName , &$result )
	{
		foreach ($matchTAs[0] as $key => $value)
		{
			$patterForName    = "/name=\"\S*\"/iU";
			preg_match($patterForName, $value, $matchName);
			$refinedName = str_replace("\"", "", str_replace("name=", "", $matchName[0]));

			$patterForValues  = "/\<textarea.+>(.*\n*)*.*\<\/textarea>/imUs";
			preg_match($patterForValues, $value, $matchValue);

			if( !empty ( $matchValue[0] ) )
			{
				$replacePattern = array (
				'/<textarea.+\">/iU',
				'/<\/textarea>/iU'
                                  );
				$replaceText    = array (
				'',
				''
                                  );

				$refinedValue = preg_replace($replacePattern, $replaceText, $matchValue[0]);

				$result[$refinedName] = $refinedValue;
			}
		}
	}
	
	public static function getLinkString ( $content , $a_partial_text = NULL , $prefix_text = NULL )
	{
		if ( $content == NULL ) return NULL;
		if ( $a_partial_text == NULL )
		{
			$a_partial_text = "";
		}
		
		$inner_pattern = "";
		if (  $prefix_text != NULL )
		{
			$inner_pattern =   $prefix_text . "[^<]*" ; // between the text and the <a - allow everything except for '<'
		}
		
		$inner_pattern .= "<a [^\>]*>.*" . $a_partial_text . ".*<\/a>";
		//$inner_pattern .= "<a .*>.*<";
		
		$pattern  = "/" . $inner_pattern . "/iU" ;
		
		preg_match($pattern, $content , $matchLink);
		
		if( empty( $matchLink[0] ) )
		{
			return NULL;
		}
		
		return $matchLink[0];		
	}
	
	public static function getHrefValue ( $content )
	{
		if ( $content == NULL ) return NULL;
		$pattern   = "/href=\"([^\"]*)\"/iU";
		preg_match($pattern, $content , $matchLink);
		if(empty($matchLink[0]) || empty ( $matchLink[1]))
		{
			return NULL;
		}
		
		return $matchLink[1];
	}
	
	/**
  * Initializes cURL session
  * @return void
  */
	function initCurl()
	{
		$this->curlSession    = curl_init();
	}

	/**
  * Sets the Cookie Jar File where Cookie is temporarily saved
  * @return void
  */
	function setCookieJar()
	{
		// Sets the encrypted cookie filename using MySpace.com account username
		$this->cookieFilename = MD5($this->email);

		// Sets the Cookie Jar filename with an absolute path
		$this->cookieFileJar  = self::$cookiePath . $this->cookieFilename ; //(!empty($this->cookieJarPath)) ? $this->cookieJarPath . "/" . $this->cookieFilename : $_SERVER['DOCUMENT_ROOT'] . "/" . $this->cookieFilename;

		$this->log  ( "setCookieJar: " . $this->cookieFileJar . "\n" );

		return fopen($this->cookieFileJar, "w");
	}

	/**
  * Sets cURL options
  * @return boolean
  */
	function setCurlOption()
	{
		// Sets the User Agent
		curl_setopt($this->curlSession, CURLOPT_USERAGENT, $this->userAgent);

		// Sets the HTTP Referer
		curl_setopt($this->curlSession, CURLOPT_REFERER, $this->referer);

		// Sets the URL that PHP will fetch using cURL
		curl_setopt($this->curlSession, CURLOPT_URL, $this->url);

		// Sets the number of fields to be passed via HTTP POST
		curl_setopt($this->curlSession, CURLOPT_POST, $this->usePostField);

		// Sets the fields to be sent via HTTP POST as key=value
		curl_setopt($this->curlSession, CURLOPT_POSTFIELDS, $this->postFields);

		// Sets the filename where cookie information will be saved
		curl_setopt($this->curlSession, CURLOPT_COOKIEJAR, $this->cookieFileJar);

		// Sets the filename where cookie information will be looked up
		curl_setopt($this->curlSession, CURLOPT_COOKIEFILE, $this->cookieFile);

		#    curl_setopt($this->curlSession, CURLOPT_HEADER, $this->header);

		// Sets the option to set Cookie into HTTP header
		curl_setopt($this->curlSession, CURLOPT_COOKIE, $this->cookie);

		// Checks if the user needs proxy (to be set by user)
		if ($this->isUsingProxy)
		{
			// Checks if the proxy host and port is specified
			if ((empty($this->proxyHost)) || (empty($this->proxyPort)))
			{
				$this->setError("proxy_required");
				$this->unlinkFile($this->cookieFileJar);
				return false;
			}

			// Sets the proxy address as proxy.host:port
			$this->proxy          = $this->proxyHost . ":" . $this->proxyPort;
		}

		// Sets the proxy server as proxy.host:port
		curl_setopt($this->curlSession, CURLOPT_PROXY, $this->proxy);

		// Specifies whether to save the output to a string
		curl_setopt($this->curlSession, CURLOPT_RETURNTRANSFER, $this->returnTransfer);

		// Specifies whether to use all header redirections
		curl_setopt($this->curlSession, CURLOPT_FOLLOWLOCATION, $this->follow);

		return true;
	}

	/**
  * Executes cURL Session
  * @return void
  */
	function execCurl( $depth = 0 )
	{
		$start = time();
		$this->log  ( "hiting ... [" . $this->hit_num . "]");

		$hit_params = array ( "url" => $this->url , "usePostField" => $this->usePostField , "postFields" => $this->postFields );
		$this->hit_stack [] = 	$hit_params;
		
		$this->log  ( print_r ( $hit_params , true ) );
		
		$this->outputContent    = curl_exec($this->curlSession);
		$this->log  ( "ended hit [" . $this->hit_num . "] result of length [" . strlen( $this->outputContent ).	"]. took " . ( time() - $start ) . " seconds.");
		$this->hit_num++;

		$this->log  ( "execCurl: " . "\n---------------------------\n" . $this->outputContent . "\n---------------------------\n" ) ;
	}

	/**
  * Closes cURL session
  * @return void
  */
	function closeCurl()
	{
		curl_close($this->curlSession);
		unset($this->curlSession);
	}

	/**
  * Sets the either success/failure message
  * @return void
  */
	function setStatusMessage($status = null)
	{
		$this->statusMsg = ($status) ? "Profile updated successfully" : "Profile was not updated";
	}

	/**
  * Returns the status message
  * @return string
  */
	function getStatusMessage()
	{
		return $this->statusMsg;
	}

	/**
  * Unlinks any given file
  * @return void
  */
	function unlinkFile($fileToUnlink)
	{
		if (file_exists($fileToUnlink))
		{
			unlink($fileToUnlink);
		}
	}

	/**
  * Sets any cURL error generated
  * @return boolean
  */
	function getCurlError()
	{
		$this->curlError    = curl_error($this->curlSession);
		$this->log  ( "getCurlError: " . $this->curlError . "\n" );

		return (!empty($this->curlError)) ? true : false;
	}

	/**
  * Sets Error Information
  * @return void
  */
	function setError($error)
	{
		$msg  = (!empty($error)) ? $this->getErrorInfo($error) : null;
		$this->errorCount++;
		$this->errorInfo = $msg;
		
		$this->log ( "setError: " . $msg );
		$this->log ( debugUtils::st( true ) );
	}

	/**
  * Provides the Error message
  * @param string $error Error code for which error message is generated
  * @return string
  */
	function getErrorInfo($error)
	{
		switch ($error)
		{
			case 'provide_email'      : $msg  = "Please enter your MySpace e-mail address"; $this->err_code = -2 ; break;

			case 'provide_pass'       : $msg  = "Please enter your MySpace password"; $this->err_code = -3 ; break;

			case 'invalid_login'      : $msg  = "You provided incorrect login information."; $this->err_code = -4 ; break;
			
			case 'select_section'     : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -10 ; break;

			case 'profile_data_empty' : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -11 ; break;

			case 'empty_data'         : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -12 ; break;

			case 'proxy_required'     : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -13 ; break;
			//      case 'curl_error'         : $msg  = $this->curlError; break;
			case 'curl_error'         : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -14 ; break;

			case 'cookie_error'       : $msg  = "We were unable to update your MySpace profile." ;  $this->err_code = -15 ; break;

			case 'parse_error'        : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -16 ; break;
			
			case 'security_measures'  : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -17 ; break;
			
			case 'provide_service'    : $msg  = "We were unable to update your MySpace profile."; $this->err_code = -18 ; break;
			
		}

		return $msg;
	}
	
	private function log ( $str )
	{
		$this->log->log ( $str );
	}
}


class mySpaceLog 
{
	private $file_name;
	private $content = "";
	 
	public function mySpaceLog ( $name )
	{
		$name = trim ( $name );
		$name = preg_replace ( "/[^a-zA-Z0-9_\-]/" , "_" , $name );
		$this->file_name = $name;
	}
	
	public function log ( $str )
	{
		$this->content .= $str . "\n";
	}
	
	public function write()
	{
		$full_path =  self::getMySpaceLogPath() . $this->file_name . "/" . time() . ".log";
		kFile::fullMkdir( $full_path );
		file_put_contents( $full_path , $this->content );
	}
	
	public static function  getMySpaceLogPath()
	{
		$tmp_dir = getenv ("TEMP");
		if ( empty ( $tmp_dir ) ) $tmp_dir = "/tmp";
		
		$path = kFile::fixPath( $tmp_dir . DIRECTORY_SEPARATOR . "kaltura/myspace/logs/" );
		return $path;
	}	
}
?>
