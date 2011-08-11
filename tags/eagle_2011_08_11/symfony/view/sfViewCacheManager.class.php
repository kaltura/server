<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class to cache the HTML results for actions and templates.
 *
 * This class uses $cacheClass class to store cache.
 * All cache files are stored in files in the [sf_root_dir].'/cache/'.[sf_app].'/html' directory.
 * To disable all caching, you can set to false [sf_cache] constant.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfViewCacheManager.class.php 3232 2007-01-11 20:51:54Z fabien $
 */
class sfViewCacheManager
{
  protected
    $cache              = null,
    $cacheConfig        = array(),
    $context            = null,
    $controller         = null,
    $loaded             = array();

  /**
   * Initializes the cache manager.
   *
   * @param sfContext Current application context
   * @param sfCache Type of the cache
   * @param array Cache parameters
   */
  public function initialize($context, $cacheClass, $cacheParameters = array())
  {
    $this->context    = $context;
    $this->controller = $context->getController();

    // empty configuration
    $this->cacheConfig = array();

    // create cache instance
    $this->cache = new $cacheClass();
    $this->cache->initialize($cacheParameters);

    // register a named route for our partial cache (at the end)
    $r = sfRouting::getInstance();
    if (!$r->hasRouteName('sf_cache_partial'))
    {
      $r->connect('sf_cache_partial', '/sf_cache_partial/:module/:action/:sf_cache_key.', array(), array());
    }
  }

  /**
   * Retrieves the current cache context.
   *
   * @return sfContext The sfContext instance
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the current cache type.
   *
   * @return sfCache The current cache type
   */
  public function getCache()
  {
    return $this->cache;
  }

  /**
   * Generates namespaces for the cache manager
   *
   * @param string Internal unified resource identifier.
   *
   * @return array Path and filename for the current namespace
   *
   * @throws <b>sfException</b> if the generation fails
   */
  public function generateNamespace($internalUri)
  {
    if ($callable = sfConfig::get('sf_cache_namespace_callable'))
    {
      if (!is_callable($callable))
      {
        throw new sfException(sprintf('"%s" cannot be called as a function.', var_export($callable, true)));
      }

      return call_user_func($callable, $internalUri);
    }

    // generate uri
    // we want our URL with / only
    $oldUrlFormat = sfConfig::get('sf_url_format');
    sfConfig::set('sf_url_format', 'PATH');
    if ($this->isContextual($internalUri))
    {
      list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);
      $uri = $this->controller->genUrl(sfRouting::getInstance()->getCurrentInternalUri()).sprintf('/%s/%s/%s', $params['module'], $params['action'], $params['sf_cache_key']);
    }
    else
    {
      $uri = $this->controller->genUrl($internalUri);
    }
    sfConfig::set('sf_url_format', $oldUrlFormat);

    // prefix with vary headers
    $varyHeaders = $this->getVary($internalUri);
    if ($varyHeaders)
    {
      sort($varyHeaders);
      $request = $this->getContext()->getRequest();
      $vary = '';

      foreach ($varyHeaders as $header)
      {
        $vary .= $request->getHttpHeader($header).'|';
      }

      $vary = $vary;
    }
    else
    {
      $vary = 'all';
    }

    // prefix with hostname
    $request = $this->context->getRequest();
    $hostName = $request->getHost();
    $hostName = preg_replace('/[^a-z0-9]/i', '_', $hostName);
    $hostName = strtolower(preg_replace('/_+/', '_', $hostName));

    $uri = '/'.$hostName.'/'.$vary.'/'.$uri;

    // replace multiple /
    $uri = preg_replace('#/+#', '/', $uri);

