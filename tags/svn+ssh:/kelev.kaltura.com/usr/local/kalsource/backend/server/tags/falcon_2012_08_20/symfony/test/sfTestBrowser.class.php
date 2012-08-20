<?php

require_once(dirname(__FILE__).'/../vendor/lime/lime.php');

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTestBrowser simulates a fake browser which can test a symfony application.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestBrowser.class.php 3329 2007-01-23 08:29:34Z fabien $
 */
class sfTestBrowser extends sfBrowser
{
  protected
    $test = null;

  /**
   * Initializes the browser tester instance.
   *
   * @param string Hostname
   * @param string Remote IP address
   * @param array  Options
   */
  public function initialize($hostname = null, $remote = null, $options = array())
  {
    parent::initialize($hostname, $remote, $options);

    $output = isset($options['output']) ? $options['output'] : new lime_output_color();

    $this->test = new lime_test(null, $output);
  }

  /**
   * Retrieves the lime_test instance.
   *
   * @return sfTestBrowser The lime_test instance
   */
  public function test()
  {
    return $this->test;
  }

  /**
   * Retrieves and checks an action.
   *
   * @param string Module name
   * @param string Action name
   * @param string Url
   * @param string The expected return status code
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function getAndCheck($module, $action, $url = null, $code = 200)
  {
    return $this->
      get(null !== null ? $url : sprintf('/%s/%s', $module, $action))->
      isStatusCode($code)->
      isRequestParameter('module', $module)->
      isRequestParameter('action', $action)
    ;
  }

  /**
   * Calls a request.
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
  {
    $uri = $this->fixUri($uri);

    $this->test->comment(sprintf('%s %s', strtolower($method), $uri));

    return parent::call($uri, $method, $parameters, $changeStack);
  }

  /**
   * Simulates the browser back button.
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function back()
  {
    $this->test->comment('back');

    return parent::back();
  }

  /**
   * Simulates the browser forward button.
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function forward()
  {
    $this->test->comment('forward');

    return parent::forward();
  }

  /**
   * Tests if the current request has been redirected.
   *
   * @param boolean Flag for redirection mode
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function isRedirected($boolean = true)
  {
    if ($location = $this->getContext()->getResponse()->getHttpHeader('location'))
    {
      $boolean ? $this->test->pass(sprintf('page redirected to "%s"', $location)) : $this->test->fail(sprintf('page redirected to "%s"', $location));
    }
    else
    {
      $boolean ? $this->test->fail('page redirected') : $this->test->pass('page not redirected');
    }

    return $this;
  }

  /**
   * Checks that the current response contains a given text.
   *
   * @param string Uniform resource identifier
   * @param string Text in the response
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function check($uri, $text = null)
  {
    $this->get($uri)->isStatusCode();

    if ($text !== null)
    {
      $this->responseContains($text);
    }

    return $this;
  }

  /**
   * Test an status code for the current test browser.
   *
   * @param string Status code to check, default 200
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function isStatusCode($statusCode = 200)
  {
    $this->test->is($this->getResponse()->getStatusCode(), $statusCode, sprintf('status code is "%s"', $statusCode));

    return $this;
  }

  /**
   * Tests whether or not a given string is in the response.
   *
   * @param string Text to check
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function responseContains($text)
  {
    $this->test->like($this->getResponse()->getContent(), '/'.preg_quote($text, '/').'/', sprintf('response contains "%s"', substr($text, 0, 40)));

    return $this;
  }

  /**
   * Tests whether or not a given key and value exists in the current request.
   *
   * @param string Key
   * @param string Value
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function isRequestParameter($key, $value)
  {
    $this->test->is($this->getRequest()->getParameter($key), $value, sprintf('request parameter "%s" is "%s"', $key, $value));

    return $this;
  }

  /**
   * Checks that the request is forwarded to a given module/action.
   *
   * @param string The module name
   * @param string The action name
   * @param mixed  The position in the action stack (default to the last entry)
   *
   * @return object this
   */
  public function isForwardedTo($moduleName, $actionName, $position = 'last')
  {
    $actionStack = $this->getContext()->getActionStack();

    switch ($position)
    {
      case 'first':
        $entry = $actionStack->getFirstEntry();
        break;
      case 'last':
        $entry = $actionStack->getLastEntry();
        break;
      default:
        $entry = $actionStack->getEntry($position);
    }

    $this->test->is($entry->getModuleName(), $moduleName, sprintf('request is forwarded to the "%s" module (%s)', $moduleName, $position));
    $this->test->is($entry->getActionName(), $actionName, sprintf('request is forwarded to the "%s" action (%s)', $actionName, $position));

    return $this;
  }

