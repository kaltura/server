<?php

/**
 * sfMessageSource_MySQL class file.
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
 * @version    $Id: sfMessageSource_MySQL.class.php 3148 2007-01-04 19:34:28Z fabien $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * Get the I18N utility file, contains the DSN parser.
 */
require_once(dirname(__FILE__).'/util.php');

/**
 * sfMessageSource_MySQL class.
 * 
 * Retrieve the message translation from a MySQL database.
 *
 * See the MessageSource::factory() method to instantiate this class.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 16:58:58 EST 2004
 * @package System.I18N.core
 */
class sfMessageSource_MySQL extends sfMessageSource
{
  /**
   * The datasource string, full DSN to the database.
   * @var string 
   */
  protected $source;

  /**
   * The DSN array property, parsed by PEAR's DB DSN parser.
   * @var array 
   */
  protected $dns;

  /**
   * A resource link to the database
   * @var db 
   */
  protected $db;

  /**
   * Constructor.
   * Create a new message source using MySQL.
   *
   * @param string MySQL datasource, in PEAR's DB DSN format.
   * @see MessageSource::factory();
   */
  function __construct($source)
  {
    $this->source = (string)$source;
    $this->dns = parseDSN($this->source);
    $this->db = $this->connect();
  }

  /**
   * Destructor, close the database connection.
   */
  function __destruct()
  {
    @mysql_close($this->db);
  }

  /**
   * Connect to the MySQL datasource
   *
   * @return resource MySQL connection.
   * @throws sfException, connection and database errors.
   */
  protected function connect()
  {
    $dsninfo = $this->dns;

    if (isset($dsninfo['protocol']) && $dsninfo['protocol'] == 'unix')
    {
      $dbhost = ':'.$dsninfo['socket'];
    }
    else
    {
      $dbhost = $dsninfo['hostspec'] ? $dsninfo['hostspec'] : 'localhost';
      if (!empty($dsninfo['port']))
      {
        $dbhost = ':' . $dsninfo['socket'];
      }
    }
    $user = $dsninfo['username'];
    $pw = $dsninfo['password'];

    $connect_function = 'mysql_connect';

    if ($dbhost && $user && $pw)
    {
      $conn = @$connect_function($dbhost, $user, $pw);
    }
    elseif ($dbhost && $user)
    {
      $conn = @$connect_function($dbhost, $user);
    }
    elseif ($dbhost)
    {
      $conn = @$connect_function($dbhost);
    }
    else
    {
      $conn = false;
    }

    if (empty($conn))
    {
      throw new sfException('Error in connecting to '.$dsninfo);
    }

    if ($dsninfo['database'])
    {
      if (!@mysql_select_db($dsninfo['database'], $conn))
      {
        throw new sfException('Error in connecting database, dns:'.$dsninfo);
      }
    }
    else
    {
      throw new sfException('Please provide a database for message translation.');
    }

    return $conn;
  }

  /**
   * Get the database connection.
   *
   * @return db database connection. 
   */
  public function connection()
  {
    return $this->db;
  }

  /**
   * Get an array of messages for a particular catalogue and cultural 
   * variant.
   *
   * @param string the catalogue name + variant
   * @return array translation messages.
   */
  protected function &loadData($variant)
  {
    $variant = mysql_escape_string($variant);

    $statement = 
      "SELECT t.id, t.source, t.target, t.comments
        FROM trans_unit t, catalogue c
        WHERE c.cat_id =  t.cat_id
          AND c.name = '{$variant}' 
        ORDER BY id ASC";

    $rs = mysql_query($statement,$this->db);

    $result = array();

    while ($row = mysql_fetch_array($rs, MYSQL_NUM))
    {
      $source = $row[1];
      $result[$source][] = $row[2]; //target
      $result[$source][] = $row[0]; //id
      $result[$source][] = $row[3]; //comments
    }

    return $result;
  }

  /**
   * Get the last modified unix-time for this particular catalogue+variant.
   * We need to query the database to get the date_modified.
   *
   * @param string catalogue+variant
   * @return int last modified in unix-time format.
   */
  protected function getLastModified($source)
  {
    $source = mysql_escape_string($source);

    $rs = mysql_query("SELECT date_modified FROM catalogue WHERE name = '{$source}'", $this->db);

    $result = $rs ? intval(mysql_result($rs, 0)) : 0;

    return $result;
  }

