<?php

require_once KALTURA_ROOT_PATH . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';

/**
 * @package infra
 * @subpackage utils
 */
class kHtmlPurifier
{
	private static $purifier = null;
	private static $AllowedProperties = null;

	public static function purify( $className, $propertyName, $value )
	{
		if ( ! is_string($value)								// Skip objects like KalturaNullField, for example
			|| self::isMarkupAllowed($className, $propertyName)	// Skip fields that are allowed to contain HTML/XML tags
		)
		{
			return $value;
		}

		$modifiedValue = self::$purifier->purify( $value );

		if ( $modifiedValue != $value )
		{
			$msg = "Potential Unsafe HTML tags found in $className::$propertyName"
					. "\nORIGINAL VALUE: [" . $value . "]"
					. "\nMODIFIED VALUE: [" . $modifiedValue . "]"
				;

			KalturaLog::err( $msg );

			// We're currently in monitoring mode so we won't perform any action.
			// Real code should:
			//		throw an exception if we do not allow unknown tags in input
			// or
			//		return $modifiedString; ==> if we will force-remove unknown tags
		}

		return $value;
	}

	public static function isMarkupAllowed( $className, $propertyName )
	{
		// Is it an excluded property?
		if ( array_key_exists($className, self::$AllowedProperties)
				&& array_key_exists($propertyName, self::$AllowedProperties[$className]) )
		{
			return true;
		}

		return false;
	}
	
	public static function init()
	{
		self::initHTMLPurifier();
		self::initAllowedProperties();
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
			$config->set('Cache.DefinitionImpl', false);
			self::$purifier = new HTMLPurifier($config);
			if ( $cacheKey )
			{
				apc_store( $cacheKey, self::$purifier );
			}
		}
	}
		
	public static function initAllowedProperties()
	{
		$cacheKey = null;
		if ( function_exists('apc_fetch') && function_exists('apc_store') )
		{
			$cacheKey = 'kHtmlPurifierAllowedProperties-' . kConf::getCachedVersionId();
			self::$AllowedProperties = apc_fetch($cacheKey);
		}
		
		if ( ! self::$AllowedProperties )
		{
			self::$AllowedProperties = array(
					'entry' => array(
							'categories' => 1,
							'playlistContent' => 1
					),
					'uiConf' => array(
							'config' => 1,
							'confFile' => 1,
							'confFileFeatures' => 1
					),
					'metadata' => array(
							'xml' => 1
					),
					'metadataProfile' => array(
							'xsd' => 1,
							'xslt' => 1,
							'views' => 1
					),
					'KalturaDataEntry' => array(
							'dataContent' => 1
					),
					'KalturaGenericXsltSyndicationFeed' => array(
							'xslt' => 1
					),
				);

			if ( $cacheKey )
			{
				apc_store( $cacheKey, self::$AllowedProperties );
			}
		}
	}
}

kHtmlPurifier::init();