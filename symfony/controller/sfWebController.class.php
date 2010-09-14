<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebController provides web specific methods to sfController such as, url redirection.
 *
 * @package    symfony
 * @subpackage controller
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfWebController.class.php 3204 2007-01-09 18:50:08Z fabien $
 */
abstract class sfWebController extends sfController
{
  /**
   * Generates an URL from an array of parameters.
   *
   * @param mixed   An associative array of URL parameters or an internal URI as a string.
   * @param boolean Whether to generate an absolute URL
   *
   * @return string A URL to a symfony resource
   */
  public function genUrl($parameters = array(), $absolute = false)
  {
    // absolute URL or symfony URL?
    if (!is_array($parameters) && preg_match('#^[a-z]+\://#', $parameters))
    {
      return $parameters;
    }

    if (!is_array($parameters) && $parameters == '#')
    {
      return $parameters;
    }

    $url = '';
    if (!($sf_no_script_name = sfConfig::get('sf_no_script_name')))
    {
      $url = $this->getContext()->getRequest()->getScriptName();
    }
    else if (($sf_relative_url_root = $this->getContext()->getRequest()->getRelativeUrlRoot()) && $sf_no_script_name)
    {
      $url = $sf_relative_url_root;
    }

    $route_name = '';
    $fragment = '';

    if (!is_array($parameters))
    {
      // strip fragment
      if (false !== ($pos = strpos($parameters, '#')))
      {
        $fragment = substr($parameters, $pos + 1);
        $parameters = substr($parameters, 0, $pos);
      }

      list($route_name, $parameters) = $this->convertUrlStringToParameters($parameters);
    }

    if (sfConfig::get('sf_url_format') == 'PATH')
    {
      // use PATH format
      $divider = '/';
      $equals  = '/';
      $querydiv = '/';
    }
    else
    {
      // use GET format
      $divider = ini_get('arg_separator.output');
      $equals  = '=';
      $querydiv = '?';
    }

    // default module
    if (!isset($parameters['module']))
    {
      $parameters['module'] = sfConfig::get('sf_default_module');
    }

    // default action
    if (!isset($parameters['action']))
    {
      $parameters['action'] = sfConfig::get('sf_default_action');
    }

    $r = sfRouting::getInstance();
    if ($r->hasRoutes() && $generated_url = $r->generate($route_name, $parameters, $querydiv, $divider, $equals))
    {
      $url .= $generated_url;
    }
    else
    {
      $query = http_build_query($parameters);

      if (sfConfig::get('sf_url_format') == 'PATH')
      {
        $query = strtr($query, ini_get('arg_separator.output').'=', '/');
      }

      $url .= $query;
    }

    if ($absolute)
    {
      $request = $this->getContext()->getRequest();
      $url = 'http'.($request->isSecure() ? 's' : '').'://'.$request->getHost().$url;
    }

    if ($fragment)
    {
      $url .= '#'.$fragment;
    }

    return $url;
  }

  /**
   * Converts an internal URI string to an array of parameters.
   *
   * @param string An internal URI
   *
   * @return array An array of parameters
   */
  public function convertUrlStringToParameters($url)
  {
    $params       = array();
    $query_string = '';
    $route_name   = '';

    // empty url?
    if (!$url)
    {
      $url = '/';
    }

    // we get the query string out of the url
    if ($pos = strpos($url, '?'))
    {
      $query_string = substr($url, $pos + 1);
      $url = substr($url, 0, $pos);
    }

    // 2 url forms
    // @route_name?key1=value1&key2=value2...
    // module/action?key1=value1&key2=value2...

    // first slash optional
    if ($url[0] == '/')
    {
      $url = substr($url, 1);
    }


    // route_name?
    if ($url[0] == '@')
    {
      $route_name = substr($url, 1);
    }
    else
    {
      $tmp = explode('/', $url);

      $params['module'] = $tmp[0];
      $params['action'] = isset($tmp[1]) ? $tmp[1] : sfConfig::get('sf_default_action');
    }

    // split the query string
    if ($query_string)
    {
      $matched = preg_match_all('/
        ([^&=]+)            # key
        =                   # =
        (.*?)               # value
        (?:
          (?=&[^&=]+=) | $   # followed by another key= or the end of the string
        )
      /x', $query_string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach ($matches as $match)
      {
        $params[$match[1][0]] = $match[2][0];
      }

      // check that all string is matched
      if (!$matched)
      {
        throw new sfParseException(sprintf('Unable to parse query string "%s".', $query_string));
      }
    }

    return array($route_name, $params);
  }

  /**
   * Redirects the request to another URL.
   *
   * @param string An existing URL
   * @param int    A delay in seconds before redirecting. This is only needed on
   *               browsers that do not support HTTP headers
   * @param int    The status code
   */
  public function redirect($url, $delay = 0, $statusCode = 302)
  {
    $response = $this->getContext()->getResponse();

    // redirect
    $response->clearHttpHeaders();
    $response->setStatusCode($statusCode);
    $response->setHttpHeader('Location', $url);
    $response->setContent(sprintf('<html><head><meta http-equiv="refresh" content="%d;url=%s"/></head></html>', $delay, htmlentities($url, ENT_QUOTES, sfConfig::get('sf_charset'))));

    if (!sfConfig::get('sf_test'))
    {
      $response->sendHttpHeaders();
    }
    $response->sendContent();
  }
}
