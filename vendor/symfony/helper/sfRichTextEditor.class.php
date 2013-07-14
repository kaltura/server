<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRichTextEditor is an abstract class for rich text editor classes.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRichTextEditor.class.php 3284 2007-01-15 19:05:48Z fabien $
 */
abstract class sfRichTextEditor
{
  protected
    $name = '',
    $content = '',
    $options = array();

  /**
   * Initializes this rich text editor.
   *
   * @param string The tag name
   * @param string The rich text editor content
   * @param array  An array of options
   */
  public function initialize($name, $content, $options = array())
  {
    $this->name = $name;
    $this->content = $content;
    $this->options = $options;
  }

  /**
   * Returns the rich text editor as HTML.
   *
   * @return string Rich text editor HTML representation
   */
  abstract public function toHTML();
}
