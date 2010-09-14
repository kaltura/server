<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfBrowser simulates a fake browser which can surf a symfony application.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfBrowser.class.php 3334 2007-01-23 15:46:08Z fabien $
 */
class sfBrowser
{
  protected
    $context            = null,
    $hostname           = null,
    $remote             = null,
    $dom                = null,
    $stack              = array(),
    $stackPosition      = -1,
    $cookieJar          = array(),
    $fields             = array(),
    $vars               = array(),
    $defaultServerArray = array(),
    $currentException   = null;

  public function initialize($hostname = null, $remote = null, $options = array())
  {
    unset($_SERVER['argv']);
    unset($_SERVER['argc']);

    // setup our fake environment
    $this->hostname = $hostname;
    $this->remote   = $remote;

    sfConfig::set('sf_path_info_array', 'SERVER');
    sfConfig::set('sf_test', true);

    // we set a session id (fake cookie / persistence)
    $this->newSession();

    // store default global $_SERVER array
    $this->defaultServerArray = $_SERVER;

    // register our shutdown function
    register_shutdown_function(array($this, 'shutdown'));
  }

  public function setVar($name, $value)
  {
    $this->vars[$name] = $value;

    return $this;
  }

  public function setAuth($login, $password)
  {
    $this->vars['PHP_AUTH_USER'] = $login;
    $this->vars['PHP_AUTH_PW']   = $password;

    return $this;
  }

  public function get($uri, $parameters = array())
  {
    return $this->call($uri, 'get', $parameters);
  }

  public function post($uri, $parameters = array())
  {
    return $this->call($uri, 'post', $parameters);
  }

  public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
  {
    $uri = $this->fixUri($uri);

    // add uri to the stack
    if ($changeStack)
    {
      $this->stack = array_slice($this->stack, 0, $this->stackPosition + 1);
      $this->stack[] = array(
        'uri'        => $uri,
        'method'     => $method,
        'parameters' => $parameters,
      );
      $this->stackPosition = count($this->stack) - 1;
    }

    list($path, $query_string) = false !== ($pos = strpos($uri, '?')) ? array(substr($uri, 0, $pos), substr($uri, $pos + 1)) : array($uri, '');
    $query_string = html_entity_decode($query_string);

    // remove anchor
    $path = preg_replace('/#.*/', '', $path);

    // removes all fields from previous request
    $this->fields = array();

    // prepare the request object
    $_SERVER = $this->defaultServerArray;
    $_SERVER['HTTP_HOST']       = $this->hostname ? $this->hostname : sfConfig::get('sf_app').'-'.sfConfig::get('sf_environment');
    $_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
    $_SERVER['SERVER_PORT']     = 80;
    $_SERVER['HTTP_USER_AGENT'] = 'PHP5/CLI';
    $_SERVER['REMOTE_ADDR']     = $this->remote ? $this->remote : '127.0.0.1';
    $_SERVER['REQUEST_METHOD']  = strtoupper($method);
    $_SERVER['PATH_INFO']       = $path;
    $_SERVER['REQUEST_URI']     = '/index.php'.$uri;
    $_SERVER['SCRIPT_NAME']     = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = '/index.php';
    $_SERVER['QUERY_STRING']    = $query_string;
    foreach ($this->vars as $key => $value)
    {
      $_SERVER[strtoupper($key)] = $value;
    }

    // request parameters
    $_GET = $_POST = array();
    if (strtoupper($method) == 'POST')
    {
      $_POST = $parameters;
    }
    if (strtoupper($method) == 'GET')
    {
      $_GET  = $parameters;
    }
    parse_str($query_string, $qs);
    if (is_array($qs))
    {
      $_GET = array_merge($qs, $_GET);
    }

    // restore cookies
    $_COOKIE = array();
    foreach ($this->cookieJar as $name => $cookie)
    {
      $_COOKIE[$name] = $cookie['value'];
    }

    // recycle our context object
    sfContext::removeInstance();
    $this->context = sfContext::getInstance();

    // launch request via controller
    $controller = $this->context->getController();
    $request    = $this->context->getRequest();
    $response   = $this->context->getResponse();

    // we register a fake rendering filter
    sfConfig::set('sf_rendering_filter', array('sfFakeRenderingFilter', null));

    $this->currentException = null;

    // dispatch our request
    ob_start();
    try
    {
      $controller->dispatch();
    }
    catch (sfException $e)
    {
      $this->currentException = $e;

      $e->printStackTrace();
    }
    catch (Exception $e)
    {
      $this->currentException = $e;

      $sfException = new sfException();
      $sfException->printStackTrace($e);
    }
    $retval = ob_get_clean();

    if ($this->currentException instanceof sfStopException)
    {
      $this->currentException = null;
    }

    // append retval to the response content
    $response->setContent($retval);

    // manually shutdown user to save current session data
    $this->context->getUser()->shutdown();
    $this->context->getStorage()->shutdown();

    // save cookies
    $this->cookieJar = array();
    foreach ($response->getCookies() as $name => $cookie)
    {
      // FIXME: deal with expire, path, secure, ...
      $this->cookieJar[$name] = $cookie;
    }

    // for HTML/XML content, create a DOM and sfDomCssSelector objects for the response content
    if (preg_match('/(x|ht)ml/i', $response->getContentType()))
    {
      $this->dom = new DomDocument('1.0', sfConfig::get('sf_charset'));
      $this->dom->validateOnParse = true;
      @$this->dom->loadHTML($response->getContent());
      $this->domCssSelector = new sfDomCssSelector($this->dom);
    }

    return $this;
  }

