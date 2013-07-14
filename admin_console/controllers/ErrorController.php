<?php
/**
 * @package Admin
 * @subpackage Errors
 */
class ErrorController extends Zend_Controller_Action
{
	const ACL_RESOURCE_NOT_EXCEPTION_PATTERN = '/Resource \'\\w*\' not found/';

	public function errorAction()
	{
		$this->view->request = $this->getRequest();
		
		$this->_helper->layout->disableLayout();
		$errors = $this->_getParam('error_handler');

		$exception = $errors->exception;
		$this->view->exception	= $exception;
		KalturaLog::err($exception);

		// handle kaltura session expired
		if ($exception instanceof Kaltura_Client_Exception && strpos($exception->getMessage(), 'EXPIRED'))
		{
			Infra_AuthHelper::getAuthInstance()->clearIdentity();
			$this->_helper->redirector('login', 'user');
		}

		// handle Zend MVC errors
		switch ($errors->type)
		{
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->handleNotFoundException($exception);
				break;
			default:
				if (preg_match(self::ACL_RESOURCE_NOT_EXCEPTION_PATTERN, $exception->getMessage()))
					$this->handleNotFoundException($exception);
				else
					$this->handleApplicationException($exception);
				break;
		}
	}

	public function deniedAction()
	{
		$this->view->request = $this->getRequest();
		Infra_AuthHelper::getAuthInstance()->clearIdentity();
		$this->_helper->viewRenderer('error');
		$this->view->code	= Kaltura_AdminException::ERROR_CODE_ACCESS_DENIED;
		$this->getResponse()->setHttpResponseCode(403);
		$this->getResponse()->setHeader(Kaltura_AdminException::KALTURA_HEADER_ERROR_CODE, $this->view->code, true);
	}

	protected function handleNotFoundException(Exception $ex)
	{
		$this->view->code	= Kaltura_AdminException::ERROR_CODE_PAGE_NOT_FOUND;
		$this->getResponse()->setHttpResponseCode(404);
		$this->getResponse()->setHeader(Kaltura_AdminException::KALTURA_HEADER_ERROR_CODE, $this->view->code, true);
	}

	protected function handleApplicationException(Exception $ex)
	{
		$this->view->code	= Kaltura_AdminException::getErrorCode($ex);
		$this->getResponse()->setHttpResponseCode(500);
		$this->getResponse()->setHeader(Kaltura_AdminException::KALTURA_HEADER_ERROR_CODE, $this->view->code, true);
	}
}