<?php
/**
 * @package plugins.hostedReports
 */
class CategoryMediaReportAction extends KalturaApplicationPlugin
{
	const TOP_COUNT = 5;
	
	public function __construct()
	{
		$this->action = 'CategoryMediaReportAction';
		$this->label = 'Category Media';
		$this->rootLabel = 'Reports';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getRequiredPermissions()
	 */
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::USER_SESSION_PERMISSION);
	}

	private function getDefaultFromDate()
	{
		$ret = time() - 31*24*60*60;
		return date("m/d/Y", $ret);
		
	}
	
	private function getDefaultToDate()
	{
		$ret = time() - 2*24*60*60;
		return date("m/d/Y", $ret);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::doAction()
	 */
	public function doAction(Zend_Controller_Action $action)
	{
		$request = $action->getRequest();
		$action->view->errMessage = null;
		
		$categoryId = $request->getParam('categoryId');
		if(!$categoryId)
		{
			$action->view->errMessage = 'category-media-report category not supplied';
			return;
		}
		
		$client = Infra_ClientHelper::getClient();
		try
		{
			$category = $client->category->get($categoryId);
		}
		catch (Kaltura_Client_Exception $ke)
		{
			$action->view->errMessage = $ke->getMessage();
		}
		catch (Kaltura_Client_ClientException $kce)
		{
			$action->view->errMessage = $kce->getMessage();
		}
		catch (Exception $e)
		{
			$action->view->errMessage = 'category-media-report category not found';
		}
		
		if(!$category || !($category instanceof Kaltura_Client_Type_Category))
			return;
			
		$action->view->category = $category;
		$action->view->filterForm = new DateRangeFilter();
		$action->view->playedEntriesCount = 0;
		$action->view->entriesPlaysCount = 0;
		$action->view->maxPlaysEntry = null;
		$action->view->top = null;
		
		
		$filter = new Kaltura_Client_Type_ReportInputFilter();
		
		$from = $request->getParam('from_date', $this->getDefaultFromDate());
		$to = $request->getParam('to_date', $this->getDefaultToDate());
		
		$filter->categories = $category->fullName;
		$filter->fromDate = DateTime::createFromFormat('m/d/Y', $from)->getTimestamp();
		$filter->toDate = DateTime::createFromFormat('m/d/Y', $to)->getTimestamp();
		$filter->timeZoneOffset = Infra_AuthHelper::getAuthInstance()->getIdentity()->getTimezoneOffset();
		
		$total = $client->report->getTotal(Kaltura_Client_Enum_ReportType::TOP_CONTENT, $filter);
		/* @var $total Kaltura_Client_Type_ReportTotal */
		$totalData = array_combine(explode(',', $total->header), explode(',', $total->data));
		
		// count_plays,sum_time_viewed,avg_time_viewed,count_loads,load_play_ratio,avg_view_drop_off
		$action->view->entriesPlaysCount = $totalData['count_plays'];
		
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageSize= self::TOP_COUNT;
		$pager->page = 1;
		$table = $client->report->getTable(Kaltura_Client_Enum_ReportType::TOP_CONTENT, $filter, $pager);
		/* @var $table Kaltura_Client_Type_ReportTable */
		
		if($table->totalCount)
		{
			$action->view->playedEntriesCount = $table->totalCount;
			
			//object_id,entry_name,count_plays,sum_time_viewed,avg_time_viewed,count_loads,load_play_ratio,avg_view_drop_off
			$tableTopData = explode(';', $table->data);
			
			$top = array();
			foreach($tableTopData as $tableData)
			{
				$top[] = array_combine(explode(',', $table->header), explode(',', $tableData));
			}
			
			$action->view->maxPlaysEntry = reset($top);
			$action->view->top = $top;
		}
	}
}

