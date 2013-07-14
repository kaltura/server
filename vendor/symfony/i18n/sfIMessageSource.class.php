<?php

/**
 * sfIMessageSource interface file.
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
 * @version    $Id: sfIMessageSource.class.php 2834 2006-11-27 14:09:05Z fabien $
 * @package    symfony
 * @subpackage i18n
 */
 
/**
 * sfIMessageSource interface.
 * 
 * All messages source used by MessageFormat must be of sfIMessageSource.
 * It defines a set of operations to add and retrieve messages from the
 * message source. In addition, message source can load a particular 
 * catalogue.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 17:40:19 EST 2004
 * @package System.I18N.core
 */
interface sfIMessageSource
{
  /**
   * Load the translation table for this particular catalogue.
   * The translation should be loaded in the following order.
   *  # [1] call getCatalogeList($catalogue) to get a list of 
   *    variants for for the specified $catalogue.
   *  # [2] for each of the variants, call getSource($variant)
   *    to get the resource, could be a file or catalogue ID.
   *  # [3] verify that this resource is valid by calling isValidSource($source)
   *  # [4] try to get the messages from the cache
   *  # [5] if a cache miss, call load($source) to load the message array
   *  # [6] store the messages to cache.
   *  # [7] continue with the foreach loop, e.g. goto [2].
   *
   * @param string a catalogue to load
   * @return boolean true if loaded, false otherwise.
   */
  function load($catalogue = 'messages');

  /**
   * Get the translation table. This includes all the loaded sections.
   * It must return a 2 level array of translation strings.
   * # "catalogue+variant" the catalogue and its variants.
   * # "source string" translation keys, and its translations.
   * <code>
   *   array('catalogue+variant' => 
   *       array('source string' => 'target string', ...)
   *             ...),
   *        ...);
   * </code>
   *
   * @return array 2 level array translation table.
   */
  function read();

  /**
   * Save the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.
   */
  function save($catalogue = 'messages');

  /**
   * Add a untranslated message to the source. Need to call save()
   * to save the messages to source.
   *
   * @param string message to add
   * @return void
   */
  function append($message);

  /**
   * Delete a particular message from the specified catalogue.
   *
   * @param string the source message to delete.
   * @param string the catalogue to delete from.
   * @return boolean true if deleted, false otherwise. 
   */
  function delete($message, $catalogue = 'messages');

  /**
   * Update the translation.
   *
   * @param string the source string.
   * @param string the new translation string.
   * @param string comments
   * @param string the catalogue of the translation.
   * @return boolean true if translation was updated, false otherwise. 
   */
  function update($text, $target, $comments, $catalogue = 'messages');

  /**
   * Returns a list of catalogue as key and all it variants as value.
   *
   * @return array list of catalogues 
   */
  function catalogues();

  /**
   * Set the culture for this particular message source.
   *
   * @param string the Culture name.
   */
  function setCulture($culture);

  /**
   * Get the culture identifier for the source.
   *
   * @return string culture identifier. 
   */
  function getCulture();
}
