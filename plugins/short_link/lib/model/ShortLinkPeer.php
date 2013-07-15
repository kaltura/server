<?php


/**
 * Skeleton subclass for performing query and update operations on the 'short_link' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.shortLink
 * @subpackage model
 */
class ShortLinkPeer extends BaseShortLinkPeer {

	/**
	 * Retrieve all objects by kuser id
	 *
	 * @param      int $kuserId the kuser id.
	 * @param      PropelPDO $con the connection to use
	 * @return     array<ShortLink>
	 */
	public static function retrieveByKuserId($kuserId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(ShortLinkPeer::KUSER_ID, $kuserId);

		return ShortLinkPeer::doSelect($criteria, $con);
	}
	
} // ShortLinkPeer
