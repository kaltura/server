<?php

class KalturaLogFactory
{
	public static function getLogger(Zend_Config $config = null)
	{
		$logger = new Zend_Log();
		
		$hasWriters = false;
		if ($config !== null && $config->writers !== null)
		{
			foreach($config->writers as $writerConfig)
			{
				$writer = self::getWriter($writerConfig);
				if ($writer !== null)
				{
					$logger->addWriter($writer);
					$hasWriters = true;
				}
			}
		}
		
		if (!$hasWriters)
			$logger->addWriter(new Zend_Log_Writer_Null());  // to turn off logging without making the logger throw exceptions
			
		if ($config !== null && $config->eventItems !== null)
		{
			foreach($config->eventItems as $eventItemName => $eventItemClass)
			{
				$eventItemClass = (string)$eventItemClass;
				if (class_exists($eventItemClass))
					$logger->setEventItem($eventItemName, new $eventItemClass);
			}
		}
		
		
		return $logger;
	}
	
	private static function getWriter(Zend_Config $config)
	{
		if ($config->name === null)
			return null;
		
		$writer = null;
		switch($config->name)
		{
			case "Zend_Log_Writer_Stream":
				if ($config->stream === null)
					return null;
					
				if ($config->mode === null)
					$mode = 'a';
					
				$writer = new Zend_Log_Writer_Stream($config->stream, $mode);
				break;
		}
		
		if ($writer !== null && $config->formatters !== null)
		{
			foreach($config->formatters as $formatterConfig)
			{
				$formatter = self::getFormatter($formatterConfig);
				if ($formatter !== null)
					$writer->setFormatter($formatter);
			}
		}
		
		if ($writer !== null && $config->formatters !== null)
		{
			foreach($config->formatters as $formatterConfig)
			{
				$formatter = self::getFormatter($formatterConfig);
				if ($formatter !== null)
					$writer->setFormatter($formatter);
			}
		}
		
		if ($writer !== null && $config->filters !== null)
		{
			foreach($config->filters as $filterConfig)
			{
				$formatter = self::getFilter($filterConfig);
				if ($formatter !== null)
					$writer->addFilter($formatter);
			}
		}
		
		return $writer;
	}
	
	private static function getFormatter(Zend_Config $config)
	{
		if ($config === null)
			return null;
			
		if ($config->name === null)
			return null;
		
		$formatter = null;
		switch($config->name)
		{
			case "Zend_Log_Formatter_Simple":
				if ($config->format)
					$formatter = new Zend_Log_Formatter_Simple($config->format . PHP_EOL);
				else
					$formatter = new Zend_Log_Formatter_Simple();
				break;
		}
		
		return $formatter;
	}
	
	private static function getFilter(Zend_Config $config)
	{
		if ($config === null)
			return null;
			
		if ($config->name === null)
			return null;
			
		$filter = null;
		switch($config->name)
		{
			case "Zend_Log_Filter_Message":
				if ($config->regex !== null)
				{
					if (@preg_match($config->regex, '') !== false)
						$filter = new Zend_Log_Filter_Message($config->regex);
				}
				break;
			case "Zend_Log_Filter_Priority":
				if ($config->priority !== null)
				{
					if ($config->operator !== null)
						$filter = new Zend_Log_Filter_Priority((integer)$config->priority, $config->operator);
					else
						$filter = new Zend_Log_Filter_Priority((integer)$config->priority);
				}
				break;
			case "Zend_Log_Filter_Suppress":
				if ($config->suppress !== null)
				{
					$filter = new Zend_Log_Filter_Suppress();
					$filter->suppress($config->suppress);
				}
				break;
			case "KalturaLogPartnerFilter":
				if ($config->partnerId !== null)
				{
					$filter = new KalturaLogPartnerFilter($config->partnerId);
				}
				break;
		}
		return $filter;
	}
}