<?php

/**
 * sfCultureInfo class file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author     Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version    $Id: sfCultureInfo.class.php 3037 2006-12-15 08:05:13Z fabien $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfCultureInfo class.
 *
 * Represents information about a specific culture including the
 * names of the culture, the calendar used, as well as access to
 * culture-specific objects that provide methods for common operations,
 * such as formatting dates, numbers, and currency.
 *
 * The sfCultureInfo class holds culture-specific information, such as the
 * associated language, sublanguage, country/region, calendar, and cultural
 * conventions. This class also provides access to culture-specific
 * instances of sfDateTimeFormatInfo and sfNumberFormatInfo. These objects
 * contain the information required for culture-specific operations,
 * such as formatting dates, numbers and currency.
 *
 * The culture names follow the format "<languagecode>_<country/regioncode>",
 * where <languagecode> is a lowercase two-letter code derived from ISO 639
 * codes. You can find a full list of the ISO-639 codes at
 * http://www.ics.uci.edu/pub/ietf/http/related/iso639.txt
 *
 * The <country/regioncode2> is an uppercase two-letter code derived from
 * ISO 3166. A copy of ISO-3166 can be found at
 * http://www.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
 *
 * For example, Australian English is "en_AU".
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Sat Dec 04 13:41:46 EST 2004
 * @package System.I18N.core
 */
class sfCultureInfo
{
  /**
   * ICU data filename extension.
   * @var string
   */
  protected $dataFileExt = '.dat';

  /**
   * The ICU data array.
   * @var array
   */
  protected $data = array();

  /**
   * The current culture.
   * @var string
   */
  protected $culture;

  /**
   * Directory where the ICU data is stored.
   * @var string
   */
  protected $dataDir;

  /**
   * A list of ICU date files loaded.
   * @var array
   */
  protected $dataFiles = array();

  /**
   * The current date time format info.
   * @var sfDateTimeFormatInfo
   */
  protected $dateTimeFormat;

  /**
   * The current number format info.
   * @var sfNumberFormatInfo
   */
  protected $numberFormat;
  
  /**
   * A list of properties that are accessable/writable.
   * @var array
   */ 
  protected $properties = array();

  /**
   * Culture type, all.
   * @see getCultures()
   * @var int
   */
  const ALL = 0;

  /**
   * Culture type, neutral.
   * @see getCultures()
   * @var int
   */ 
  const NEUTRAL = 1;

  /**
   * Culture type, specific.
   *
   * @see getCultures()
   * @var int
   */ 
  const SPECIFIC = 2;

  /**
   * Display the culture name.
   *
   * @return string the culture name.
   * @see getName()
   */
  public function __toString()
  {
    return $this->getName();
  }

  /**
   * Allow functions that begins with 'set' to be called directly
   * as an attribute/property to retrieve the value.
   *
   * @return mixed
   */
  public function __get($name)
  {
    $getProperty = 'get'.$name;
    if (in_array($getProperty, $this->properties))
    {
      return $this->$getProperty();
    }
    else
    {
      throw new sfException('Property '.$name.' does not exists.');
    }
  }

  /**
   * Allow functions that begins with 'set' to be called directly
   * as an attribute/property to set the value.
   */
  public function __set($name, $value)
  {
    $setProperty = 'set'.$name;
    if (in_array($setProperty, $this->properties))
    {
      $this->$setProperty($value);
    }
    else
    {
      throw new sfException('Property '.$name.' can not be set.');
    }
  }

  /**
   * Initializes a new instance of the sfCultureInfo class based on the 
   * culture specified by name. E.g. <code>new sfCultureInfo('en_AU');</code>
   * The culture indentifier must be of the form 
   * "<language>_(country/region/variant)".
   *
   * @param string a culture name, e.g. "en_AU".
   * @return return new sfCultureInfo.
   */
  public function __construct($culture = 'en')
  {
    $this->properties = get_class_methods($this);

    if (empty($culture))
    {
      $culture = 'en';
    }

    $this->dataDir = $this->dataDir();
    $this->dataFileExt = $this->fileExt();

    $this->setCulture($culture);

    $this->loadCultureData('root');
    $this->loadCultureData($culture);
  }