  /**
   * Tests for a response header.
   *
   * @param string Key
   * @param string Value
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function isResponseHeader($key, $value)
  {
    $headers = $this->getResponse()->getHttpHeader($key);

    $ok = false;
    foreach ($headers as $header)
    {
      if ($header == $value)
      {
        $ok = true;
        break;
      }
    }

    $this->test->ok($ok, sprintf('response header "%s" is "%s"', $key, $value));

    return $this;
  }

  /**
   * Tests that the current response matches a given CSS selector.
   *
   * @param string The response selector
   * @param string Flag for the selector
   * @param array Options for the current test
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function checkResponseElement($selector, $value = true, $options = array())
  {
    $texts = $this->getResponseDomCssSelector()->getTexts($selector);

    if (false === $value)
    {
      $this->test->is(count($texts), 0, sprintf('response selector "%s" does not exist', $selector));
    }
    else if (true === $value)
    {
      $this->test->cmp_ok(count($texts), '>', 0, sprintf('response selector "%s" exists', $selector));
    }
    else if (is_int($value))
    {
      $this->test->is(count($texts), $value, sprintf('response selector "%s" matches "%s" times', $selector, $value));
    }
    else if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      if ($match[1] == '!')
      {
        $this->test->unlike(@$texts[$position], substr($value, 1), sprintf('response selector "%s" does not match regex "%s"', $selector, substr($value, 1)));
      }
      else
      {
        $this->test->like(@$texts[$position], $value, sprintf('response selector "%s" matches regex "%s"', $selector, $value));
      }
    }
    else
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      $this->test->is(@$texts[$position], $value, sprintf('response selector "%s" matches "%s"', $selector, $value));
    }

    if (isset($options['count']))
    {
      $this->test->is(count($texts), $options['count'], sprintf('response selector "%s" matches "%s" times', $selector, $options['count']));
    }

    return $this;
  }

  /**
   * Tests if an exception is thrown by the latest request.
   *
   * @param string Class name
   * @param string Message name
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function throwsException($class = null, $message = null)
  {
    $e = $this->getCurrentException();

    if (null === $e)
    {
      $this->test->fail('response returns an exception');
    }
    else
    {
      if (null !== $class)
      {
        $this->test->ok($e instanceof $class, sprintf('response returns an exception of class "%s"', $class));
      }

      if (null !== $message && preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $message, $match))
      {
        if ($match[1] == '!')
        {
          $this->test->unlike($e->getMessage(), substr($message, 1), sprintf('response exception message does not match regex "%s"', $message));
        }
        else
        {
          $this->test->like($e->getMessage(), $message, sprintf('response exception message matches regex "%s"', $message));
        }
      }
      else if (null !== $message)
      {
        $this->test->is($e->getMessage(), $message, sprintf('response exception message matches regex "%s"', $message));
      }
    }

    return $this;
  }

  /**
   * Tests if the given uri is cached.
   *
   * @param boolean Flag for checking the cache
   * @param boolean If have or not layout
   *
   * @return sfBrowser The current sfBrowser instance
   */
  public function isCached($boolean, $with_layout = false)
  {
    return $this->isUriCached(sfRouting::getInstance()->getCurrentInternalUri(), $boolean, $with_layout);
  }

  /**
   * Tests if the given uri is cached.
   *
   * @param string Uniform resource identifier
   * @param boolean Flag for checking the cache
   * @param boolean If have or not layout
   *
   * @param sfTestBrowser Test browser instance
   */
  public function isUriCached($uri, $boolean, $with_layout = false)
  {
    $cacheManager = $this->getContext()->getViewCacheManager();

    // check that cache is enabled
    if (!$cacheManager)
    {
      $this->test->ok(!$boolean, 'cache is disabled');

      return $this;
    }

    if ($uri == sfRouting::getInstance()->getCurrentInternalUri())
    {
      $main = true;
      $type = $with_layout ? 'page' : 'action';
    }
    else
    {
      $main = false;
      $type = $uri;
    }

    // check layout configuration
    if ($cacheManager->withLayout($uri) && !$with_layout)
    {
      $this->test->fail('cache without layout');
      $this->test->skip('cache is not configured properly', 2);
    }
    else if (!$cacheManager->withLayout($uri) && $with_layout)
    {
      $this->test->fail('cache with layout');
      $this->test->skip('cache is not configured properly', 2);
    }
    else
    {
      $this->test->pass('cache is configured properly');

      // check page is cached
      $ret = $this->test->is($cacheManager->has($uri), $boolean, sprintf('"%s" %s in cache', $type, $boolean ? 'is' : 'is not'));

      // check that the content is ok in cache
      if ($boolean)
      {
        if (!$ret)
        {
          $this->test->fail('content in cache is ok');
        }
        else if ($with_layout)
        {
          $response = unserialize($cacheManager->get($uri));
          $content = $response->getContent();
          $this->test->ok($content == $this->getResponse()->getContent(), 'content in cache is ok');
        }
        else if (true === $main)
        {
          $ret = unserialize($cacheManager->get($uri));
          $content = $ret['content'];
          $this->test->ok(false !== strpos($this->getResponse()->getContent(), $content), 'content in cache is ok');
        }
        else
        {
          $content = $cacheManager->get($uri);
          $this->test->ok(false !== strpos($this->getResponse()->getContent(), $content), 'content in cache is ok');
        }
      }
    }

    return $this;
  }
}

/**
 * Error handler for the current test browser instance.
 *
 * @param mixed Error number
 * @param string Error message
 * @param string Error file
 * @param mixed Error line
 */
function sfTestBrowserErrorHandler($errno, $errstr, $errfile, $errline)
{
  if (($errno & error_reporting()) == 0)
  {
    return;
  }

  $msg = sprintf('PHP send a "%s" error at %s line %s (%s)', '%s', $errfile, $errline, $errstr);
  switch ($errno)
  {
    case E_WARNING:
      throw new Exception(sprintf($msg, 'warning'));
      break;
    case E_NOTICE:
      throw new Exception(sprintf($msg, 'notice'));
      break;
    case E_STRICT:
      throw new Exception(sprintf($msg, 'strict'));
      break;
  }
}

set_error_handler('sfTestBrowserErrorHandler');
