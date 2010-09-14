<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * AssetHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id: AssetHelper.php 3313 2007-01-20 07:00:37Z fabien $
 */

/**
 * Returns a <link> tag that browsers and news readers
 * can use to auto-detect a RSS or ATOM feed for the current page,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Options:</b>
 * - rel - defaults to 'alternate'
 * - type - defaults to 'application/rss+xml'
 * - title - defaults to the feed type in upper case
 *
 * <b>Examples:</b>
 * <code>
 *  echo auto_discovery_link_tag('rss', 'module/feed');
 *    => <link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.curenthost.com/module/feed" />
 *  echo auto_discovery_link_tag('rss', 'module/feed', array('title' => 'My RSS'));
 *    => <link rel="alternate" type="application/rss+xml" title="My RSS" href="http://www.curenthost.com/module/feed" />
 * </code>
 *
 * @param  string feed type ('rss', 'atom')
 * @param  string 'module/action' or '@rule' of the feed
 * @param  array additional HTML compliant <link> tag parameters
 * @return string XHTML compliant <link> tag
 */
function auto_discovery_link_tag($type = 'rss', $url_options = array(), $tag_options = array())
{
  return tag('link', array(
    'rel'   => isset($tag_options['rel']) ? $tag_options['rel'] : 'alternate',
    'type'  => isset($tag_options['type']) ? $tag_options['type'] : 'application/'.$type.'+xml',
    'title' => isset($tag_options['title']) ? $tag_options['title'] : ucfirst($type),
    'href'  => url_for($url_options, true)
  ));
}

/**
 * Returns the path to a JavaScript asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo javascript_path('myscript');
 *    => /js/myscript.js
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_js/myscript.css"
 * - file name, like "myscript.js", that gets expanded to "/js/myscript.js"
 * - file name without extension, like "myscript", that gets expanded to "/js/myscript.js"
 *
 * @param  string asset name
 * @param  bool return absolute path ?
 * @return string file path to the JavaScript file
 * @see    javascript_include_tag
 */
function javascript_path($source, $absolute = false)
{
  return _compute_public_path($source, 'js', 'js', $absolute);
}

/**
 * Returns a <script> include tag per source given as argument.
 *
 * <b>Examples:</b>
 * <code>
 *  echo javascript_include_tag('xmlhr');
 *    => <script language="JavaScript" type="text/javascript" src="/js/xmlhr.js"></script>
 *  echo javascript_include_tag('common.javascript', '/elsewhere/cools');
 *    => <script language="JavaScript" type="text/javascript" src="/js/common.javascript"></script>
 *       <script language="JavaScript" type="text/javascript" src="/elsewhere/cools.js"></script>
 * </code>
 *
 * @param  string asset names
 * @return string XHTML compliant <script> tag(s)
 * @see    javascript_path 
 */
function javascript_include_tag()
{
  $html = '';
  foreach (func_get_args() as $source)
  {
    $source = javascript_path($source);
    $html .= content_tag('script', '', array('type' => 'text/javascript', 'src' => $source))."\n";
  }

  return $html;
}

/**
 * Returns the path to a stylesheet asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo stylesheet_path('style');
 *    => /css/style.css
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_css/style.css"
 * - file name, like "style.css", that gets expanded to "/css/style.css"
 * - file name without extension, like "style", that gets expanded to "/css/style.css"
 *
 * @param  string asset name
 * @param  bool return absolute path ?
 * @return string file path to the stylesheet file
 * @see    stylesheet_tag  
 */
function stylesheet_path($source, $absolute = false)
{
  return _compute_public_path($source, 'css', 'css', $absolute);
}

/**
 * Returns a css <link> tag per source given as argument,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Options:</b>
 * - rel - defaults to 'stylesheet'
 * - type - defaults to 'text/css'
 * - media - defaults to 'screen'
 *
 * <b>Examples:</b>
 * <code>
 *  echo stylesheet_tag('style');
 *    => <link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('style', array('media' => 'all'));
 *    => <link href="/stylesheets/style.css" media="all" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('random.styles', '/css/stylish');
 *    => <link href="/stylesheets/random.styles" media="screen" rel="stylesheet" type="text/css" />
 *       <link href="/css/stylish.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * @param  string asset names
 * @param  array additional HTML compliant <link> tag parameters
 * @return string XHTML compliant <link> tag(s)
 * @see    stylesheet_path 
 */