  /**
   * Get the default directory for the ICU data.
   * The default is the "data" directory for this class.
   *
   * @return string directory containing the ICU data.
   */
  protected static function dataDir()
  {
    return sfConfig::get('sf_symfony_data_dir').'/i18n/';
  }

  /**
   * Get the filename extension for ICU data. Default is ".dat".
   *
   * @return string filename extension for ICU data.
   */
  protected static function fileExt()
  {
    return '.dat';
  }

  /**
   * Determine if a given culture is valid. Simply checks that the
   * culture data exists.
   *
   * @param string a culture
   * @return boolean true if valid, false otherwise.
   */
  public function validCulture($culture)
  {
    if (preg_match('/^[a-z]{2}(_[A-Z]{2,5}){0,2}$/', $culture))
    {
      return is_file(self::dataDir().$culture.self::fileExt());
    }

    return false;
  }

  /**
   * Set the culture for the current instance. The culture indentifier
   * must be of the form "<language>_(country/region)".
   *
   * @param string culture identifier, e.g. "fr_FR_EURO".
   */
  protected function setCulture($culture)
  {
    if (!empty($culture))
    {
      if (!preg_match('/^[a-z]{2}(_[A-Z]{2,5}){0,2}$/', $culture))
      {
        throw new sfException('Invalid culture supplied: '.$culture);
      }
    }

    $this->culture = $culture;
  }

  /**
   * Load the ICU culture data for the specific culture identifier.
   *
   * @param string the culture identifier.
   */
  protected function loadCultureData($culture)
  {
    $file_parts = explode('_',$culture);
    $current_part = $file_parts[0];

    $files = array($current_part);

    for ($i = 1, $max = count($file_parts); $i < $max; $i++)
    {
      $current_part .= '_'.$file_parts[$i];
      $files[] = $current_part;
    }

    foreach ($files as $file)
    {
      $filename = $this->dataDir.$file.$this->dataFileExt;

      if (is_file($filename) == false)
      {
        throw new sfException('Data file for "'.$file.'" was not found.');
      }

      if (in_array($filename, $this->dataFiles) == false)
      {
        array_unshift($this->dataFiles, $file);

        $data = &$this->getData($filename);
        $this->data[$file] = &$data;

        if (isset($data['__ALIAS']))
        {
          $this->loadCultureData($data['__ALIAS'][0]);
        }
        unset($data);
      }
    }
  }

  /**
   * Get the data by unserializing the ICU data from disk.
   * The data files are cached in a static variable inside
   * this function.
   *
   * @param string the ICU data filename
   * @return array ICU data 
   */
  protected function &getData($filename)
  {
    static $data  = array();
    static $files = array();

    if (!isset($files[$filename]))
    {
      $data[$filename]  = unserialize(file_get_contents($filename));
      $files[$filename] = true;
    }

    return $data[$filename];
  }

  /**
   * Find the specific ICU data information from the data.
   * The path to the specific ICU data is separated with a slash "/".
   * E.g. To find the default calendar used by the culture, the path
   * "calendar/default" will return the corresponding default calendar.
   * Use merge=true to return the ICU including the parent culture.
   * E.g. The currency data for a variant, say "en_AU" contains one
   * entry, the currency for AUD, the other currency data are stored
   * in the "en" data file. Thus to retrieve all the data regarding 
   * currency for "en_AU", you need to use findInfo("Currencies,true);.
   *
   * @param string the data you want to find.
   * @param boolean merge the data from its parents.
   * @return mixed the specific ICU data.
   */
  protected function findInfo($path = '/', $merge = false)
  {
    $result = array();
    foreach ($this->dataFiles as $section)
    {
      $info = $this->searchArray($this->data[$section], $path);

      if ($info)
      {
        if ($merge)
        {
          $result = array_merge($info, $result);
        }
        else
        {
          return $info;
        }
      }
    }

    return $result;
  }

