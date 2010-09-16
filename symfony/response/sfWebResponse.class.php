<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebResponse class.
 *
 * This class manages web reponses. It supports cookies and headers management.
 * 
 * @package    symfony
 * @subpackage response
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebResponse.class.php 3250 2007-01-12 20:09:11Z fabien $
 */
class sfWebResponse extends sfResponse
{
  protected
    $cookies     = array(),
    $statusCode  = 200,
    $statusText  = 'OK',
    $statusTexts = array(),
    $headerOnly  = false;

  /**
   * Initializes this sfWebResponse.
   *
   * @param sfContext A sfContext instance
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Response
   */
  public function initialize($context, $parameters = array())
  {
    parent::initialize($context, $parameters);

    if ('HEAD' == $context->getRequest()->getMethodName())
    {
      $this->setHeaderOnly(true);
    }

    $this->statusTexts = array(
      '100' => 'Continue',
      '101' => 'Switching Protocols',
      '200' => 'OK',
      '201' => 'Created',
      '202' => 'Accepted',
      '203' => 'Non-Authoritative Information',
      '204' => 'No Content',
      '205' => 'Reset Content',
      '206' => 'Partial Content',
      '300' => 'Multiple Choices',
      '301' => 'Moved Permanently',
      '302' => 'Found',
      '303' => 'See Other',
      '304' => 'Not Modified',
      '305' => 'Use Proxy',
      '306' => '(Unused)',
      '307' => 'Temporary Redirect',
      '400' => 'Bad Request',
      '401' => 'Unauthorized',
      '402' => 'Payment Required',
      '403' => 'Forbidden',
      '404' => 'Not Found',
      '405' => 'Method Not Allowed',
      '406' => 'Not Acceptable',
      '407' => 'Proxy Authentication Required',
      '408' => 'Request Timeout',
      '409' => 'Conflict',
      '410' => 'Gone',
      '411' => 'Length Required',
      '412' => 'Precondition Failed',
      '413' => 'Request Entity Too Large',
      '414' => 'Request-URI Too Long',
      '415' => 'Unsupported Media Type',
      '416' => 'Requested Range Not Satisfiable',
      '417' => 'Expectation Failed',
      '500' => 'Internal Server Error',
      '501' => 'Not Implemented',
      '502' => 'Bad Gateway',
      '503' => 'Service Unavailable',
      '504' => 'Gateway Timeout',
      '505' => 'HTTP Version Not Supported',
    );
  }

  /**
   * Sets if the response consist of just HTTP headers.
   *
   * @param boolean
   */
  public function setHeaderOnly($value = true)
  {
    $this->headerOnly = (boolean) $value;
  }

  /**
   * Returns if the response must only consist of HTTP headers.
   *
   * @return boolean returns true if, false otherwise
   */
  public function isHeaderOnly()
  {
    return $this->headerOnly;
  }

  /**
   * Sets a cookie.
   *
   * @param string HTTP header name
   * @param string Value for the cookie
   * @param string Cookie expiration period
   * @param string Path
   * @param string Domain name
   * @param boolean If secure
   * @param boolean If uses only HTTP
   *
   * @throws <b>sfException</b> If fails to set the cookie
   */
  public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
  {
    if ($expire !== null)
    {
      if (is_numeric($expire))
      {
        $expire = (int) $expire;
      }
      else
      {
        $expire = strtotime($expire);
        if ($expire === false || $expire == -1)
        {
          throw new sfException('Your expire parameter is not valid.');
        }
      }
    }

    $this->cookies[] = array(
      'name'     => $name,
      'value'    => $value,
      'expire'   => $expire,
      'path'     => $path,
      'domain'   => $domain,
      'secure'   => $secure ? true : false,
      'httpOnly' => $httpOnly,
    );
  }

  /**
   * Sets response status code.
   *
   * @param string HTTP status code
   * @param string HTTP status text
   *
   */
  public function setStatusCode($code, $name = null)
  {
    $this->statusCode = $code;
    $this->statusText = null !== $name ? $name : $this->statusTexts[$code];
  }

