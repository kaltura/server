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
 * sfSecurityUser interface provides advanced security manipulation methods.
 *
 * @package    symfony
 * @subpackage user
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfSecurityUser.class.php 2971 2006-12-08 12:14:14Z fabien $
 */
interface sfSecurityUser
{
  /**
   * Add a credential to this user.
   *
   * @param mixed Credential data.
   *
   * @return void
   */
  public function addCredential($credential);

  /**
   * Clear all credentials associated with this user.
   *
   * @return void
   */
  public function clearCredentials();

  /**
   * Indicates whether or not this user has a credential.
   *
   * @param mixed Credential data.
   *
   * @return bool true, if this user has the credential, otherwise false.
   */
  public function hasCredential($credential);

  /**
   * Indicates whether or not this user is authenticated.
   *
   * @return bool true, if this user is authenticated, otherwise false.
   */
  public function isAuthenticated();

  /**
   * Remove a credential from this user.
   *
   * @param mixed Credential data.
   *
   * @return void
   */
  public function removeCredential($credential);

  /**
   * Set the authenticated status of this user.
   *
   * @param bool A flag indicating the authenticated status of this user.
   *
   * @return void
   */
  public function setAuthenticated($authenticated);
}
