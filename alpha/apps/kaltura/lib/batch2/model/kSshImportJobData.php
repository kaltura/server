<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kSshImportJobData extends kImportJobData
{	
	/**
	 * @var string
	 */
	private $privateKey;
	
	/**
	 * @var string
	 */
	private $publicKey;
	
	/**
	 * @var string
	 */
	private $passPhrase;
	
	
	/**
     * @return the $privateKey
     */
    public function getPrivateKey ()
    {
        return $this->privateKey;
    }

	/**
     * @param string $privateKey
     */
    public function setPrivateKey ($privateKey)
    {
        $this->privateKey = $privateKey;
    }

	/**
     * @param string $publicKey
     */
    public function setPublicKey ($publicKey)
    {
        $this->publicKey = $publicKey;
    }
    
	/**
     * @return the $publicKey
     */
    public function getPublicKey ()
    {
        return $this->publicKey;
    }
    
	/**
     * @return the $passPhrase
     */
    public function getPassPhrase ()
    {
        return $this->passPhrase;
    }

	/**
     * @param string $passPhrase
     */
    public function setPassPhrase ($passPhrase)
    {
        $this->passPhrase = $passPhrase;
    }    
    
}