  /**
   * Retrieves status code for the current web response.
   *
   * @return string Status code
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Sets a HTTP header.
   *
   * @param string HTTP header name
   * @param string Value
   * @param boolean Replace for the value
   *
   */
  public function setHttpHeader($name, $value, $replace = true)
  {
    $name = $this->normalizeHeaderName($name);

    if ('Content-Type' == $name)
    {
      if ($replace || !$this->getHttpHeader('Content-Type', null))
      {
        $this->setContentType($value);
      }

      return;
    }

    if (!$replace)
    {
      $current = $this->getParameter($name, '', 'symfony/response/http/headers');
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->setParameter($name, $value, 'symfony/response/http/headers');
  }

  /**
   * Gets HTTP header current value.
   *
   * @return array
   */
  public function getHttpHeader($name, $default = null)
  {
    return $this->getParameter($this->normalizeHeaderName($name), $default, 'symfony/response/http/headers');
  }

  /**
   * Has a HTTP header.
   *
   * @return boolean
   */
  public function hasHttpHeader($name)
  {
    return $this->hasParameter($this->normalizeHeaderName($name), 'symfony/response/http/headers');
  }

  /**
   * Sets response content type.
   *
   * @param string Content type
   *
   */
  public function setContentType($value)
  {
    // add charset if needed
    if (false === stripos($value, 'charset'))
    {
      $value .= '; charset='.sfConfig::get('sf_charset');
    }

    $this->setParameter('Content-Type', $value, 'symfony/response/http/headers');
  }

  /**
   * Gets response content type.
   *
   * @return array
   */
  public function getContentType()
  {
    return $this->getHttpHeader('Content-Type', 'text/html; charset='.sfConfig::get('sf_charset'));
  }

  /**
   * Send HTTP headers and cookies.
   *
   */
  public function sendHttpHeaders()
  {
    // status
    $status = 'HTTP/1.0 '.$this->statusCode.' '.$this->statusText;
    header($status);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfResponse} send status "'.$status.'"');
    }

    // headers
    foreach ($this->getParameterHolder()->getAll('symfony/response/http/headers') as $name => $value)
    {
      header($name.': '.$value);

      if (sfConfig::get('sf_logging_enabled') && $value != '')
      {
        $this->getContext()->getLogger()->info('{sfResponse} send header "'.$name.'": "'.$value.'"');
      }
    }

