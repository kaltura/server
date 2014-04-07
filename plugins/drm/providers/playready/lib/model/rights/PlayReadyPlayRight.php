<?php
/**
* @package plugins.drm
* @subpackage model
*/
class PlayReadyPlayRight extends PlayReadyRight
{	
	/**
	 * Minimum Analog Television Output Protection Level:	100, 150, 200
	 * @var PlayReadyAnalogVideoOPL
	 */
	private $analogVideoOPL ;
	
	/**
	 * Explicit Analog Video Output Protection 
	 * Video Output Protection ID Field			| Binary Configuration Data Field	| Output Protection Description
	 * --------------------------------------------------------------------------------------------------------------------
	 * {C3FD11C6-F8B7-4D20-B008-1DB17D61F2DA}	| 0, 1, 2, 3						| AGC and Color Stripe
	 * {2098DE8D-7DDD-4BAB-96C6-32EBB6FABEA3}	| 0, 1, 2, 3						| Explicit Analog Television Output Restriction
	 * {225CD36F-F132-49EF-BA8C-C91EA28E4369}	| 0, 1, 2, 3						| Best Effort Explicit Analog Television Output Restriction
	 * {811C5110-46C8-4C6E-8163- C0482A15D47E}	| 520000							| Image constraint for Analog Component Video Output
	 * {D783A191-E083-4BAF-B2DA-E69F910B3772}	| 520000							| Image constraint for Analog Computer Monitor Output 
	 * 
	 * @var array of PlayReadyAnalogVideoOPId
	 */
	private $analogVideoOutputProtectionList ;
	
    /**
     * Minimum Compressed Digital Audio Output Protection Level:	100, 150, 200, 250, 300
	 * @var PlayReadyDigitalAudioOPL
	 */
	private $compressedDigitalAudioOPL ;
	
    /**
     * Minimum Compressed Digital Video Output Protection Level:	400, 500
	 * @var PlayReadyCompressedDigitalVideoOPL
	 */
	private $compressedDigitalVideoOPL ;

	/**
	 * Audio Output Protection ID Field			| Binary Configuration Data Field		| Output Protection Description
	 * --------------------------------------------------------------------------------------------------------------
	 * {6D5CFA59-C250-4426-930E-FAC72C8FCFA6}	| 00, 01, 10, 11						| SCMS. See Table 3.5.2.8 (SCMS Control Bits)
	 *
	 * @var array of PlayReadyDigitalAudioOPId
	 */
	private $digitalAudioOutputProtectionList; 
	
	/**
	 * Minimum Uncompressed Digital Audio Output Protection Level:	100, 150, 200, 250, 300
	 * @var PlayReadyDigitalAudioOPL
	 */	
	private $uncompressedDigitalAudioOPL;

    /**
     * Minimum Uncompressed Digital Video Output Protection Level:	100, 250, 270, 300
	 * @var PlayReadyUncompressedDigitalVideoOPL
	 */
	private $uncompressedDigitalVideoOPL; 
	
    /**
	 * @var int configured in hours
	 */
	private $firstPlayExpiration;
	
    /**
	 * @var array of PlayReadyPlayEnablerType
	 */
	private $playEnablers;
	
	/**
	 * @return the $analogVideoOPL
	 */
	public function getAnalogVideoOPL() {
		return $this->analogVideoOPL;
	}

	/**
	 * @return the $analogVideoOutputProtectionList
	 */
	public function getAnalogVideoOutputProtectionList() {
		return $this->analogVideoOutputProtectionList;
	}

	/**
	 * @return the $compressedDigitalAudioOPL
	 */
	public function getCompressedDigitalAudioOPL() {
		return $this->compressedDigitalAudioOPL;
	}

	/**
	 * @return the $compressedDigitalVideoOPL
	 */
	public function getCompressedDigitalVideoOPL() {
		return $this->compressedDigitalVideoOPL;
	}

	/**
	 * @return the $digitalAudioOutputProtectionList
	 */
	public function getDigitalAudioOutputProtectionList() {
		return $this->digitalAudioOutputProtectionList;
	}

	/**
	 * @return the $uncompressedDigitalAudioOPL
	 */
	public function getUncompressedDigitalAudioOPL() {
		return $this->uncompressedDigitalAudioOPL;
	}

	/**
	 * @return the $uncompressedDigitalVideoOPL
	 */
	public function getUncompressedDigitalVideoOPL() {
		return $this->uncompressedDigitalVideoOPL;
	}

	/**
	 * @return the $firstPlayExpiration
	 */
	public function getFirstPlayExpiration() {
		return $this->firstPlayExpiration;
	}

	/**
	 * @return the $playEnablers
	 */
	public function getPlayEnablers() {
		return $this->playEnablers;
	}

	/**
	 * @param int $analogVideoOPL
	 */
	public function setAnalogVideoOPL($analogVideoOPL) {
		$this->analogVideoOPL = $analogVideoOPL;
	}

	/**
	 * @param array $analogVideoOutputProtectionList
	 */
	public function setAnalogVideoOutputProtectionList($analogVideoOutputProtectionList) {
		$this->analogVideoOutputProtectionList = $analogVideoOutputProtectionList;
	}

	/**
	 * @param int $compressedDigitalAudioOPL
	 */
	public function setCompressedDigitalAudioOPL($compressedDigitalAudioOPL) {
		$this->compressedDigitalAudioOPL = $compressedDigitalAudioOPL;
	}

	/**
	 * @param int $compressedDigitalVideoOPL
	 */
	public function setCompressedDigitalVideoOPL($compressedDigitalVideoOPL) {
		$this->compressedDigitalVideoOPL = $compressedDigitalVideoOPL;
	}

	/**
	 * @param array $digitalAudioOutputProtectionList
	 */
	public function setDigitalAudioOutputProtectionList($digitalAudioOutputProtectionList) {
		$this->digitalAudioOutputProtectionList = $digitalAudioOutputProtectionList;
	}

	/**
	 * @param int $uncompressedDigitalAudioOPL
	 */
	public function setUncompressedDigitalAudioOPL($uncompressedDigitalAudioOPL) {
		$this->uncompressedDigitalAudioOPL = $uncompressedDigitalAudioOPL;
	}

	/**
	 * @param int $uncompressedDigitalVideoOPL
	 */
	public function setUncompressedDigitalVideoOPL($uncompressedDigitalVideoOPL) {
		$this->uncompressedDigitalVideoOPL = $uncompressedDigitalVideoOPL;
	}

	/**
	 * @param int $firstPlayExpiration
	 */
	public function setFirstPlayExpiration($firstPlayExpiration) {
		$this->firstPlayExpiration = $firstPlayExpiration;
	}

	/**
	 * @param array $playEnablers
	 */
	public function setPlayEnablers($playEnablers) {
		$this->playEnablers = $playEnablers;
	}
}