<?php
class ErrorController extends Zend_Controller_Action
{
	const ACL_RESOURCE_NOT_EXCEPTION_PATTERN = '/Resource \'\\w*\' not found/';
	
	public function errorAction()
	{
		$this->_helper->layout->disableLayout();
		$errors = $this->_getParam('error_handler');
		
		// handle kaltura session expired
		if (get_class($errors->exception) == 'Kaltura_Client_Exception')
		{
			if (strpos($errors->exception->getMessage(), 'EXPIRED'))
			{
				Zend_Auth::getInstance()->clearIdentity();
				$this->_helper->redirector('login', 'user');
			}
		}
		
		// handle Zend MVC errors
		switch ($errors->type) 
		{ 
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->handleNotFoundException($errors->exception);
				break;
			default:
				if (preg_match(self::ACL_RESOURCE_NOT_EXCEPTION_PATTERN, $errors->exception->getMessage()))
					$this->handleNotFoundException($errors->exception);
				else
					$this->handleApplicationException($errors->exception);
				break;
		}
		
		$this->view->exception = $errors->exception;
		$this->view->request   = $errors->request;
	}
	
	public function deniedAction()
	{
		$this->_helper->layout->disableLayout();
		die('Access denied');
	}
	
	public function handleNotFoundException(Exception $ex)
	{
		$this->getResponse()->setHttpResponseCode(404);
		$this->_helper->viewRenderer('not-found');
	}

	public function handleApplicationException(Exception $ex)
	{
		KalturaLog::ERR($ex);
		$this->getResponse()->setHttpResponseCode(500);
		$this->view->message = 'Application error';
	}
}