<?php
require_once ( "kalturaSalesActions.class.php");
/**
 * system actions.
 *
 * @package    kaltura
 * @subpackage system
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class salestoolsActions extends kalturaSalesActions
{
  /**
  * Executes index action
  *
  */
  public function executeIndex()
  {
    $this->forceSystemAuthentication();
    if (isset($_GET['logout']) && $_GET['logout'] == 1)
    {
      $this->systemLogout();
      $this->redirect('salestools', 'login');
    }
  }
  
  public function executeLogin()
  {
    // reset values:
    $this->result = 0;
    $this->sign_in_referer = '';
    $this->login = '';
    if(isset($_POST['pwd']) && isset($_POST['login']))
    {
      $this->sign_in_referer = $this->getFlash('sign_in_referer');
      if($this->validatePassword($_POST['pwd']))
      {
        setcookie( self::COOKIE_NAME , $this->authKey() , time() + self::SYSTEM_CRED_EXPIRY_SEC , "/"  );
        //$this->redirect('salestools', 'index');
        $this->result = 1;
      }
      else
      {
        $this->result = -1;
      }
    }
    else
    {
      if($this->forceSystemAuthentication(0))
      {
        $this->redirect('salestools', 'index');
      }
    }
  }
  
  public function executeLoadpartner()
  {
    error_reporting(E_ALL);
    if(!$this->forceSystemAuthentication(0))
    {
      die('must login to do that !');
    }
    if(isset($_GET['action']) && $_GET['action'] == 'loadPartner')
    {
      /**
       * pid & hash combination should only be sent from corp upgrade page
       */
      if(isset($_GET['pid']) && isset($_GET['h']))
      {
        $this->partner = PartnerPeer::retrieveByPK((int)$_GET['pid']);
        if(!$this->partner)
        {
          die('could not load partner');
        }
        if(myPartnerUtils::getEmailLinkHash($this->partner->getId(),$this->partner->getSecret()) != $_GET['h'])
        {
          die('wrong pid/hash combination');
        }
        /**
         * since this is only requested from corp upgrade page (or other internal uses of kaltura)
         * we can output here raw data instead of HTML for the salestools pages
         */
        $data_array = array (
            'name' => $this->partner->getPartnerName(),
            'email' => $this->partner->getAdminEmail(),
            'phone' => $this->partner->getPhone(),
        );
        echo serialize($data_array);
        exit();
      }
      elseif(isset($_GET['pid']) && !isset($_GET['h']))
      {
        /**
         * pid sent, hash not - suspected malicious manipulation
         */
        die();
      }
      elseif (isset($_GET['partnerId']))
      {
        /**
         * if sent partnerId - verify that hash was not sent.
         * if hash sent - suspected as a malicious manipulation
         */
        if(isset($_GET['h']))
        {
          die();
        }
        $this->partner = PartnerPeer::retrieveByPK((int)$_GET['partnerId']);
        if(!$this->partner)
        {
          die('could not load partner');
          throw('could not load partner');
        }
      }
      $packages = new PartnerPackages();
      $this->packages_list ='';
      foreach($packages->listPackages() as $p)
      {
        $selected = ($this->partner->getPartnerPackage() == $p['id'])? 'selected="selected"': '';
        $this->packages_list .= '<option value="'.$p['id'].'" '. $selected .'>'.$p['name'].'</option>'.PHP_EOL;
      }
    }
    else
    {
      /**
       * page requested without action - wrong.
       * this page should always be used as AJAX/back-end request
       */
      die('incorrect action');
    }
  }
  
  public function executeUpdatepartner()
  {
    if(!$this->forceSystemAuthentication(0))
    {
      die('must login to do that !');
    }    
    $pid = $_GET['partnerId'];
    $partner = PartnerPeer::retrieveByPK((int)$pid);
    if(!$partner)
    {
      die('error');
    }
    $save_flag = false;
    if(isset($_GET['partnerPackage']) && $_GET['partnerPackage'] != $partner->getPartnerPackage())
    {
      $partner->setPartnerPackage($_GET['partnerPackage']);
      $save_flag = true;
    }
    if(isset($_GET['monitorUsage']) && $_GET['monitorUsage'] != $partner->getMonitorUsage())
    {
      $partner->setMonitorUsage($_GET['monitorUsage']);
      $save_flag = true;
    }
    if(isset($_GET['licenseJW']) && $_GET['licenseJW'] != $partner->getLicensedJWPlayer())
    {
      $partner->setLicensedJWPlayer($_GET['licenseJW']);
      $save_flag = true;
    }    
    if($save_flag)
    {
      $partner->save();
    }
    die('ok');
  }
  
  public function executePartnersettings()
  {
    $this->forceSystemAuthentication();

  }
}
