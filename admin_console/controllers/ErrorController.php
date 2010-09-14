<?php
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
    	$this->_helper->layout->disableLayout();
        $errors = $this->_getParam('error_handler');
        
        // handle kaltura session expired
        if (get_class($errors->exception) == 'KalturaException')
        {
        	if (strpos($errors->exception->getMessage(), 'EXPIRED'))
        	{
        		$session = new Zend_Session_Namespace();
        		$session->apiSession = null;
        		$this->_helper->redirector('login', 'user');
        	}
        }
        
        // handle Zend MVC errors
        switch ($errors->type) 
        { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->_helper->viewRenderer('not-found');
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
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
}