package com.kaltura.client;

import com.kaltura.client.KalturaLoggerLog4j;

abstract public class KalturaLogger 
{
	// Creation & retrieval methods:
	public static KalturaLogger getLogger(String name)
	{
		return KalturaLoggerLog4j.getLogger(name);
	}
	
	public static KalturaLogger getLogger(Class<?> clazz)
	{
		return getLogger(clazz.getName());
	}
	
	public boolean isEnabled()
	{
		return true;
	}

	// printing methods:
	abstract public void trace(Object message);
	abstract public void debug(Object message);
	abstract public void info(Object message);
	abstract public void warn(Object message);
	abstract public void error(Object message);
	abstract public void fatal(Object message);

	abstract public void trace(Object message, Throwable t);
	abstract public void debug(Object message, Throwable t);
	abstract public void info(Object message, Throwable t);
	abstract public void warn(Object message, Throwable t);
	abstract public void error(Object message, Throwable t);
	abstract public void fatal(Object message, Throwable t);
}