    return array(dirname($uri), basename($uri));
  }

  /**
   * Adds a cache to the manager.
   *
   * @param string Module name
   * @param string Action name
   * @param array Options for the cache
   */
  public function addCache($moduleName, $actionName, $options = array())
  {
    // normalize vary headers
    foreach ($options['vary'] as $key => $name)
    {
      $options['vary'][$key] = strtr(strtolower($name), '_', '-');
    }

    $options['lifeTime'] = isset($options['lifeTime']) ? $options['lifeTime'] : 0;
    if (!isset($this->cacheConfig[$moduleName]))
    {
      $this->cacheConfig[$moduleName] = array();
    }
    $this->cacheConfig[$moduleName][$actionName] = array(
      'withLayout'     => isset($options['withLayout']) ? $options['withLayout'] : false,
      'lifeTime'       => $options['lifeTime'],
      'clientLifeTime' => isset($options['clientLifeTime']) && $options['clientLifeTime'] ? $options['clientLifeTime'] : $options['lifeTime'],
      'contextual'     => isset($options['contextual']) ? $options['contextual'] : false,
      'vary'           => isset($options['vary']) ? $options['vary'] : array(),
    );
  }

  /**
   * Registers configuration options for the cache.
   *
   * @param string Module name
   */
  public function registerConfiguration($moduleName)
  {
    if (!isset($loaded[$moduleName]))
    {
      require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/cache.yml'));
      $loaded[$moduleName] = true;
    }
  }

  /**
   * Retrieves the layout from the cache option list.
   *
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if have layout otherwise false
   */
  public function withLayout($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'withLayout', false);
  }

  /**
   * Retrieves lifetime from the cache option list.
   *
   * @param string Internal uniform resource identifier
   *
   * @return int LifeTime
   */
  public function getLifeTime($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'lifeTime', 0);
  }

  /**
   * Retrieves client lifetime from the cache option list
   *
   * @param string Internal uniform resource identifier
   *
   * @return int Client lifetime
   */
  public function getClientLifeTime($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'clientLifeTime', 0);
  }

  /**
   * Retrieves contextual option from the cache option list.
   *
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if is contextual otherwise false
   */
  public function isContextual($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'contextual', false);
  }

  /**
   * Retrieves vary option from the cache option list.
   *
   * @param string Internal uniform resource identifier
   *
   * @return array Vary options for the cache
   */
  public function getVary($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'vary', array());
  }

  /**
   * Gets a config option from the cache.
   *
   * @param string Internal uniform resource identifier
   * @param string Option name
   * @param string Default value of the option
   *
   * @return mixed Value of the option
   */
  protected function getCacheConfig($internalUri, $key, $defaultValue = null)
  {
    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);

    $value = $defaultValue;
    if (isset($this->cacheConfig[$params['module']][$params['action']][$key]))
    {
      $value = $this->cacheConfig[$params['module']][$params['action']][$key];
    }
    else if (isset($this->cacheConfig[$params['module']]['DEFAULT'][$key]))
    {
      $value = $this->cacheConfig[$params['module']]['DEFAULT'][$key];
    }

    return $value;
  }

  /**
   * Returns true if the current content is cacheable.
   *
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if the content is cacheable otherwise false
   */
  public function isCacheable($internalUri)
  {
    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);

    if (isset($this->cacheConfig[$params['module']][$params['action']]))
    {
      return ($this->cacheConfig[$params['module']][$params['action']]['lifeTime'] > 0);
    }
    else if (isset($this->cacheConfig[$params['module']]['DEFAULT']))
    {
      return ($this->cacheConfig[$params['module']]['DEFAULT']['lifeTime'] > 0);
    }

    return false;
  }

  /**
   * Retrieves namespace for the current cache.
   *
   * @param string Internal uniform resource identifier
   *
   * @return string The data of the cache
   */
  public function get($internalUri)
  {
    // no cache or no cache set for this action
    if (!$this->isCacheable($internalUri) || $this->ignore())
    {
      return null;
    }

    list($namespace, $id) = $this->generateNamespace($internalUri);

    $this->cache->setLifeTime($this->getLifeTime($internalUri));

    $retval = $this->cache->get($id, $namespace);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info(sprintf('{sfViewCacheManager} cache for "%s" %s', $internalUri, ($retval !== null ? 'exists' : 'does not exist')));
    }

    return $retval;
  }

  /**
   * Returns true if there is a cache.
   *
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if there is a cache otherwise false
   */
  public function has($internalUri)
  {
    if (!$this->isCacheable($internalUri) || $this->ignore())
    {
      return null;
    }

    list($namespace, $id) = $this->generateNamespace($internalUri);

    $this->cache->setLifeTime($this->getLifeTime($internalUri));

    return $this->cache->has($id, $namespace);
  }

  /**
   * Ignores the cache functionality.
   *
   * @return boolean true, if the cache is ignore otherwise false
   */
  protected function ignore()
  {
    // ignore cache parameter? (only available in debug mode)
    if (sfConfig::get('sf_debug') && $this->getContext()->getRequest()->getParameter('_sf_ignore_cache', false, 'symfony/request/sfWebRequest') == true)
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->getContext()->getLogger()->info('{sfViewCacheManager} discard cache');
      }

      return true;
    }

    return false;
  }

  /**
   * Sets the cache content
   *
   * @param string Data to put in the cache
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if the data get set successfully otherwise false
   */
  public function set($data, $internalUri)
  {
    if (!$this->isCacheable($internalUri))
    {
      return false;
    }

    list($namespace, $id) = $this->generateNamespace($internalUri);

    try
    {
      $ret = $this->cache->set($id, $namespace, $data);
    }
    catch (Exception $e)
    {
      return false;
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info(sprintf('{sfViewCacheManager} save cache for "%s"', $internalUri));
    }

    return true;
  }

  /**
   * Removes the cache for the current namespace.
   *
   * @param string Internal uniform resource identifier
   *
   * @return boolean true, if the remove happend otherwise false
   */
  public function remove($internalUri)
  {
    list($namespace, $id) = $this->generateNamespace($internalUri);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info(sprintf('{sfViewCacheManager} remove cache for "%s"', $internalUri));
    }

    if ($this->cache->has($id, $namespace))
    {
      $this->cache->remove($id, $namespace);
    }
  }

  /**
   * Retrieves the last modified time.
   *
   * @param string Internal uniform resource identifier
   *
   * @return string Last modified datetime for the current namespace
   */
  public function lastModified($internalUri)
  {
    if (!$this->isCacheable($internalUri))
    {
      return null;
    }

    list($namespace, $id) = $this->generateNamespace($internalUri);

    return $this->cache->lastModified($id, $namespace);
  }

  /**
   * Starts the fragment cache.
   *
   * @param string Unique fragment name
   * @param string Life time for the cache
   * @param string Client life time for the cache
   * @param array Vary options for the cache
   *
   * @return boolean true, if success otherwise false
   */
  public function start($name, $lifeTime, $clientLifeTime = null, $vary = array())
  {
    $internalUri = sfRouting::getInstance()->getCurrentInternalUri();

    if (!$clientLifeTime)
    {
      $clientLifeTime = $lifeTime;
    }

    // add cache config to cache manager
    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);
    $this->addCache($params['module'], $params['action'], array('withLayout' => false, 'lifeTime' => $lifeTime, 'clientLifeTime' => $clientLifeTime, 'vary' => $vary));

    // get data from cache if available
    $data = $this->get($internalUri.(strpos($internalUri, '?') ? '&' : '?').'_sf_cache_key='.$name);
    if ($data !== null)
    {
      return $data;
    }
    else
    {
      ob_start();
      ob_implicit_flush(0);

      return null;
    }
  }

  /**
   * Stops the fragment cache.
   *
   * @param string Unique fragment name
   *
   * @return boolean true, if success otherwise false
   */
  public function stop($name)
  {
    $data = ob_get_clean();

    // save content to cache
    $internalUri = sfRouting::getInstance()->getCurrentInternalUri();
    try
    {
      $this->set($data, $internalUri.(strpos($internalUri, '?') ? '&' : '?').'_sf_cache_key='.$name);
    }
    catch (Exception $e)
    {
    }

    return $data;
  }

  /**
   * Executes the shutdown procedure.
   */
  public function shutdown()
  {
  }
}
