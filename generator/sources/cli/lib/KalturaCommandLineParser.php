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

class KalturaCommandLineParser
{
	// command line parsing functions
	const SWITCH_NO_VALUE = 0;
	const SWITCH_REQUIRES_VALUE = 1;

	public static function parseArguments(array $commandLineSwitches)
	{
		// build getopt arguments
		$shortSwitches = '';
		$longSwitches = array();	
		foreach ($commandLineSwitches as $commandLineSwitch)
		{
			$shortName = $commandLineSwitch[1];
			$longName = $commandLineSwitch[2];
			switch ($commandLineSwitch[0])
			{
			case self::SWITCH_NO_VALUE:
				$shortSwitches .= $shortName;
				$longSwitches[] = $longName;
				break;

			case self::SWITCH_REQUIRES_VALUE:
				$shortSwitches .= $shortName . ':';
				$longSwitches[] = $longName . ':';
				break;
			}
		}
		$options = getopt($shortSwitches, $longSwitches);
		
		// normalize short parameters to long parameters
		foreach ($commandLineSwitches as $commandLineSwitch)
		{
			$shortName = $commandLineSwitch[1];
			if (!isset($options[$shortName]))
				continue;

			$longName = $commandLineSwitch[2];
			$options[$longName] = $options[$shortName];
		}
		
		return $options;
	}

	public static function getArgumentsUsage(array $commandLineSwitches)
	{
		$result = '';
		foreach ($commandLineSwitches as $commandLineSwitch)
		{
			$shortName = $commandLineSwitch[1];
			$longName = $commandLineSwitch[2];
			$description = $commandLineSwitch[3];
			$curLine = " -{$shortName}/--{$longName}";
			if ($commandLineSwitch[0] == self::SWITCH_REQUIRES_VALUE)
				$curLine .= " <{$longName}>";
			$curLine = str_pad($curLine, 19);
			$result .= "{$curLine} {$description}\n";
		}
		return $result;
	}

	public static function stripCommandLineSwitches(array $commandLineSwitches, array $inputArgs)
	{
		$requireValueArgs = array();
		foreach ($commandLineSwitches as $commandLineSwitch)
		{
			if ($commandLineSwitch[0] != self::SWITCH_REQUIRES_VALUE)
				continue;

			$requireValueArgs[] = '-' . $commandLineSwitch[1];
			$requireValueArgs[] = '--' . $commandLineSwitch[2];
		}

		$arguments = array();
		$inputArgCount = count($inputArgs);
		for ($curIndex = 1; $curIndex < $inputArgCount; $curIndex++)
		{
			$curArg = $inputArgs[$curIndex];
			if (strlen($curArg) < 2 || $curArg[0] != '-' || is_numeric($curArg[1]))
			{
				$arguments[] = $curArg;
				continue;
			}
			
			if (in_array($curArg, $requireValueArgs))
				$curIndex++;		// this arg accepts values, and the values weren't defined within the arg (e.g. -ukaltura.com)
		}
		return $arguments;
	}
	
	public static function getAllCommandLineSwitches(array $commandLineSwitches)
	{
		$result = array();
		foreach ($commandLineSwitches as $commandLineSwitch)
		{
			$result[] = '-' . $commandLineSwitch[1];
			$result[] = '--' . $commandLineSwitch[2];
		}
		return $result;
	}
}
