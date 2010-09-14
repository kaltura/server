<?php

/**
 * mail actions.
 *
 * @package    kaltura
 * @subpackage mail
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class mailActions extends sfActions
{
  /**
   * Executes index action
   *
   */
  public function executeDefMail()
  {
  	$this->forward('default', 'module');
  }


  public function executeSendPassword()
  {
  	// determine customer from the request 'id' parameter 
	$c = new Criteria();
	$c->add(kuserPeer::ID, $this->getRequest()->getAttribute('id') );
	$user = kuserPeer::doSelectOne($c);
			
	if( $user && $user->getEmail() == $this->getRequest()->getAttribute('email') )
	{
		// class initialization
	  	$mail = new sfMail();
	  	$mail->setCharset('utf-8');
	  	// definition of the required parameters
	  	$mail->setSender('password-reminder@kaltura.com', 'Kaltura Customer Service');
	  	$mail->setFrom('password-reminder@kaltura.com', 'Kaltura Customer Service');
	  	$mail->addReplyTo('password-reminder@kaltura.com');
	  	$mail->addAddress($user->getEmail());
	  	$mail->setSubject('Your Kaltura password reminder');
	  	
	  	// create a new temporary code to be sent by email
	  	// then the user will be asked to change the password
	  	
	  	$this->tempCode = sha1($user->getSalt().$user->getScreenName().$user->getSha1Password());
	  	
	  	if( $user->getFullName() != null )
	  	{
	  		$this->name = $user->getFullName();
	  	}
	  	else
	  	{
	  		$this->name = "";	
	  	}
	  		  	
	  	$this->mail = $mail;
	}
	else   	
	{
		$this->getRequest()->setError('email', 'Problem while sending email');	
		$this->redirect( 'login/passwordRequest?error=Unknown');
	}
	
  }


  public function executeSendRegistrationConfirmation()
  {
  	// determine customer from the request 'id' parameter 
	$c = new Criteria();
	$c->add(kuserPeer::ID, $this->getRequest()->getAttribute('id') );
	$user = kuserPeer::doSelectOne($c);
			
	if( $user && $user->getEmail() == $this->getRequest()->getAttribute('email') )
	{
		// class initialization
	  	$mail = new sfMail();
	  	$mail->setCharset('utf-8');
	  	// definition of the required parameters
	  	$mail->setSender('support@kaltura.com', 'Kaltura Customer Service');
	  	$mail->setFrom('support@kaltura.com', 'Kaltura Customer Service');
	  	$mail->addReplyTo('support@kaltura.com');
	  	$mail->addAddress($user->getEmail());
	  	$mail->setSubject('Welcome to Kaltura!');
	  		  	
	  	$this->name = $user->getScreenName();
	  	
	  	$this->mail = $mail;
	}
	else   	
	{
		$this->getRequest()->setError('email', 'Problem while sending email');	
		$this->redirect( 'login/sendRegistrationConfirmation?error=Unknown');
	}
  }
  
  
  
}
