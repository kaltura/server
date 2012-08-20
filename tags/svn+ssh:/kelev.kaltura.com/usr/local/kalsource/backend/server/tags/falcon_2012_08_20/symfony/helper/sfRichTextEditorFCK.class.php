<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRichTextEditorFCK implements the FCK rich text editor.
 *
 * <b>Options:</b>
 *  - tool   - Sets the FCKEditor toolbar style
 *  - config - Sets custom path to the FCKEditor configuration file
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRichTextEditorFCK.class.php 3284 2007-01-15 19:05:48Z fabien $
 */
class sfRichTextEditorFCK extends sfRichTextEditor
{
  /**
   * Returns the rich text editor as HTML.
   *
   * @return string Rich text editor HTML representation
   */
  public function toHTML()
  {
    $options = $this->options;

    // we need to know the id for things the rich text editor
    // in advance of building the tag
    $id = _get_option($options, 'id', $this->name);

    $php_file = sfConfig::get('sf_rich_text_fck_js_dir').DIRECTORY_SEPARATOR.'fckeditor.php';

    if (!is_readable(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$php_file))
    {
      throw new sfConfigurationException('You must install FCKEditor to use this helper (see rich_text_fck_js_dir settings).');
    }

    // FCKEditor.php class is written with backward compatibility of PHP4.
    // This reportings are to turn off errors with public properties and already declared constructor
    $error_reporting = ini_get('error_reporting');
    error_reporting(E_ALL);

    require_once(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$php_file);

    // turn error reporting back to your settings
    error_reporting($error_reporting);

    $fckeditor           = new FCKeditor($this->name);
    $fckeditor->BasePath = sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/'.sfConfig::get('sf_rich_text_fck_js_dir').'/';
    $fckeditor->Value    = $this->content;

    if (isset($options['width']))
    {
      $fckeditor->Width = $options['width'];
    }
    elseif (isset($options['cols']))
    {
      $fckeditor->Width = (string)((int) $options['cols'] * 10).'px';
    }

    if (isset($options['height']))
    {
      $fckeditor->Height = $options['height'];
    }
    elseif (isset($options['rows']))
    {
      $fckeditor->Height = (string)((int) $options['rows'] * 10).'px';
    }

    if (isset($options['tool']))
    {
      $fckeditor->ToolbarSet = $options['tool'];
    }

    if (isset($options['config']))
    {
      $fckeditor->Config['CustomConfigurationsPath'] = javascript_path($options['config']);
    }

    $content = $fckeditor->CreateHtml();

    return $content;
  }
}