    // cookies
    foreach ($this->cookies as $cookie)
    {
      if (version_compare(phpversion(), '5.2', '>='))
      {
        setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);
      }
      else
      {
        setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure']);
      }

      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->getContext()->getLogger()->info('{sfResponse} send cookie "'.$cookie['name'].'": "'.$cookie['value'].'"');
      }
    }
  }

  /**
   * Send content for the current web response.
   *
   */
  public function sendContent()
  {
    if (!$this->headerOnly)
    {
      parent::sendContent();
    }
  }

  /**
   * Retrieves a normalized Header.
   *
   * @param string Header name
   *
   * @return string Normalized header
   */
  protected function normalizeHeaderName($name)
  {
    return preg_replace('/\-(.)/e', "'-'.strtoupper('\\1')", strtr(ucfirst(strtolower($name)), '_', '-'));
  }

  /**
   * Retrieves a formated date.
   *
   * @param string Timestamp
   * @param string Format type
   *
   * @return string Formated date
   */
  public function getDate($timestamp, $type = 'rfc1123')
  {
    $type = strtolower($type);

    if ($type == 'rfc1123')
    {
      return substr(gmdate('r', $timestamp), 0, -5).'GMT';
    }
    else if ($type == 'rfc1036')
    {
      return gmdate('l, d-M-y H:i:s ', $timestamp).'GMT';
    }
    else if ($type == 'asctime')
    {
      return gmdate('D M j H:i:s', $timestamp);
    }
    else
    {
      $error = 'The second getDate() method parameter must be one of: rfc1123, rfc1036 or asctime';

      throw new sfParameterException($error);
    }
  }

  /**
   * Adds vary to a http header.
   *
   * @param string HTTP header
   */
  public function addVaryHttpHeader($header)
  {
    $vary = $this->getHttpHeader('Vary');
    $currentHeaders = array();
    if ($vary)
    {
      $currentHeaders = preg_split('/\s*,\s*/', $vary);
    }
    $header = $this->normalizeHeaderName($header);

    if (!in_array($header, $currentHeaders))
    {
      $currentHeaders[] = $header;
      $this->setHttpHeader('Vary', implode(', ', $currentHeaders));
    }
  }

  /**
   * Adds an control cache http header.
   *
   * @param string HTTP header
   * @param string Value for the http header
   */
  public function addCacheControlHttpHeader($name, $value = null)
  {
    $cacheControl = $this->getHttpHeader('Cache-Control');
    $currentHeaders = array();
    if ($cacheControl)
    {
      foreach (preg_split('/\s*,\s*/', $cacheControl) as $tmp)
      {
        $tmp = explode('=', $tmp);
        $currentHeaders[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
      }
    }
    $currentHeaders[strtr(strtolower($name), '_', '-')] = $value;

    $headers = array();
    foreach ($currentHeaders as $key => $value)
    {
      $headers[] = $key.(null !== $value ? '='.$value : '');
    }

    $this->setHttpHeader('Cache-Control', implode(', ', $headers));
  }

  /**
   * Retrieves meta headers for the current web response.
   *
   * @return string Meta headers
   */
  public function getHttpMetas()
  {
    return $this->getParameterHolder()->getAll('helper/asset/auto/httpmeta');
  }

  /**
   * Adds meta headers to the current web response.
   *
   * @param string Key to replace
   * @param string Value for the replacement
   * @param boolean Replace or not
   */
  public function addHttpMeta($key, $value, $replace = true)
  {
    $key = $this->normalizeHeaderName($key);

    // set HTTP header
    $this->setHttpHeader($key, $value, $replace);

    if ('Content-Type' == $key)
    {
      $value = $this->getContentType();
    }

    if (!$replace)
    {
      $current = $this->getParameter($key, '', 'helper/asset/auto/httpmeta');
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->setParameter($key, $value, 'helper/asset/auto/httpmeta');
  }

  /**
   * Retrieves all meta headers for the current web response.
   *
   * @return array List of meta headers
   */
  public function getMetas()
  {
    return $this->getParameterHolder()->getAll('helper/asset/auto/meta');
  }

  /**
   * Adds a meta header to the current web response.
   *
   * @param string Name of the header
   * @param string Meta header to be set
   * @param boolean true if it's replaceable
   * @param boolean true for escaping the header
   */
  public function addMeta($key, $value, $replace = true, $escape = true)
  {
    $key = strtolower($key);

    if (sfConfig::get('sf_i18n'))
    {
      $value = $this->getContext()->getI18N()->__($value);
    }

    if ($escape)
    {
      $value = htmlentities($value, ENT_QUOTES, sfConfig::get('sf_charset'));
    }

    if ($replace || !$this->getParameter($key, null, 'helper/asset/auto/meta'))
    {
      $this->setParameter($key, $value, 'helper/asset/auto/meta');
    }
  }

  /**
   * Retrieves title for the current web response.
   *
   * @return string Title
   */
  public function getTitle()
  {
    return $this->getParameter('title', '', 'helper/asset/auto/meta');
  }

  /**
   * Sets title for the current web response.
   *
   * @param string Title name
   * @param boolean true, for escaping the title
   */
  public function setTitle($title, $escape = true)
  {
    $this->addMeta('title', $title, true, $escape);
  }

  /**
   * Retrieves stylesheets for the current web response.
   *
   * @param string Direcotry delimiter
   *
   * @return string Stylesheets
   */
  public function getStylesheets($position = '')
  {
    return $this->getParameterHolder()->getAll('helper/asset/auto/stylesheet'.($position ? '/'.$position : ''));
  }

  /**
   * Adds an stylesheet to the current web response.
   *
   * @param string Stylesheet
   * @param string Direcotry delimiter
   * @param string Stylesheet options
   */
  public function addStylesheet($css, $position = '', $options = array())
  {
    $this->setParameter($css, $options, 'helper/asset/auto/stylesheet'.($position ? '/'.$position : ''));
  }

  /**
   * Retrieves javascript code from the current web response.
   *
   * @param string Directory delimiter
   *
   * @return string Javascript code
   */
  public function getJavascripts($position = '')
  {
    return $this->getParameterHolder()->getAll('helper/asset/auto/javascript'.($position ? '/'.$position : ''));
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @param string Javascript code
   * @param string Directory delimiter
   */
  public function addJavascript($js, $position = '')
  {
    $this->setParameter($js, $js, 'helper/asset/auto/javascript'.($position ? '/'.$position : ''));
  }

  /**
   * Retrieves cookies from the current web response.
   *
   * @return array Cookies
   */
  public function getCookies()
  {
    $cookies = array();
    foreach ($this->cookies as $cookie)
    {
      $cookies[$cookie['name']] = $cookie;
    }

    return $cookies;
  }

  /**
   * Retrieves HTTP headers from the current web response.
   *
   * @return string HTTP headers
   */
  public function getHttpHeaders()
  {
    return $this->getParameterHolder()->getAll('symfony/response/http/headers');
  }

  /**
   * Cleans HTTP headers from the current web response.
   */
  public function clearHttpHeaders()
  {
    $this->getParameterHolder()->removeNamespace('symfony/response/http/headers');
  }

  /**
   * Copies a propertie to a new one.
   *
   * @param sfResponse Response instance
   */
  public function mergeProperties($response)
  {
    $this->parameterHolder = clone $response->getParameterHolder();
  }

  /**
   * Retrieves all objects handlers for the current web response.
   *
   * @return array Objects instance
   */
  public function __sleep()
  {
    return array('content', 'statusCode', 'statusText', 'parameterHolder');
  }

  /**
   * Reconstructs any result that web response instance needs.
   */
  public function __wakeup()
  {
  }

  /**
   * Executes the shutdown procedure.
   */
  public function shutdown()
  {
  }
}