  public function back()
  {
    if ($this->stackPosition < 1)
    {
      throw new sfException('You are already on the first page.');
    }

    --$this->stackPosition;
    return $this->call($this->stack[$this->stackPosition]['uri'], $this->stack[$this->stackPosition]['method'], $this->stack[$this->stackPosition]['parameters'], false);
  }

  public function forward()
  {
    if ($this->stackPosition > count($this->stack) - 2)
    {
      throw new sfException('You are already on the last page.');
    }

    ++$this->stackPosition;
    return $this->call($this->stack[$this->stackPosition]['uri'], $this->stack[$this->stackPosition]['method'], $this->stack[$this->stackPosition]['parameters'], false);
  }

  public function reload()
  {
    if (-1 == $this->stackPosition)
    {
      throw new sfException('No page to reload.');
    }

    return $this->call($this->stack[$this->stackPosition]['uri'], $this->stack[$this->stackPosition]['method'], $this->stack[$this->stackPosition]['parameters'], false);
  }

  public function getResponseDomCssSelector()
  {
    return $this->domCssSelector;
  }

  public function getResponseDom()
  {
    return $this->dom;
  }

  public function getContext()
  {
    return $this->context;
  }

  public function getResponse()
  {
    return $this->context->getResponse();
  }

  public function getRequest()
  {
    return $this->context->getRequest();
  }

  public function getCurrentException()
  {
    return $this->currentException;
  }

  public function followRedirect()
  {
    if (null === $this->getContext()->getResponse()->getHttpHeader('Location'))
    {
      throw new sfException('The request was not redirected');
    }

    return $this->get($this->getContext()->getResponse()->getHttpHeader('Location'));
  }

  public function setField($name, $value)
  {
    // as we don't know yet the form, just store name/value pairs
    $this->parseArgumentAsArray($name, $value, $this->fields);

    return $this;
  }

