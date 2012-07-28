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
 * @package Scheduler
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentType
{
	const DOCUMENT = 11;
	const SWF = 12;
	const PDF = 13;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentEntryOrderBy
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const TOTAL_RANK_ASC = "+totalRank";
	const TOTAL_RANK_DESC = "-totalRank";
	const START_DATE_ASC = "+startDate";
	const START_DATE_DESC = "-startDate";
	const END_DATE_ASC = "+endDate";
	const END_DATE_DESC = "-endDate";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
	const RECENT_ASC = "+recent";
	const RECENT_DESC = "-recent";
	const WEIGHT_ASC = "+weight";
	const WEIGHT_DESC = "-weight";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParamsOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParamsOutputOrderBy
{
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentEntry extends KalturaBaseEntry
{
	/**
	 * The type of the document
	 * 	 
	 *
	 * @var KalturaDocumentType
	 * @insertonly
	 */
	public $documentType = null;

	/**
	 * Comma separated asset params ids that exists for this media entry
	 * 	 
	 *
	 * @var string
	 * @readonly
	 */
	public $assetParamsIds = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParams extends KalturaFlavorParams
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParams extends KalturaFlavorParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $densityWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $densityHeight = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeHeight = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $depth = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParams extends KalturaFlavorParams
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $readonly = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParams extends KalturaFlavorParams
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flashVersion = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $poly2Bitmap = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDocumentEntryBaseFilter extends KalturaBaseEntryFilter
{
	/**
	 * 
	 *
	 * @var KalturaDocumentType
	 */
	public $documentTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $documentTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdsMatchOr = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $assetParamsIdsMatchAnd = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParamsOutput extends KalturaFlavorParamsOutput
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParamsOutput extends KalturaFlavorParamsOutput
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $densityWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $densityHeight = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeWidth = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $sizeHeight = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $depth = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParamsOutput extends KalturaFlavorParamsOutput
{
	/**
	 * 
	 *
	 * @var bool
	 */
	public $readonly = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParamsOutput extends KalturaFlavorParamsOutput
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $flashVersion = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $poly2Bitmap = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentEntryFilter extends KalturaDocumentEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDocumentFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaImageFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPdfFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaSwfFlavorParamsBaseFilter extends KalturaFlavorParamsFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParamsFilter extends KalturaDocumentFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParamsFilter extends KalturaImageFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParamsFilter extends KalturaPdfFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParamsFilter extends KalturaSwfFlavorParamsBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDocumentFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaImageFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaPdfFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaSwfFlavorParamsOutputBaseFilter extends KalturaFlavorParamsOutputFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentFlavorParamsOutputFilter extends KalturaDocumentFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaImageFlavorParamsOutputFilter extends KalturaImageFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaPdfFlavorParamsOutputFilter extends KalturaPdfFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaSwfFlavorParamsOutputFilter extends KalturaSwfFlavorParamsOutputBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaDocumentClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaDocumentClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'document';
	}
}

