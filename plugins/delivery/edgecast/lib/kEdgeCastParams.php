<?php

class kEdgeCastParams
{
	private $accountNumber;
	private $apiToken;
	
	
	/**
     * @return the $accountNumber
     */
    public function getAccountNumber ()
    {
        return $this->accountNumber;
    }

	/**
     * @return the $apiToken
     */
    public function getApiToken ()
    {
        return $this->apiToken;
    }

	/**
     * @param field_type $accountNumber
     */
    public function setAccountNumber ($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

	/**
     * @param field_type $apiToken
     */
    public function setApiToken ($apiToken)
    {
        $this->apiToken = $apiToken;
    }	
	
}