  // link or button
  public function click($name, $arguments = array())
  {
    if (!$this->dom)
    {
      throw new sfException('Cannot click because there is no current page in the browser');
    }

    $xpath = new DomXpath($this->dom);
    $dom   = $this->dom;

    // text link
    if ($link = $xpath->query(sprintf('//a[.="%s"]', $name))->item(0))
    {
      return $this->get($link->getAttribute('href'));
    }

    // image link
    if ($link = $xpath->query(sprintf('//a/img[@alt="%s"]/ancestor::a', $name))->item(0))
    {
      return $this->get($link->getAttribute('href'));
    }

    // form
    if (!$form = $xpath->query(sprintf('//input[((@type="submit" or @type="button") and @value="%s") or (@type="image" and @alt="%s")]/ancestor::form', $name, $name))->item(0))
    {
      throw new sfException(sprintf('Cannot find the "%s" link or button.', $name));
    }

    // form attributes
    $url = $form->getAttribute('action');
    $method = $form->getAttribute('method') ? strtolower($form->getAttribute('method')) : 'get';

    // merge form default values and arguments
    $defaults = array();
    foreach ($xpath->query('descendant::input | descendant::textarea | descendant::select', $form) as $element)
    {
      $elementName = $element->getAttribute('name');
      $nodeName    = $element->nodeName;
      $value       = null;
      if ($nodeName == 'input' && ($element->getAttribute('type') == 'checkbox' || $element->getAttribute('type') == 'radio'))
      {
        if ($element->getAttribute('checked'))
        {
          $value = $element->getAttribute('value');
        }
      }
      else if (
        $nodeName == 'input'
        &&
        (($element->getAttribute('type') != 'submit' && $element->getAttribute('type') != 'button') || $element->getAttribute('value') == $name)
        &&
        ($element->getAttribute('type') != 'image' || $element->getAttribute('alt') == $name)
      )
      {
        $value = $element->getAttribute('value');
      }
      else if ($nodeName == 'textarea')
      {
        $value = '';
        foreach ($element->childNodes as $el)
        {
          $value .= $dom->saveXML($el);
        }
      }
      else if ($nodeName == 'select')
      {
        if ($multiple = $element->hasAttribute('multiple'))
        {
          $elementName = str_replace('[]', '', $elementName);
          $value = array();
        }
        else
        {
          $value = null;
        }

        $found = false;
        foreach ($xpath->query('descendant::option', $element) as $option)
        {
          if ($option->getAttribute('selected'))
          {
            $found = true;
            if ($multiple)
            {
              $value[] = $option->getAttribute('value');
            }
            else
            {
              $value = $option->getAttribute('value');
            }
          }
        }

        // if no option is selected and if it is a simple select box, take the first option as the value
        if (!$found && !$multiple)
        {
          $value = $xpath->query('descendant::option', $element)->item(0)->getAttribute('value');
        }
      }

      if (null !== $value)
      {
        $this->parseArgumentAsArray($elementName, $value, $defaults);
      }
    }

    // create request parameters
    $arguments = sfToolkit::arrayDeepMerge($defaults, $this->fields, $arguments);
    if ('post' == $method)
    {
      return $this->post($url, $arguments);
    }
    else
    {
      $query_string = http_build_query($arguments);
      $sep = false === strpos($url, '?') ? '?' : '&';

      return $this->get($url.($query_string ? $sep.$query_string : ''));
    }
  }

  protected function parseArgumentAsArray($name, $value, &$vars)
  {
    if (false !== $pos = strpos($name, '['))
    {
      $var = &$vars;
      $tmps = array_filter(preg_split('/(\[ | \[\] | \])/x', $name));
      foreach ($tmps as $tmp)
      {
        $var = &$var[$tmp];
      }
      if ($var)
      {
        if (!is_array($var))
        {
          $var = array($var);
        }
        $var[] = $value;
      }
      else
      {
        $var = $value;
      }
    }
    else
    {
      $vars[$name] = $value;
    }
  }

  public function restart()
  {
    $this->newSession();
    $this->cookieJar     = array();
    $this->stack         = array();
    $this->fields        = array();
    $this->vars          = array();
    $this->dom           = null;
    $this->stackPosition = -1;

    return $this;
  }

  public function shutdown()
  {
    // we remove all session data
    sfToolkit::clearDirectory(sfConfig::get('sf_test_cache_dir').'/sessions');
  }

  protected function fixUri($uri)
  {
    // remove absolute information if needed (to be able to do follow redirects, click on links, ...)
    if (0 === strpos($uri, 'http'))
    {
      // detect secure request
      if (0 === strpos($uri, 'https'))
      {
        $this->defaultServerArray['HTTPS'] = 'on';
      }
      else
      {
        unset($this->defaultServerArray['HTTPS']);
      }

      $uri = substr($uri, strpos($uri, 'index.php') + strlen('index.php'));
    }
    $uri = str_replace('/index.php', '', $uri);

    // # as a uri
    if ($uri && '#' == $uri[0])
    {
      $uri = $this->stack[$this->stackPosition]['uri'].$uri;
    }

    return $uri;
  }

  protected function newSession()
  {
    $_SERVER['session_id'] = md5(uniqid(rand(), true));
  }
}

class sfFakeRenderingFilter extends sfFilter
{
  public function execute($filterChain)
  {
    $filterChain->execute();

    $this->getContext()->getResponse()->sendContent();
  }
}
