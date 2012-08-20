<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * UrlHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: UrlHelper.php 3380 2007-02-01 06:57:47Z fabien $
 */


/**
 * Returns a routed URL based on the module/action passed as argument
 * and the routing configuration.
 *
 * <b>Examples:</b>
 * <code>
 *  echo url_for('my_module/my_action');
 *    => /path/to/my/action
 *  echo url_for('@my_rule');
 *    => /path/to/my/action 
 *  echo url_for('@my_rule', true);
 *    => http://myapp.example.com/path/to/my/action
 * </code>
 *
 * @param  string 'module/action' or '@rule' of the action
 * @param  bool return absolute path?
 * @return string routed URL
 */
function url_for($internal_uri, $absolute = false)
{
  static $controller;

  if (!isset($controller))
  {
    $controller = sfContext::getInstance()->getController();
  }

  return $controller->genUrl($internal_uri, $absolute);
}

/**
 * Creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * It's also possible to pass a string instead of a module/action pair to
 * get a link tag that just points without consideration. 
 * If null is passed as a name, the link itself will become the name.
 * If an object is passed as a name, the object string representation is used.
 * One of the options serves for for creating javascript confirm alerts where 
 * if you pass 'confirm' => 'Are you sure?', the link will be guarded 
 * with a JS popup asking that question. If the user accepts, the link is processed,
 * otherwise not.
 *
 * <b>Options:</b>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Note:</b> The 'popup' and 'post' options are not compatible with each other.
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to('Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 *  echo link_to('Visit Hoogle', 'http://www.hoogle.com');
 *    => <a href="http://www.hoogle.com">Visit Hoogle</a>
 *  echo link_to('Delete this page', 'my_module/my_action', array('id' => 'myid', 'confirm' => 'Are you sure?', 'absolute' => true));
 *    => <a href="http://myapp.example.com/path/to/my/action" id="myid" onclick="return confirm('Are you sure?');">Delete this page</a>
 * </code>
 *
 * @param  string name of the link, i.e. string to appear between the <a> tags
 * @param  string 'module/action' or '@rule' of the action
 * @param  array additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag
 * @see    url_for
 */
function link_to($name = '', $internal_uri = '', $options = array())
{
  $html_options = _parse_attributes($options);

  $html_options = _convert_options_to_javascript($html_options);

  $absolute = false;
  if (isset($html_options['absolute_url']))
  {
    $html_options['absolute'] = $html_options['absolute_url'];
    unset($html_options['absolute_url']);
  }
  if (isset($html_options['absolute']))
  {
    $absolute = (boolean) $html_options['absolute'];
    unset($html_options['absolute']);
  }

  $html_options['href'] = url_for($internal_uri, $absolute);

  if (isset($html_options['query_string']))
  {
    $html_options['href'] .= '?'.$html_options['query_string'];
    unset($html_options['query_string']);
  }

  if (is_object($name))
  {
    if (method_exists($name, '__toString'))
    {
      $name = $name->__toString();
    }
    else
    {
      throw new sfException(sprintf('Object of class "%s" cannot be converted to string (Please create a __toString() method)', get_class($name)));
    }
  }

  if (!strlen($name))
  {
    $name = $html_options['href'];
  }

  return content_tag('a', $name, $html_options);
}

/**
 * If the condition passed as first argument is true,
 * creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is false, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is false, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to_if($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 *  echo link_to_if(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action'); 
 *    => <span>Delete this page</span>
 * </code>
 *
 * @param  bool condition
 * @param  string name of the link, i.e. string to appear between the <a> tags
 * @param  string 'module/action' or '@rule' of the action
 * @param  array additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag or name
 * @see    link_to
 */
function link_to_if($condition, $name = '', $internal_uri = '', $options = array())
{
  if ($condition)
  {
    return link_to($name, $internal_uri, $options);
  }
  else
  {
    $html_options = _parse_attributes($options);
    $tag = _get_option($html_options, 'tag', 'span');

    return content_tag($tag, $name, $html_options);
  }
}

/**
 * If the condition passed as first argument is false,
 * creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is true, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is true, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to_unless($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <span>Delete this page</span>
 *  echo link_to_unless(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action'); 
 *    => <a href="/path/to/my/action">Delete this page</a>
 * </code>
 *
 * @param  bool condition
 * @param  string name of the link, i.e. string to appear between the <a> tags
 * @param  string 'module/action' or '@rule' of the action
 * @param  array additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag or name
 * @see    link_to
 */
function link_to_unless($condition, $name = '', $url = '', $options = array())
{
  return link_to_if(!$condition, $name, $url, $options);
}

/**
 * Creates an <input> button tag of the given name pointing to a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * The syntax is similar to the one of link_to.
 *
 * <b>Options:</b>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the button is clicked
 * - 'popup' - if set to true, the button opens a new browser window 
 * - 'post' - if set to true, the button submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo button_to('Delete this page', 'my_module/my_action');
 *    => <input value="Delete this page" type="button" onclick="document.location.href='/path/to/my/action';" />
 * </code>
 *
 * @param  string name of the button
 * @param  string 'module/action' or '@rule' of the action
 * @param  array additional HTML compliant <input> tag parameters
 * @return string XHTML compliant <input> tag
 * @see    url_for, link_to
 */
