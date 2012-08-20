<?php

/**
 * sfMessageSource_gettext class file.
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
 * @version    $Id: sfMessageSource_gettext.class.php 3148 2007-01-04 19:34:28Z fabien $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfMessageSource_gettext class.
 * 
 * Using Gettext MO format as the message source for translation.
 * The gettext classes are based on PEAR's gettext MO and PO classes.
 * 
 * See the MessageSource::factory() method to instantiate this class.
 * 
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 16:18:44 EST 2004
 * @package System.I18N.core
 */
class sfMessageSource_gettext extends sfMessageSource
{ 
  /**
   * Message data filename extension.
   * @var string 
   */
  protected $dataExt = '.mo';

  /**
   * PO data filename extension
   * @var string 
   */
  protected $poExt = '.po';

  /**
   * Separator between culture name and source.
   * @var string 
   */
  protected $dataSeparator = '.';

  function __construct($source)
  {
    $this->source = (string)$source;
  }

  /**
   * Load the messages from a MO file.
   *
   * @param string MO file.
   * @return array of messages.
   */
  protected function &loadData($filename)
  {
    $mo = TGettext::factory('MO',$filename);
    $mo->load();
    $result = $mo->toArray();

    $results = array();
    $count = 0;
    foreach ($result['strings'] as $source => $target)
    {
      $results[$source][] = $target;  //target
      $results[$source][] = $count++; //id
      $results[$source][] = '';       //comments
    }

    return $results;
  }

  /**
   * Determin if the MO file source is valid.
   *
   * @param string MO file
   * @return boolean true if valid, false otherwise. 
   */
  protected function isValidSource($filename)
  {
    return is_file($filename);
  }

  /**
   * Get the MO file for a specific message catalogue and cultural variant.
   *
   * @param string message catalogue
   * @return string full path to the MO file. 
   */
  protected function getSource($variant)
  {
    return $this->source.'/'.$variant;
  }

  /**
   * Get the last modified unix-time for this particular catalogue+variant.
   * Just use the file modified time.
   *
   * @param string catalogue+variant
   * @return int last modified in unix-time format.
   */
  protected function getLastModified($source)
  {
    return is_file($source) ? filemtime($source) : 0;
  }

  /**
   * Get all the variants of a particular catalogue.
   *
   * @param string catalogue name
   * @return array list of all variants for this catalogue.
   */
  protected function getCatalogueList($catalogue)
  {
    $variants = explode('_', $this->culture);
    $source = $catalogue.$this->dataExt;

    $catalogues = array($source);

    $variant = null;

    for ($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $catalogue.$this->dataSeparator.$variant.$this->dataExt;
      }
    }
    $byDir = $this->getCatalogueByDir($catalogue);
    $catalogues = array_merge($byDir,array_reverse($catalogues));

