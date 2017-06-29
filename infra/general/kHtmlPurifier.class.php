<?php

require_once KALTURA_ROOT_PATH . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';

/**
 * @package infra
 * @subpackage utils
 */
class kHtmlPurifier
{
	private static $purifier = null;
	private static $cache = null;
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

		if ( $modifiedValue != $value )
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


			throw new KalturaAPIException(KalturaErrors::UNSAFE_HTML_TAGS, $className, $propertyName);
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
		self::$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_APC_LCAL);
		
		self::initHTMLPurifier();
		self::initAllowedProperties();
		self::initAllowedTokenPatterns();
	}
	
	public static function initHTMLPurifier()
	{
		$cacheKey = null;
		if(self::$cache)
		{
			$cacheKey = 'kHtmlPurifierPurifier-' . kConf::getCachedVersionId();
			self::$purifier = self::$cache->get($cacheKey);
		}
		
		if (!self::$purifier)
		{
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Cache.DefinitionImpl', null);
			self::$purifier = new HTMLPurifier($config);
			if(self::$cache)
			{
				self::$cache->set($cacheKey, self::$purifier);
			}
		}
	}
		
	public static function initAllowedProperties()
	{
		$cacheKey = null;
		if(self::$cache)
		{
			$cacheKey = 'kHtmlPurifierAllowedProperties-' . kConf::getCachedVersionId();
			self::$AllowedProperties = self::$cache->get($cacheKey);
		}
		
		if ( ! self::$AllowedProperties )
		{
			$allowedProperties = kConf::get("xss_allowed_object_properties");
			self::$AllowedProperties = $allowedProperties['base_list'];
			
			if (!kCurrentContext::$HTMLPurifierBaseListOnlyUsage)
				self::$AllowedProperties = array_merge($allowedProperties['base_list'], $allowedProperties['extend_list']);

			// Convert values to keys (we don't care about the values) in order to test via array_key_exists.
			self::$AllowedProperties = array_flip(self::$AllowedProperties);

			if (self::$cache)
			{
				self::$cache->set($cacheKey, self::$AllowedProperties);
			}
		}
	}

	public static function initAllowedTokenPatterns()
	{
		$cacheKey = null;
		if(self::$cache)
		{
			$cacheKey = 'kHtmlPurifierAllowedTokenPatterns-' . kConf::getCachedVersionId();
			self::$allowedTokenPatterns = self::$cache->get($cacheKey);
		}

		if ( ! self::$allowedTokenPatterns )
		{
			self::$allowedTokenPatterns = kConf::get("xss_allowed_token_patterns");
			self::$allowedTokenPatterns = preg_replace("/\\\\/", "\\", self::$allowedTokenPatterns);

			if (self::$cache)
			{
				self::$cache->set($cacheKey, self::$allowedTokenPatterns);
			}
		}
	}
}

kHtmlPurifier::init();
