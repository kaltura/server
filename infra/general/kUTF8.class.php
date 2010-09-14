<?php

/**
 * kUTF8 -
 * manipulate UTF8 strings
 */

class kUTF8
{
	/* 
	 * the following script creates the kUTF8Codes.php file which contains all of the UTF8 characters and their lexical order
	 * the file is used by the str2int64 function which creates a numeric representation of the first 4 UTF8 chars of a give string
	 
	#!/bin/bash
	
	# CREATING THE SORTED CHARS PHP FILE
	
	# download char tables
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=0
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=1024
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=2048
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=3072
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=4096
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=5120
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=6144
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=7168
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=8192
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=9216
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=10240
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=11264
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=12288
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=13312
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=14336
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=15360
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=16384
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=17408
	wget http://www.fileformat.info/info/charset/UTF-8/list.htm?start=18432
	
	# grab chars rows from tables
	grep -A1 "/info/unicode/char/" list.htm\?start\=* |grep -v unicode|grep td > tmp_chars
	
	# grab char code from rows
	cat tmp_chars|awk -F\> '{print $2}'|awk -F\< '{print $1}' >tmp_charshex
	
	# create script to insert chars to database
	cat tmp_charshex|awk {'print "insert into chartest values(char(0x"$1" using utf8));"}' >tmp_insert
	
	# create database and table
	mysql -pXXXX --default-character-set=utf8 -e "drop database if exists chartestdb;"
	mysql -pXXXX --default-character-set=utf8 -e "create database chartestdb;"
	mysql -pXXXX chartestdb --default-character-set=utf8 -e "drop table if exists chartest;"
	mysql -pXXXX chartestdb --default-character-set=utf8 -e "create table chartest (c char(1)) character set UTF8;"
	
	# insert into database
	mysql -pXXXX chartestdb --default-character-set=utf8 < tmp_insert
	
	# select distinct characters and make sure regular letters (with lower and capital case) are ordered together
	mysql -pXXXX chartestdb --default-character-set=utf8 -N -e "select distinct ord(c) from chartest order by upper(c),ord(c)" >tmp_chartest
	
	# create script to find similar
	cat tmp_chartest|awk 'BEGIN {s=1} {print "select "$1", strcmp(char("$1" using utf8), char("s" using utf8))=0;" ; s= $1;}' >tmp_findlike
	
	# create a list of chars with where similar ones have 1 and different have 0 in the seconds column
	mysql -pXXXX chartestdb --default-character-set=utf8 --silent <tmp_findlike >tmp_charlike

	# drop database
	mysql -pXXXX --default-character-set=utf8 -e "drop database if exists chartestdb;"
	
	# add the space char (code 32) which mysql dismisses
	awk '$1==33 {print "32 0"} {print $0}' tmp_charlike > tmp_charlike_space
	
	# create a php file where every character code is assigned its lexical order
	# a char is either the same as its predecessor or next in order according to previous strcmp check
	awk 'BEGIN {print "<?"} s[$1] != 1 {x=x+1-$2; print "$kUTF8Codes["$1"]="x";"} {s[$1]=1} ' tmp_charlike_space >kUTF8Codes.php

	# remove temp files 
	rm -f list.htm* chars tmp_*


	 */
	
	/**
	 * returns a string containing the integer representation of the first four character of a UTF8 string
	 * used for ordering UTF8 strings by their numeric representation
	 *
	 * @param string $s
	 * @return string
	 */
	public static function str2int64 ( $s )
	{
		global $kUTF8Codes;
		require_once("kUTF8Codes.php");

		// get 4 letters prefix (add trailing spaces chars in case we are missing characters)
		$s = iconv_substr($s."    ", 0, 4, "UTF-8");

		$ords = array(4);

		for($i = 0; $i < 4; ++$i)
		{
			$c = iconv_substr($s, $i, 1, "UTF-8");
			$l = strlen($c);
			$c = substr("\0\0\0\0", 0, 4 - $l).$c;
			$o = unpack("N", $c);
			$ords[$i] = $kUTF8Codes[$o[1]];
		}

		if(function_exists('gmp_init'))
		{
			$res = gmp_init(0);
			for($i = 0; $i < 4; $i++)
			{
				$res = gmp_mul($res, 1 << 16);
				$res = gmp_add($res, $ords[$i]);
			}
			return gmp_strval($res);
		}
		elseif(function_exists('bcmul'))
		{
			$res = 0;
			for($i = 0; $i < 4; $i++)
			{
				$res = bcmul($res, (1 << 16));
				$res = bcadd($res, $ords[$i]);
			}
			return $res;
		}
		return 0;
	}
}
