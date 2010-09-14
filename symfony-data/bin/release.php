<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Release script.
 *
 * Usage: php data/bin/release.php 1.0.0 stable
 *
 * @package    symfony
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
require_once(dirname(__FILE__).'/../../lib/vendor/pake/pakeFunction.php');
require_once(dirname(__FILE__).'/../../lib/vendor/pake/pakeGetopt.class.php');
require_once(dirname(__FILE__).'/../../lib/vendor/lime/lime.php');

if (!isset($argv[1]))
{
  throw new Exception('You must provide version prefix.');
}

if (!isset($argv[2]))
{
  throw new Exception('You must provide stability status (alpha/beta/stable).');
}

$stability = $argv[2];

if (($stability == 'beta' || $stability == 'alpha') && count(explode('.', $argv[1])) < 2)
{
  $version_prefix = $argv[1];

  $result = pake_sh('svn status -u '.getcwd());
  if (preg_match('/Status against revision\:\s+(\d+)\s*$/im', $result, $match))
  {
    $version = $match[1];
  }

  if (!isset($version))
  {
    throw new Exception('unable to find last svn revision');
  }

  // make a PEAR compatible version
  $version = $version_prefix.'.'.$version;
}
else
{
  $version = $argv[1];
}

print 'releasing symfony version "'.$version."\"\n";

// Test
$h = new lime_harness(new lime_output_color());

$h->base_dir = realpath(dirname(__FILE__).'/../../test');

// unit tests
$h->register_glob($h->base_dir.'/unit/*/*Test.php');

// functional tests
$h->register_glob($h->base_dir.'/functional/*Test.php');
$h->register_glob($h->base_dir.'/functional/*/*Test.php');

$ret = $h->run();

if (!$ret)
{
  throw new Exception('Some tests failed. Release process aborted!');
}

if (is_file('package.xml'))
{
  pake_remove('package.xml', getcwd());
}

pake_copy(getcwd().'/package.xml.tmpl', getcwd().'/package.xml');

// add class files
$finder = pakeFinder::type('file')->ignore_version_control()->relative();
$xml_classes = '';
$dirs = array('lib' => 'php', 'data' => 'data');
foreach ($dirs as $dir => $role)
{
  $class_files = $finder->in($dir);
  foreach ($class_files as $file)
  {
    $xml_classes .= '<file role="'.$role.'" baseinstalldir="symfony" install-as="'.$file.'" name="'.$dir.'/'.$file.'" />'."\n";
  }
}

// replace tokens
pake_replace_tokens('package.xml', getcwd(), '##', '##', array(
  'SYMFONY_VERSION' => $version,
  'CURRENT_DATE'    => date('Y-m-d'),
  'CLASS_FILES'     => $xml_classes,
  'STABILITY'       => $stability,
));

$results = pake_sh('pear package');
echo $results;

pake_remove('package.xml', getcwd());

// copy .tgz as symfony-latest.tgz
pake_copy(getcwd().'/symfony-'.$version.'.tgz', getcwd().'/symfony-latest.tgz');

exit(0);