  /**
   * Check if a particular catalogue+variant exists in the database.
   *
   * @param string catalogue+variant
   * @return boolean true if the catalogue+variant is in the database, false otherwise.
   */ 
  protected function isValidSource($variant)
  {
    $variant = mysql_escape_string ($variant);

    $rs = mysql_query("SELECT COUNT(*) FROM catalogue WHERE name = '{$variant}'", $this->db);

    $row = mysql_fetch_array($rs,MYSQL_NUM);

    $result = $row && $row[0] == '1';

    return $result;
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

    $catalogues = array($catalogue);

    $variant = null;

    for ($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $catalogue.'.'.$variant;
      }
    }

    return array_reverse($catalogues);
  }

  /**
   * Retrieve catalogue details, array($cat_id, $variant, $count).
   *
   * @param string catalogue
   * @return array catalogue details, array($cat_id, $variant, $count). 
   */
  protected function getCatalogueDetails($catalogue = 'messages')
  {
    if (empty($catalogue))
    {
      $catalogue = 'messages';
    }

    $variant = $catalogue.'.'.$this->culture;

    $name = mysql_escape_string($this->getSource($variant));

    $rs = mysql_query("SELECT cat_id FROM catalogue WHERE name = '{$name}'", $this->db);

    if (mysql_num_rows($rs) != 1)
    {
      return false;
    }

    $cat_id = intval(mysql_result($rs, 0));

    // first get the catalogue ID
    $rs = mysql_query("SELECT count(msg_id) FROM trans_unit WHERE cat_id = {$cat_id}", $this->db);

    $count = intval(mysql_result($rs, 0));

    return array($cat_id, $variant, $count);
  }

  /**
   * Update the catalogue last modified time.
   *
   * @return boolean true if updated, false otherwise. 
   */
  protected function updateCatalogueTime($cat_id, $variant)
  {
    $time = time();

    $result = mysql_query("UPDATE catalogue SET date_modified = {$time} WHERE cat_id = {$cat_id}", $this->db);

    if (!empty($this->cache))
    {
      $this->cache->clean($variant, $this->culture);
    }

    return $result;
  }

  /**
   * Save the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.
   */
  function save($catalogue = 'messages')
  {
    $messages = $this->untranslated;

    if (count($messages) <= 0)
    {
      return false;
    }

    $details = $this->getCatalogueDetails($catalogue);

    if ($details)
    {
      list($cat_id, $variant, $count) = $details;
    }
    else
    {
      return false;
    }

    if ($cat_id <= 0)
    {
      return false;
    }
    $inserted = 0;

    $time = time();

    foreach ($messages as $message)
    {
      $count++;
      $inserted++;
      $message = mysql_escape_string($message);
      $statement = "INSERT INTO trans_unit
        (cat_id,id,source,date_added) VALUES
        ({$cat_id}, {$count},'{$message}',$time)";
      mysql_query($statement, $this->db);
    }
    if ($inserted > 0)
    {
      $this->updateCatalogueTime($cat_id, $variant);
    }

    return $inserted > 0;
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
    $details = $this->getCatalogueDetails($catalogue);
    if ($details)
    {
      list($cat_id, $variant, $count) = $details;
    }
    else
    {
      return false;
    }

    $text = mysql_escape_string($message);

    $statement = "DELETE FROM trans_unit WHERE cat_id = {$cat_id} AND source = '{$message}'";
    $deleted = false;

    mysql_query($statement, $this->db);

    if (mysql_affected_rows($this->db) == 1)
    {
      $deleted = $this->updateCatalogueTime($cat_id, $variant);
    }

    return $deleted;
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
    $details = $this->getCatalogueDetails($catalogue);
    if ($details)
    {
      list($cat_id, $variant, $count) = $details;
    }
    else
    {
      return false;
    }

    $comments = mysql_escape_string($comments);
    $target = mysql_escape_string($target);
    $text = mysql_escape_string($text);

    $time = time();

    $statement = "UPDATE trans_unit SET target = '{$target}', comments = '{$comments}', date_modified = '{$time}' WHERE cat_id = {$cat_id} AND source = '{$text}'";

    $updated = false;

    mysql_query($statement, $this->db);
    if (mysql_affected_rows($this->db) == 1)
    {
      $updated = $this->updateCatalogueTime($cat_id, $variant);
    }

    return $updated;
  }

  /**
   * Returns a list of catalogue as key and all it variants as value.
   *
   * @return array list of catalogues 
   */
  function catalogues()
  {
    $statement = 'SELECT name FROM catalogue ORDER BY name';
    $rs = mysql_query($statement, $this->db);
    $result = array();
    while($row = mysql_fetch_array($rs, MYSQL_NUM))
    {
      $details = explode('.', $row[0]);
      if (!isset($details[1]))
      {
        $details[1] = null;
      }

      $result[] = $details;
    }

    return $result;
  }
}
