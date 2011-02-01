<?php
class Kaltura_Paginator extends Zend_Paginator
{
	private static $index = 1;
	
	public $pageFieldName = 'page';
	private $request = array();
	private $action = '';
	
    /**
     * Constructor.
     *
     * @param Zend_Paginator_Adapter_Interface|Zend_Paginator_AdapterAggregate $adapter
     */
    public function __construct($adapter, $request = null, $pageFieldName = null)
    {
    	parent::__construct($adapter);
    	
    	if(!is_null($request))
    		$this->request = $request;
    	
    	if(!is_null($pageFieldName))
    		$this->pageFieldName = $pageFieldName;
    }
    
    public function setAction($action)
    {
    	$this->action = $action;
    }
    
    public static function setIndex($index)
    {
    	self::$index = $index;
    }
    
    /**
     * Renders the paginator.
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            $this->setView($view);
        }

        $view = $this->getView();

        $params = array(
        	'pageFieldName' => $this->pageFieldName,
        	'request' => $this->request,
        	'action' => $this->action,
        	'index' => self::$index,
        );
        self::$index ++;
        
        return $view->paginationControl($this, null, null, $params);
    }
    
    /**
     * @return int
     */
    public function getItemsCount()
    {
    	return $this->_adapter->getTotalCount();
    }
}