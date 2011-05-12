<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFillInForm.class.php 2976 2006-12-08 19:11:36Z fabien $
 */
class sfFillInForm
{
  protected
    $converters  = array(),
    $skipFields  = array(),
    $types       = array('text', 'checkbox', 'radio', 'hidden', 'password');

  public function addConverter($callable, $fields)
  {
    foreach ((array) $fields as $field)
    {
      $this->converters[$field][] = $callable;
    }
  }

  public function setSkipFields($fields)
  {
    $this->skipFields = $fields;
  }

  public function setTypes($types)
  {
    $this->types = $types;
  }

  public function fillInHtml($html, $formName, $formId, $values)
  {
    $dom = new DomDocument('1.0', sfConfig::get('sf_charset', 'UTF-8'));
    @$dom->loadHTML($html);

    $dom = $this->fillInDom($dom, $formName, $formId, $values);

    return $dom->saveHTML();
  }

  public function fillInXml($xml, $formName, $formId, $values)
  {
    $dom = new DomDocument('1.0', sfConfig::get('sf_charset', 'UTF-8'));
    @$dom->loadXML($xml);

    $dom = $this->fillInDom($dom, $formName, $formId, $values);

    return $dom->saveXML();
  }

  public function fillInDom($dom, $formName, $formId, $values)
  {
    $xpath = new DomXPath($dom);

    $query = 'descendant::input[@name and (not(@type)';
    foreach ($this->types as $type)
    {
      $query .= ' or @type="'.$type.'"';
    }
    $query .= ')] | descendant::textarea[@name] | descendant::select[@name]';

    // find our form
    if ($formName)
    {
      $xpath_query = '//form[@name="'.$formName.'"]';
    }
    elseif ($formId)
    {
      $xpath_query = '//form[@id="'.$formId.'"]';
    }
    else
    {
      $xpath_query = '//form';
    }

    $form = $xpath->query($xpath_query)->item(0);
    if (!$form)
    {
      if (!$formName && !$formId)
      {
        throw new sfException('No form found in this page');
      }
      else
      {
        throw new sfException(sprintf('The form "%s" cannot be found', $formName ? $formName : $formId));
      }
    }

    foreach ($xpath->query($query, $form) as $element)
    {
      $name  = (string) $element->getAttribute('name');
      $value = (string) $element->getAttribute('value');
      $type  = (string) $element->getAttribute('type');

      // skip fields
      if (!$this->hasValue($values, $name) || in_array($name, $this->skipFields))
      {
        continue;
      }

      if ($element->nodeName == 'input')
      {
        if ($type == 'checkbox' || $type == 'radio')
        {
          // checkbox and radio
          $element->removeAttribute('checked');
          if ($this->hasValue($values, $name) && ($this->getValue($values, $name) == $value || !$element->hasAttribute('value')))
          {
            $element->setAttribute('checked', 'checked');
          }
        }
        else
        {
          // text input
          $element->removeAttribute('value');
          if ($this->hasValue($values, $name))
          {
            $element->setAttribute('value', $this->escapeValue($this->getValue($values, $name), $name));
          }
        }
      }
      else if ($element->nodeName == 'textarea')
      {
        $el = $element->cloneNode(false);
        $el->appendChild($dom->createTextNode($this->escapeValue($this->getValue($values, $name), $name)));
        $element->parentNode->replaceChild($el, $element);
      }
      else if ($element->nodeName == 'select')
      {
        // select
        $value    = $this->getValue($values, $name);
        $multiple = $element->hasAttribute('multiple');
        foreach ($xpath->query('descendant::option', $element) as $option)
        {
          $option->removeAttribute('selected');
          if ($multiple && is_array($value))
          {
            if (in_array($option->getAttribute('value'), $value))
            {
              $option->setAttribute('selected', 'selected');
            }
          }
          else if ($value == $option->getAttribute('value'))
          {
            $option->setAttribute('selected', 'selected');
          }
        }
      }
    }

    return $dom;
  }

  protected function hasValue($values, $name)
  {
    if (array_key_exists($name, $values))
    {
      return true;
    }

    return null !== sfToolkit::getArrayValueForPath($values, $name);
  }

  protected function getValue($values, $name)
  {
    if (array_key_exists($name, $values))
    {
      return $values[$name];
    }

    return sfToolkit::getArrayValueForPath($values, $name);
  }

  protected function escapeValue($value, $name)
  {
    if (function_exists('iconv') && strtolower(sfConfig::get('sf_charset')) != 'utf-8')
    {
      $new_value = iconv(sfConfig::get('sf_charset'), 'UTF-8', $value);
      if (false !== $new_value)
      {
        $value = $new_value;
      }
    }

    if (isset($this->converters[$name]))
    {
      foreach ($this->converters[$name] as $callable)
      {
        $value = call_user_func($callable, $value);
      }
    }

    return $value;
  }
}