function stylesheet_tag()
{
  $sources = func_get_args();
  $sourceOptions = (func_num_args() > 1 && is_array($sources[func_num_args() - 1])) ? array_pop($sources) : array();

  $html = '';
  foreach ($sources as $source)
  {
    $source  = stylesheet_path($source);
    $options = array_merge(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen', 'href' => $source), $sourceOptions);
    $html   .= tag('link', $options)."\n";
  }

  return $html;
}

/**
 * Adds a stylesheet to the response object.
 *
 * @see sfResponse->addStylesheet()
 */
function use_stylesheet($css, $position = '', $options = array())
{
  sfContext::getInstance()->getResponse()->addStylesheet($css, $position, $options);
}

/**
 * Adds a javascript to the response object.
 *
 * @see sfResponse->addJavascript()
 */
function use_javascript($js, $position = '')
{
  sfContext::getInstance()->getResponse()->addJavascript($js, $position);
}

/**
 * Decorates the current template with a given layout.
 *
 * @param mixed The layout name or path or false to disable the layout
 */
function decorate_with($layout)
{
  $view = sfContext::getInstance()->getActionStack()->getLastEntry()->getViewInstance();
  if (false === $layout)
  {
    $view->setDecorator(false);
  }
  else
  {
    $view->setDecoratorTemplate($layout);
  }
}

/**
 * Returns the path to an image asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo image_path('foobar');
 *    => /images/foobar.png
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_images/image.gif"
 * - file name, like "rss.gif", that gets expanded to "/images/rss.gif"
 * - file name without extension, like "logo", that gets expanded to "/images/logo.png"
 * 
 * @param  string asset name
 * @param  bool return absolute path ?
 * @return string file path to the image file
 * @see    image_tag  
 */
function image_path($source, $absolute = false)
{
  return _compute_public_path($source, 'images', 'png', $absolute);
}

/**
 * Returns an <img> image tag for the asset given as argument.
 *
 * <b>Options:</b>
 * - 'absolute' - to output absolute file paths, useful for embedded images in emails
 * - 'alt'  - defaults to the file name part of the asset (capitalized and without the extension)
 * - 'size' - Supplied as "XxY", so "30x45" becomes width="30" and height="45"
 *
 * <b>Examples:</b>
 * <code>
 *  echo image_tag('foobar');
 *    => <img src="images/foobar.png" alt="Foobar" />
 *  echo image_tag('/my_images/image.gif', array('alt' => 'Alternative text', 'size' => '100x200'));
 *    => <img src="/my_images/image.gif" alt="Alternative text" width="100" height="200" />
 * </code>
 *
 * @param  string image asset name
 * @param  array additional HTML compliant <img> tag parameters
 * @return string XHTML compliant <img> tag
 * @see    image_path 
 */
function image_tag($source, $options = array())
{
  if (!$source)
  {
    return '';
  }

  $options = _parse_attributes($options);

  $absolute = false;
  if (isset($options['absolute']))
  {
    unset($options['absolute']);
    $absolute = true;
  }

  $options['src'] = image_path($source, $absolute);

  if (!isset($options['alt']))
  {
    $path_pos = strrpos($source, '/');
    $dot_pos = strrpos($source, '.');
    $begin = $path_pos ? $path_pos + 1 : 0;
    $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
    $options['alt'] = ucfirst(substr($source, $begin, $nb_str));
  }

  if (isset($options['size']))
  {
    list($options['width'], $options['height']) = split('x', $options['size'], 2);
    unset($options['size']);
  }

  return tag('img', $options);
}

function _compute_public_path($source, $dir, $ext, $absolute = false)
{
  if (strpos($source, '://'))
  {
    return $source;
  }

  $request = sfContext::getInstance()->getRequest();
  $sf_relative_url_root = $request->getRelativeUrlRoot();
  if (strpos($source, '/') !== 0)
  {
    $source = $sf_relative_url_root.'/'.$dir.'/'.$source;
  }
  if (strpos(basename($source), '.') === false)
  {
    $source .= '.'.$ext;
  }
  if ($sf_relative_url_root && strpos($source, $sf_relative_url_root) !== 0)
  {
    $source = $sf_relative_url_root.$source;
  }

  if ($absolute)
  {
    $source = 'http'.($request->isSecure() ? 's' : '').'://'.$request->getHost().$source;
  }

  return $source;
}

/**
 * Prints a set of <meta> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_metas();
 *    => <meta name="title" content="symfony - open-source PHP5 web framework" />
 *       <meta name="robots" content="index, follow" />
 *       <meta name="description" content="symfony - open-source PHP5 web framework" />
 *       <meta name="keywords" content="symfony, project, framework, php, php5, open-source, mit, symphony" />
 *       <meta name="language" content="en" /><link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * <b>Note:</b> Modify the sfResponse object or the view.yml to change, add or remove metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_http_metas 
 */
function include_metas()
{
  foreach (sfContext::getInstance()->getResponse()->getMetas() as $name => $content)
  {
    echo tag('meta', array('name' => $name, 'content' => $content))."\n";
  }
}

/**
 * Returns a set of <meta http-equiv> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_http_metas();
 *    => <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 * </code>
 *
 * <b>Note:</b> Modify the sfResponse object or the view.yml to change, add or remove metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_metas 
 */
function include_http_metas()
{
  foreach (sfContext::getInstance()->getResponse()->getHttpMetas() as $httpequiv => $value)
  {
    echo tag('meta', array('http-equiv' => $httpequiv, 'content' => $value))."\n";
  }
}

/**
 * Returns the title of the current page according to the response attributes,
 * to be included in the <title> section of a HTML document.
 *
 * <b>Note:</b> Modify the sfResponse object or the view.yml to modify the title of a page.
 *
 * @return string page title
 */
function include_title()
{
  $title = sfContext::getInstance()->getResponse()->getTitle();

  echo content_tag('title', $title)."\n";
}

/**
 * Returns <script> tags for all javascripts configured in view.yml or added to the response object.
 *
 * You can use this helper to decide the location of javascripts in pages.
 * By default, if you don't call this helper, symfony will automatically include javascripts before </head>.
 * Calling this helper disables this behavior.
 *
 * @return string <script> tags
 */
function get_javascripts()
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('javascripts_included', true, 'symfony/view/asset');

  $already_seen = array();
  $html = '';

  foreach (array('first', '', 'last') as $position)
  {
    foreach ($response->getJavascripts($position) as $files)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      foreach ($files as $file)
      {
        $file = javascript_path($file);

        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;
        $html .= javascript_include_tag($file);
      }
    }
  }

  return $html;
}