    return $catalogues;
  }

  /**
   * Traverse through the directory structure to find the catalogues.
   * This should only be called by getCatalogueList()
   *
   * @param string a particular catalogue.
   * @return array a list of catalogues. 
   * @see getCatalogueList()
   */
  protected function getCatalogueByDir($catalogue)
  {
    $variants = explode('_', $this->culture);
    $catalogues = array();

    $variant = null;

    for($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $variant.'/'.$catalogue.$this->dataExt;
      }
    }

    return array_reverse($catalogues);
  }

  /**
   * Get the variant for a catalogue depending on the current culture.
   *
   * @param string catalogue
   * @return string the variant. 
   * @see save()
   * @see update()
   * @see delete()
   */
  protected function getVariants($catalogue = 'messages')
  {
    if (empty($catalogue))
    {
      $catalogue = 'messages';
    }

    foreach ($this->getCatalogueList($catalogue) as $variant)
    {
      $file = $this->getSource($variant);
      $po = $this->getPOFile($file);
      if (is_file($file) || is_file($po))
      {
        return array($variant, $file, $po);
      }
    }

    return false;
  }

  protected function getPOFile($MOFile)
  {
    return substr($MOFile, 0, strlen($MOFile) - strlen($this->dataExt)).$this->poExt;
  }

  /**
   * Save the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.   
   */
  function save($catalogue='messages')
  {
    $messages = $this->untranslated;

    if (count($messages) <= 0)
    {
      return false;
    }

    $variants = $this->getVariants($catalogue);

    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException("Unable to save to file {$MOFile}, file must be writable.");
    }
    if (is_writable($POFile) == false)
    {
      throw new sfException("Unable to save to file {$POFile}, file must be writable.");
    }

    // set the strings as untranslated.
    $strings = array();
    foreach ($messages as $message)
    {
      $strings[$message] = ''; 
    }

    // load the PO
    $po = TGettext::factory('PO',$POFile);
    $po->load();
    $result = $po->toArray();

    $existing = count($result['strings']);

    // add to strings to the existing message list
    $result['strings'] = array_merge($result['strings'],$strings);

    $new = count($result['strings']);

    if ($new > $existing)
    {
      // change the date 2004-12-25 12:26
      $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');

      $po->fromArray($result);
      $mo = $po->toMO();
      if ($po->save() && $mo->save($MOFile))
      {
        if (!empty($this->cache))
        {
          $this->cache->clean($variant, $this->culture);
        }

        return true;
      }
      else
      {
        return false;
      }
    }

    return false;
  }

  /**
   * Delete a particular message from the specified catalogue.
   *
   * @param string the source message to delete.
   * @param string the catalogue to delete from.
   * @return boolean true if deleted, false otherwise. 
   */
  function delete($message, $catalogue = 'messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException("Unable to modify file {$MOFile}, file must be writable.");
    }

    if (is_writable($POFile) == false)
    {
      throw new sfException("Unable to modify file {$POFile}, file must be writable.");
    }

    $po = TGettext::factory('PO', $POFile);
    $po->load();
    $result = $po->toArray(); 

    foreach ($result['strings'] as $string => $value)
    {
      if ($string == $message)
      {
        $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');
        unset($result['strings'][$string]);

        $po->fromArray($result);
        $mo = $po->toMO();
        if ($po->save() && $mo->save($MOFile))
        {
          if (!empty($this->cache))
          {
            $this->cache->clean($variant, $this->culture);
          }

          return true;
        }
        else
        {
          return false;
        }
      }
    }

    return false;
  }

  /**
   * Update the translation.
   *
   * @param string the source string.
   * @param string the new translation string.
   * @param string comments
   * @param string the catalogue of the translation.
   * @return boolean true if translation was updated, false otherwise.
   */
  function update($text, $target, $comments, $catalogue = 'messages')
  {
    $variants = $this->getVariants($catalogue);
    if ($variants)
    {
      list($variant, $MOFile, $POFile) = $variants;
    }
    else
    {
      return false;
    }

    if (is_writable($MOFile) == false)
    {
      throw new sfException("Unable to update file {$MOFile}, file must be writable.");
    }

    if (is_writable($POFile) == false)
    {
      throw new sfException("Unable to update file {$POFile}, file must be writable.");
    }

    $po = TGettext::factory('PO',$POFile);
    $po->load();
    $result = $po->toArray();

    foreach ($result['strings'] as $string => $value)
    {
      if ($string == $text)
      {
        $result['strings'][$string] = $target;
        $result['meta']['PO-Revision-Date'] = @date('Y-m-d H:i:s');

        $po->fromArray($result);
        $mo = $po->toMO();

        if ($po->save() && $mo->save($MOFile))
        {
          if (!empty($this->cache))
          {
            $this->cache->clean($variant, $this->culture);
          }

          return true;
        }
        else
        {
          return false;
        }
      }
    }

    return false;
  }

  /**
   * Returns a list of catalogue as key and all it variants as value.
   *
   * @return array list of catalogues 
   */
  function catalogues()
  {
    return $this->getCatalogues();
  }

  /**
   * Returns a list of catalogue and its culture ID. This takes care
   * of directory structures.
   * E.g. array('messages','en_AU')
   *
   * @return array list of catalogues 
   */
  protected function getCatalogues($dir=null,$variant=null)
  {
    $dir = $dir?$dir:$this->source;
    $files = scandir($dir);

    $catalogue = array();

    foreach ($files as $file)
    {
      if (is_dir($dir.'/'.$file) && preg_match('/^[a-z]{2}(_[A-Z]{2,3})?$/',$file))
      {
        $catalogue = array_merge($catalogue, $this->getCatalogues($dir.'/'.$file, $file));
      }

      $pos = strpos($file,$this->dataExt);

      if ($pos > 0 && substr($file, -1 * strlen($this->dataExt)) == $this->dataExt)
      {
        $name = substr($file,0,$pos);
        $dot = strrpos($name,$this->dataSeparator);
        $culture = $variant;
        $cat = $name;
        if (is_int($dot))
        {
          $culture = substr($name, $dot+1,strlen($name));
          $cat = substr($name,0,$dot);
        }
        $details[0] = $cat;
        $details[1] = $culture;

        $catalogue[] = $details;
      }
    }
    sort($catalogue);

    return $catalogue;
  }
}
