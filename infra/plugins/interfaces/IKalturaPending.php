<?php
interface IKalturaPending extends IKalturaBase
{
	/**
	 * @return array<KalturaDependency>
	 */
	public static function dependsOn();
}