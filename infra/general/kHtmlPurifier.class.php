<?php

require_once KALTURA_ROOT_PATH . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';

/**
 * @package infra
 * @subpackage utils
 */
class kHtmlPurifier
{
	const HTML_PURIFIER = 'html_purifier';
	const ALLOWED_TAGS = 'allowedTags';
	const ALLOWED_FRAME_TARGETS = 'allowedFrameTargets';
	const ALLOWED_REL = 'allowedRel';
	
	private static $purifier = null;
	private static $AllowedProperties = null;
	private static $allowedTokenPatterns;

	public static function purify( $className, $propertyName, $value )
	{
		if ( ! is_string($value)								// Skip objects like KalturaNullField, for example
			|| self::isMarkupAllowed($className, $propertyName)	// Skip fields that are allowed to contain HTML/XML tags
		)
		{
			return $value;
		}

		$tokenMapper = new kRegExTokenMapper();
		$tokenizedValue = $tokenMapper->tokenize($value, self::$allowedTokenPatterns);
		$purifiedValue = self::$purifier->purify( $tokenizedValue );
		$modifiedValue = $tokenMapper->unTokenize($purifiedValue);

		if (kCurrentContext::$HTMLPurifierBehaviour == HTMLPurifierBehaviourType::SANITIZE)
			return $modifiedValue;
		
		$valueTrimmedSpace = preg_replace('/\s+/', '', $value);
		$modifiedValueTrimmedSpace = preg_replace('/\s+/', '', $modifiedValue);
		
		if ($modifiedValueTrimmedSpace != $valueTrimmedSpace)
		{
			$msg = "Potential Unsafe HTML tags found in $className::$propertyName"
					. "\nORIGINAL VALUE: [" . $value . "]"
					. "\nMODIFIED VALUE: [" . $modifiedValue . "]"
				;
			KalturaLog::err( $msg );

			if (kCurrentContext::$HTMLPurifierBehaviour == HTMLPurifierBehaviourType::NOTIFY)
			{
//			$this->notifyAboutHtmlPurification($className, $propertyName, $value);
				KalturaLog::debug("should send notification");
				return $value;
			}
			// If we reach here kCurrentContext::$HTMLPurifierBehaviour must be BLOCK

			$errorMessage = "UNSAFE_HTML_TAGS;Potential Unsafe HTML tags found in [$className]::[$propertyName]";
			throw new Exception($errorMessage);
		}

		return $value;
	}

	public static function isMarkupAllowed( $className, $propertyName )
	{
		// Is it an excluded property?
		if ( array_key_exists($className . ":" . $propertyName, self::$AllowedProperties) )
		{
			return true;
		}

		return false;
	}
	
	public static function init()
	{
		self::initHTMLPurifier();
		self::initAllowedProperties();
		self::initAllowedTokenPatterns();
	}
	
	public static function initHTMLPurifier()
	{
		$cacheKey = null;
		if ( function_exists('apc_fetch') && function_exists('apc_store') )
		{
			$cacheKey = 'kHtmlPurifierPurifier-' . kConf::getCachedVersionId();
			self::$purifier = apc_fetch($cacheKey);
		}
		
		if ( ! self::$purifier )
		{
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Cache.DefinitionImpl', null);
			$config->set('HTML.Doctype', "HTML 4.01 Transitional");
			$htmlPurifierConf = kConf::get(self::HTML_PURIFIER, kConfMapNames::RUNTIME_CONFIG, array());
			if ($htmlPurifierConf)
			{
				if (isset($htmlPurifierConf[self::ALLOWED_TAGS]))
				{
					$config->set('HTML.Allowed', $htmlPurifierConf[self::ALLOWED_TAGS]);
				}
				if (isset($htmlPurifierConf[self::ALLOWED_FRAME_TARGETS]))
				{
					$config->set('Attr.AllowedFrameTargets', $htmlPurifierConf[self::ALLOWED_FRAME_TARGETS]);
				}
				if (isset($htmlPurifierConf[self::ALLOWED_REL]))
				{
					$config->set('Attr.AllowedRel', $htmlPurifierConf[self::ALLOWED_REL]);
				}
			}
			
			self::$purifier = new HTMLPurifier($config);
			
			if ( $cacheKey )
			{
				apc_store( $cacheKey, self::$purifier );
			}
		}
	}
		
	public static function initAllowedProperties()
	{
		if ( ! self::$AllowedProperties )
		{
			$xssAllowedObjectProperties = kConf::get('xss_allowed_object_properties');
			$AllowedProperties = $xssAllowedObjectProperties['base_list'];
			if (!kCurrentContext::$HTMLPurifierBaseListOnlyUsage)
			{
				$AllowedProperties = array_merge($AllowedProperties, $xssAllowedObjectProperties['extend_list']);
			}
			// Convert values to keys (we don't care about the values) in order to test via array_key_exists.
			self::$AllowedProperties = array_flip($AllowedProperties);
		}
	}

	public static function initAllowedTokenPatterns()
	{
		$cacheKey = null;
		if ( function_exists('apc_fetch') && function_exists('apc_store') )
		{
			$cacheKey = 'kHtmlPurifierAllowedTokenPatterns-' . kConf::getCachedVersionId();
			self::$allowedTokenPatterns = apc_fetch($cacheKey);
		}

		if ( ! self::$allowedTokenPatterns )
		{
			self::$allowedTokenPatterns = kConf::get("xss_allowed_token_patterns");
			self::$allowedTokenPatterns = preg_replace("/\\\\/", "\\", self::$allowedTokenPatterns);

			if ( $cacheKey )
			{
				apc_store( $cacheKey, self::$allowedTokenPatterns );
			}
		}
	}
}

kHtmlPurifier::init();
