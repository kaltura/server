<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiListOfTablesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleTableResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetColumnInfoForSingleTableResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetRowDataForSingleTableResponse.php');
	

class ActivitiDatabaseTablesService extends ActivitiService
{
	
	/**
	 * List of tables
	 * 
	 * @return array<ActivitiListOfTablesResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15CF7 List of tables}
	 */
	public function listOfTables()
	{
		$data = array();
		
		return $this->client->request("management/tables", 'GET', $data, array(200), array(), 'ActivitiListOfTablesResponse', true);
	}
	
	/**
	 * Get a single table
	 * 
	 * @return ActivitiGetSingleTableResponse
	 * @see {@link http://www.activiti.org/userguide/#N15D1C Get a single table}
	 */
	public function getSingleTable($tableName)
	{
		$data = array();
		
		return $this->client->request("management/tables/$tableName", 'GET', $data, array(200), array(404 => "Indicates the requested table does not exist."), 'ActivitiGetSingleTableResponse');
	}
	
	/**
	 * Get column info for a single table
	 * 
	 * @return ActivitiGetColumnInfoForSingleTableResponse
	 * @see {@link http://www.activiti.org/userguide/#N15D60 Get column info for a single table}
	 */
	public function getColumnInfoForSingleTable($tableName)
	{
		$data = array();
		
		return $this->client->request("management/tables/$tableName/columns", 'GET', $data, array(200), array(404 => "Indicates the requested table does not exist."), 'ActivitiGetColumnInfoForSingleTableResponse');
	}
	
	/**
	 * Get row data for a single table
	 * 
	 * @return ActivitiGetRowDataForSingleTableResponse
	 * @see {@link http://www.activiti.org/userguide/#N15DA4 Get row data for a single table}
	 */
	public function getRowDataForSingleTable($tableName)
	{
		$data = array();
		
		return $this->client->request("management/tables/$tableName/data", 'GET', $data, array(200), array(404 => "Indicates the requested table does not exist."), 'ActivitiGetRowDataForSingleTableResponse');
	}
	
}