  /**
   * Search the array for a specific value using a path separated using
   * slash "/" separated path. e.g to find $info['hello']['world'],
   * the path "hello/world" will return the corresponding value.
   *
   * @param array the array for search
   * @param string slash "/" separated array path.
   * @return mixed the value array using the path
   */
  protected function searchArray($info, $path = '/')
  {
    $index = explode('/', $path);

    $array = $info;

    for ($i = 0, $max = count($index); $i < $max; $i++)
    {
      $k = $index[$i];
      if ($i < $max - 1 && isset($array[$k]))
      {
        $array = $array[$k];
      }
      else if ($i == $max - 1 && isset($array[$k]))
      {
        return $array[$k];
      }
    }
  }
  
  /**
   * Gets the culture name in the format 
   * "<languagecode2>_(country/regioncode2)".
   *
   * @return string culture name.
   */
  public function getName()
  {
    return $this->culture;
  }

  /**
   * Gets the sfDateTimeFormatInfo that defines the culturally appropriate
   * format of displaying dates and times.
   *
   * @return sfDateTimeFormatInfo date time format information for the culture.
   */
  public function getDateTimeFormat()
  {
    if (is_null($this->dateTimeFormat))
    {
      $calendar = $this->getCalendar();
      $info = $this->findInfo("calendar/{$calendar}", true);
      $this->setDateTimeFormat(new sfDateTimeFormatInfo($info));
    }

    return $this->dateTimeFormat;
  }

  /**
   * Set the date time format information.
   *
   * @param sfDateTimeFormatInfo the new date time format info.
   */
  public function setDateTimeFormat($dateTimeFormat)
  {
    $this->dateTimeFormat = $dateTimeFormat;
  }

  /**
   * Gets the default calendar used by the culture, e.g. "gregorian".
   *
   * @return string the default calendar.
   */
  public function getCalendar()
  {
    $info = $this->findInfo('calendar/default');

    return $info[0];
  }

  /**
   * Gets the culture name in the language that the culture is set
   * to display. Returns <code>array('Language','Country');</code>
   * 'Country' is omitted if the culture is neutral.
   *
   * @return array array with language and country as elements, localized.
   */
  public function getNativeName()
  {
    $lang = substr($this->culture, 0, 2);
    $reg = substr($this->culture, 3, 2);
    $language = $this->findInfo("Languages/{$lang}");
    $region = $this->findInfo("Countries/{$reg}");
    if ($region)
    {
      return $language[0].' ('.$region[0].')';
    }
    else
    {
      return $language[0];
    }
  }

  /**
   * Gets the culture name in English.
   * Returns <code>array('Language','Country');</code>
   * 'Country' is omitted if the culture is neutral.
   *
   * @return array array with language and country as elements.
   */
  public function getEnglishName()
  {
    $lang = substr($this->culture, 0, 2);
    $reg = substr($this->culture, 3, 2);
    $culture = $this->getInvariantCulture();

    $language = $culture->findInfo("Languages/{$lang}");
    $region = $culture->findInfo("Countries/{$reg}");
    if ($region)
    {
      return $language[0].' ('.$region[0].')';
    }
    else
    {
      return $language[0];
    }
  }

  /**
   * Gets the sfCultureInfo that is culture-independent (invariant).
   * Any changes to the invariant culture affects all other
   * instances of the invariant culture.
   * The invariant culture is assumed to be "en";
   *
   * @return sfCultureInfo invariant culture info is "en".
   */
  static function getInvariantCulture()
  {
    static $invariant;

    if (is_null($invariant))
    {
      $invariant = new sfCultureInfo();
    }

    return $invariant;
  }

  /**
   * Gets a value indicating whether the current sfCultureInfo 
   * represents a neutral culture. Returns true if the culture
   * only contains two characters.
   *
   * @return boolean true if culture is neutral, false otherwise.
   */
  public function getIsNeutralCulture()
  {
    return strlen($this->culture) == 2;
  }