function button_to($name, $internal_uri, $options = array())
{
  $html_options = _convert_options($options);
  $html_options['value'] = $name;

  if (isset($html_options['post']) && $html_options['post'])
  {
    if (isset($html_options['popup']))
    {
      throw new sfConfigurationException('You can\'t use "popup" and "post" together');
    }
    $html_options['type'] = 'submit';
    unset($html_options['post']);
    $html_options = _convert_options_to_javascript($html_options);

    return form_tag($internal_uri, array('method' => 'post', 'class' => 'button_to')).tag('input', $html_options).'</form>';
  }
  else if (isset($html_options['popup']))
  {
    $html_options['type'] = 'button';
    $html_options = _convert_options_to_javascript($html_options, $internal_uri);

    return tag('input', $html_options);
  }
  else
  {
    $html_options['type']    = 'button';
    $html_options['onclick'] = "document.location.href='".url_for($internal_uri)."';";
    $html_options = _convert_options_to_javascript($html_options);

    return tag('input', $html_options);
  }
}

/**
 * Creates a <a> link tag to the given email (with href="mailto:...").
 * If null is passed as a name, the email itself will become the name.
 *
 * <b>Options:</b>
 * - 'encode' - if set to true, the email address appears with various random encoding for each letter.
 * The mail link still works when encoded, but the address doesn't appear in clear
 * in the source. Use it to prevent spam (efficiency not guaranteed).
 *
 * <b>Examples:</b>
 * <code>
 *  echo mail_to('webmaster@example.com');
 *    => <a href="mailto:webmaster@example.com">webmaster@example.com</a>
 *  echo mail_to('webmaster@example.com', 'send us an email');
 *    => <a href="mailto:webmaster@example.com">send us an email</a>
 *  echo mail_to('webmaster@example.com', 'send us an email', array('encode' => true));
 *    => <a href="&#x6d;a&#x69;&#x6c;&#x74;&#111;&#58;&#x77;&#x65;b&#x6d;as&#116;&#x65;&#114;&#64;&#101;&#x78;&#x61;&#x6d;&#x70;&#108;&#x65;&#46;&#99;&#x6f;&#109;">send us an email</a>
 * </code>
 *
 * @param  string target email
 * @param  string name of the link, i.e. string to appear between the <a> tags
 * @param  array additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag
 * @see    link_to
 */
function mail_to($email, $name = '', $options = array(), $default_value = array())
{
  $html_options = _parse_attributes($options);

  $html_options = _convert_options_to_javascript($html_options);

  $default_tmp = _parse_attributes($default_value);
  $default = array();
  foreach ($default_tmp as $key => $value)
  {
    $default[] = urlencode($key).'='.urlencode($value);
  }
  $options = count($default) ? '?'.implode('&', $default) : '';

  if (isset($html_options['encode']) && $html_options['encode'])
  {
    unset($html_options['encode']);
    $html_options['href'] = _encodeText('mailto:'.$email.$options);
    if (!$name)
    {
      $name = _encodeText($email);
    }
  }
  else
  {
    $html_options['href'] = 'mailto:'.$email.$options;
    if (!$name)
    {
      $name = $email;
    }
  }

  return content_tag('a', $name, $html_options);
}

function _convert_options_to_javascript($html_options, $internal_uri = '')
{
  // confirm
  $confirm = isset($html_options['confirm']) ? $html_options['confirm'] : '';
  unset($html_options['confirm']);

  // popup
  $popup = isset($html_options['popup']) ? $html_options['popup'] : '';
  unset($html_options['popup']);

  // post
  $post = isset($html_options['post']) ? $html_options['post'] : '';
  unset($html_options['post']);

  $onclick = isset($html_options['onclick']) ? $html_options['onclick'] : '';

  if ($popup && $post)
  {
    throw new sfConfigurationException('You can\'t use "popup" and "post" in the same link');
  }
  else if ($confirm && $popup)
  {
    $html_options['onclick'] = $onclick.'if ('._confirm_javascript_function($confirm).') { '._popup_javascript_function($popup, $internal_uri).' };return false;';
  }
  else if ($confirm && $post)
  {
    $html_options['onclick'] = $onclick.'if ('._confirm_javascript_function($confirm).') { '._post_javascript_function().' };return false;';
  }
  else if ($confirm)
  {
    if ($onclick)
    {
      $html_options['onclick'] = 'if ('._confirm_javascript_function($confirm).') {'.$onclick.'}';
    }
    else
    {
      $html_options['onclick'] = 'return '._confirm_javascript_function($confirm).';';
    }
  }
  else if ($post)
  {
    $html_options['onclick'] = $onclick._post_javascript_function().'return false;';
  }
  else if ($popup)
  {
    $html_options['onclick'] = $onclick._popup_javascript_function($popup, $internal_uri).'return false;';
  }

  return $html_options;
}

function _confirm_javascript_function($confirm)
{
  return "confirm('".escape_javascript($confirm)."')";
}

function _popup_javascript_function($popup, $internal_uri = '')
{
  $url = $internal_uri == '' ? 'this.href' : "'".url_for($internal_uri)."'";

  if (is_array($popup))
  {
    if (isset($popup[1]))
    {
      return "var w=window.open(".$url.",'".$popup[0]."','".$popup[1]."');w.focus();";
    }
    else
    {
      return "var w=window.open(".$url.",'".$popup[0]."');w.focus();";
    }
  }
  else
  {
    return "var w=window.open(".$url.");w.focus();";
  }
}

function _post_javascript_function()
{
  return "f = document.createElement('form'); document.body.appendChild(f); f.method = 'POST'; f.action = this.href; f.submit();";
}

function _encodeText($text)
{
  $encoded_text = '';

  for ($i = 0; $i < strlen($text); $i++)
  {
    $char = $text{$i};
    $r = rand(0, 100);

    # roughly 10% raw, 45% hex, 45% dec
    # '@' *must* be encoded. I insist.
    if ($r > 90 && $char != '@')
    {
      $encoded_text .= $char;
    }
    else if ($r < 45)
    {
      $encoded_text .= '&#x'.dechex(ord($char)).';';
    }
    else
    {
      $encoded_text .= '&#'.ord($char).';';
    }
  }

  return $encoded_text;
}
