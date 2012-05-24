<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Plugin extends Kaltura_Client_Plugin
{
	/**
	 * @var Kaltura_Client_ContentDistribution_Plugin
	 */
	protected static $instance;

	/**
	 * @var Kaltura_Client_ContentDistribution_DistributionProfileService
	 */
	public $distributionProfile = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_EntryDistributionService
	 */
	public $entryDistribution = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_DistributionProviderService
	 */
	public $distributionProvider = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_GenericDistributionProviderService
	 */
	public $genericDistributionProvider = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_GenericDistributionProviderActionService
	 */
	public $genericDistributionProviderAction = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_ContentDistributionBatchService
	 */
	public $contentDistributionBatch = null;

	/**
	 * @var Kaltura_Client_ContentDistribution_TvComService
	 */
	public $tvCom = null;

	protected function __construct(Kaltura_Client_Client $client)
	{
		parent::__construct($client);
		$this->distributionProfile = new Kaltura_Client_ContentDistribution_DistributionProfileService($client);
		$this->entryDistribution = new Kaltura_Client_ContentDistribution_EntryDistributionService($client);
		$this->distributionProvider = new Kaltura_Client_ContentDistribution_DistributionProviderService($client);
		$this->genericDistributionProvider = new Kaltura_Client_ContentDistribution_GenericDistributionProviderService($client);
		$this->genericDistributionProviderAction = new Kaltura_Client_ContentDistribution_GenericDistributionProviderActionService($client);
		$this->contentDistributionBatch = new Kaltura_Client_ContentDistribution_ContentDistributionBatchService($client);
		$this->tvCom = new Kaltura_Client_ContentDistribution_TvComService($client);
	}

	/**
	 * @return Kaltura_Client_ContentDistribution_Plugin
	 */
	public static function get(Kaltura_Client_Client $client)
	{
		if(!self::$instance)
			self::$instance = new Kaltura_Client_ContentDistribution_Plugin($client);
		return self::$instance;
	}

	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices()
	{
		$services = array(
			'distributionProfile' => $this->distributionProfile,
			'entryDistribution' => $this->entryDistribution,
			'distributionProvider' => $this->distributionProvider,
			'genericDistributionProvider' => $this->genericDistributionProvider,
			'genericDistributionProviderAction' => $this->genericDistributionProviderAction,
			'contentDistributionBatch' => $this->contentDistributionBatch,
			'tvCom' => $this->tvCom,
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'contentDistribution';
	}
}