  /**
   * Gets the sfNumberFormatInfo that defines the culturally appropriate
   * format of displaying numbers, currency, and percentage.
   *
   * @return sfNumberFormatInfo the number format info for current culture.
   */
  public function getNumberFormat()
  {
    if (is_null($this->numberFormat))
    {
      $elements = $this->findInfo('NumberElements');
      $patterns = $this->findInfo('NumberPatterns');
      $currencies = $this->getCurrencies();
      $data = array('NumberElements' => $elements, 'NumberPatterns' => $patterns, 'Currencies' => $currencies);

      $this->setNumberFormat(new sfNumberFormatInfo($data));
    }

    return $this->numberFormat;
  }

  /**
   * Set the number format information.
   *
   * @param sfNumberFormatInfo the new number format info.
   */
  public function setNumberFormat($numberFormat)
  {
    $this->numberFormat = $numberFormat;
  }

  /**
   * Gets the sfCultureInfo that represents the parent culture of the 
   * current sfCultureInfo
   *
   * @return sfCultureInfo parent culture information.
   */
  public function getParent()
  {
    if (strlen($this->culture) == 2)
    {
      return $this->getInvariantCulture();
    }

    return new sfCultureInfo(substr($this->culture, 0, 2));
  }

  /**
   * Gets the list of supported cultures filtered by the specified 
   * culture type. This is an EXPENSIVE function, it needs to traverse
   * a list of ICU files in the data directory.
   * This function can be called statically.
   *
   * @param int culture type, sfCultureInfo::ALL, sfCultureInfo::NEUTRAL
   * or sfCultureInfo::SPECIFIC.
   * @return array list of culture information available. 
   */
  static function getCultures($type = sfCultureInfo::ALL)
  {
    $dataDir = sfCultureInfo::dataDir();
    $dataExt = sfCultureInfo::fileExt();
    $dir = dir($dataDir);

    $neutral = array();
    $specific = array();

    while (false !== ($entry = $dir->read()))
    {
      if (is_file($dataDir.$entry) && substr($entry, -4) == $dataExt && $entry != 'root'.$dataExt)
      {
        $culture = substr($entry, 0, -4);
        if (strlen($culture) == 2)
        {
          $neutral[] = $culture;
        }
        else
        {
          $specific[] = $culture;
        }
      }
    }
    $dir->close();

    switch ($type)
    {
      case sfCultureInfo::ALL:
        $all =  array_merge($neutral, $specific);
        sort($all);
        return $all;
        break;
      case sfCultureInfo::NEUTRAL:
        return $neutral;
        break;
      case sfCultureInfo::SPECIFIC:
        return $specific;
        break;
    }
  }

  /**
   * Simplify a single element array into its own value.
   * E.g. <code>array(0 => array('hello'), 1 => 'world');</code>
   * becomes <code>array(0 => 'hello', 1 => 'world');</code>
   *
   * @param array with single elements arrays
   * @return array simplified array.
   */
  protected function simplify($array)
  {
    for ($i = 0, $max = count($array); $i < $max; $i++)
    {
      $key = key($array);
      if (is_array($array[$key]) && count($array[$key]) == 1)
      {
        $array[$key] = $array[$key][0];
      }
      next($array);
    }

    return $array;
  }

  /**
   * Get a list of countries in the language of the localized version.
   *
   * @return array a list of localized country names. 
   */
  public function getCountries()
  {
    return $this->simplify($this->findInfo('Countries', true));
  }

  /**
   * Get a list of currencies in the language of the localized version.
   *
   * @return array a list of localized currencies.
   */
  public function getCurrencies()
  {
    return $this->findInfo('Currencies', true);
  }

  /**
   * Get a list of languages in the language of the localized version.
   *
   * @return array list of localized language names.
   */
  public function getLanguages()
  {
    return $this->simplify($this->findInfo('Languages', true));
  }

  /**
   * Get a list of scripts in the language of the localized version.
   *
   * @return array list of localized script names.
   */
  public function getScripts()
  {
    return $this->simplify($this->findInfo('Scripts', true));
  }

  /**
   * Get a list of timezones in the language of the localized version.
   *
   * @return array list of localized timezones.
   */
  public function getTimeZones()
  {
    return $this->simplify($this->findInfo('zoneStrings', true));
  }
}
