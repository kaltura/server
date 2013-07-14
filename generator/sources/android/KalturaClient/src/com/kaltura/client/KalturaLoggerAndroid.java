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
package com.kaltura.client;

import android.util.Log;

public class KalturaLoggerAndroid extends KalturaLogger
{
	protected String tag;

	// Creation & retrieval methods:
	public static KalturaLogger getLogger(String name)
	{
		return new KalturaLoggerAndroid(name);
	}
	
	protected KalturaLoggerAndroid(String name)
	{
		this.tag = name;
	}

	// printing methods:
	public void trace(Object message)
	{
		Log.v(this.tag, message.toString());
	}
	
	public void debug(Object message)
	{
		Log.d(this.tag, message.toString());
	}
	
	public void info(Object message)
	{
		Log.i(this.tag, message.toString());
	}
	
	public void warn(Object message)
	{
		Log.w(this.tag, message.toString());
	}
	
	public void error(Object message)
	{
		Log.e(this.tag, message.toString());
	}
	
	public void fatal(Object message)
	{
		Log.wtf(this.tag, message.toString());
	}

	public void trace(Object message, Throwable t)
	{
		Log.v(this.tag, message.toString(), t);
	}
	
	public void debug(Object message, Throwable t)
	{
		Log.d(this.tag, message.toString(), t);
	}
	
	public void info(Object message, Throwable t)
	{
		Log.i(this.tag, message.toString(), t);
	}
	
	public void warn(Object message, Throwable t)
	{
		Log.w(this.tag, message.toString(), t);
	}
	
	public void error(Object message, Throwable t)
	{
		Log.e(this.tag, message.toString(), t);
	}
	
	public void fatal(Object message, Throwable t)
	{
		Log.wtf(this.tag, message.toString(), t);
	}
}