/**
 * Prints <script> tags for all javascripts configured in view.yml or added to the response object.
 *
 * @see get_javascripts()
 */
function include_javascripts()
{
  echo get_javascripts();
}

/**
 * Returns <link> tags for all stylesheets configured in view.yml or added to the response object.
 *
 * You can use this helper to decide the location of stylesheets in pages.
 * By default, if you don't call this helper, symfony will automatically include stylesheets before </head>.
 * Calling this helper disables this behavior.
 *
 * @return string <link> tags
 */
function get_stylesheets()
{
  $response = sfContext::getInstance()->getResponse();
  $response->setParameter('stylesheets_included', true, 'symfony/view/asset');

  $already_seen = array();
  $html = '';

  foreach (array('first', '', 'last') as $position)
  {
    foreach ($response->getStylesheets($position) as $files => $options)
    {
      if (!is_array($files))
      {
        $files = array($files);
      }

      foreach ($files as $file)
      {
        $file = stylesheet_path($file);

        if (isset($already_seen[$file])) continue;

        $already_seen[$file] = 1;
        $html .= stylesheet_tag($file, $options);
      }
    }
  }

  return $html;
}

/**
 * Prints <link> tags for all stylesheets configured in view.yml or added to the response object.
 *
 * @see get_stylesheets()
 */
function include_stylesheets()
{
  echo get_stylesheets();
}
