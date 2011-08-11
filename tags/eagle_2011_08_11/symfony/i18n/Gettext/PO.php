<?php
/**
 * TGettext_PO class file.
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
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version $Revision: 1415 $  $Date: 2006-06-11 10:33:51 +0200 (Sun, 11 Jun 2006) $
 * @package System.I18N.core
 */

// +----------------------------------------------------------------------+
// | PEAR :: File :: Gettext :: PO                                        |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id: PO.php 1415 2006-06-11 08:33:51Z fabien $

/**
 * File::Gettext::PO
 * 
 * @author      Michael Wallner <mike@php.net>
 * @license     PHP License
 */
 
require_once dirname(__FILE__).'/TGettext.class.php';

/** 
 * File_Gettext_PO
 *
 * GNU PO file reader and writer.
 * 
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision: 1415 $
 * @access      public
 * @package System.I18N.core 
 */
class TGettext_PO extends TGettext
{
    /**
     * Constructor
     *
     * @access  public
     * @return  object      File_Gettext_PO
     * @param   string      path to GNU PO file
     */
    function TGettext_PO($file = '')
    {
        $this->file = $file;
    }

    /**
     * Load PO file
     *
     * @access  public
     * @return  mixed   Returns true on success or PEAR_Error on failure.
     * @param   string  $file
     */
    function load($file = null)
    {
        if (!isset($file)) {
            $file = $this->file;
        }
        
        // load file
        if (!$contents = @file($file)) {
            return false;
        }
        $contents = implode('', $contents);
        
        // match all msgid/msgstr entries
        $matched = preg_match_all(
            '/(msgid\s+("([^"]|\\\\")*?"\s*)+)\s+' .
            '(msgstr\s+("([^"]|\\\\")*?"\s*)+)/',
            $contents, $matches
        );
        unset($contents);
        
        if (!$matched) {
            return false;
        }
        
        // get all msgids and msgtrs
        for ($i = 0; $i < $matched; $i++) {
            $msgid = preg_replace(
                '/\s*msgid\s*"(.*)"\s*/s', '\\1', $matches[1][$i]);
            $msgstr= preg_replace(
                '/\s*msgstr\s*"(.*)"\s*/s', '\\1', $matches[4][$i]);
            $this->strings[parent::prepare($msgid)] = parent::prepare($msgstr);
        }
        
        // check for meta info
        if (isset($this->strings[''])) {
            $this->meta = parent::meta2array($this->strings['']);
            unset($this->strings['']);
        }
        
        return true;
    }
    
    /**
     * Save PO file
     *
     * @access  public
     * @return  mixed   Returns true on success or PEAR_Error on failure.
     * @param   string  $file
     */
    function save($file = null)
    {
        if (!isset($file)) {
            $file = $this->file;
        }

        // open PO file
        if (!is_resource($fh = @fopen($file, 'w'))) {
            return false;
        }

        // lock PO file exclusively
        if (!flock($fh, LOCK_EX)) {
            fclose($fh);
            return false;
        }
        // write meta info
        if (count($this->meta)) {
            $meta = 'msgid ""' . "\nmsgstr " . '""' . "\n";
            foreach ($this->meta as $k => $v) {
                $meta .= '"' . $k . ': ' . $v . '\n"' . "\n";
            }
            fwrite($fh, $meta . "\n");
        }
        // write strings
        foreach ($this->strings as $o => $t) {
            fwrite($fh,
                'msgid "'  . parent::prepare($o, true) . '"' . "\n" .
                'msgstr "' . parent::prepare($t, true) . '"' . "\n\n"
            );
        }
        
        //done
        @flock($fh, LOCK_UN);
        @fclose($fh);
        return true;
    }
}