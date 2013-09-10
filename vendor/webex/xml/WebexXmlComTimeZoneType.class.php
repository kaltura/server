<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComTimeZoneType extends WebexXmlRequestType
{
	const _GMT_12_00_DATELINE_CENIWETOKD = 'GMT-12:00, Dateline CEniwetokD';
					
	const _GMT_11_00_SAMOA_CSAMOAD = 'GMT-11:00, Samoa CSamoaD';
					
	const _GMT_10_00_HAWAII_CHONOLULUD = 'GMT-10:00, Hawaii CHonoluluD';
					
	const _GMT_09_00_ALASKA_CANCORAGED = 'GMT-09:00, Alaska CAncorageD';
					
	const _GMT_08_00_PACIFIC_CSAN_JOSED = 'GMT-08:00, Pacific CSan JoseD';
					
	const _GMT_07_00_MOUNTAIN_CARIZONAD = 'GMT-07:00, Mountain CArizonaD';
					
	const _GMT_07_00_MOUNTAIN_CDENVERD = 'GMT-07:00, Mountain CDenverD';
					
	const _GMT_06_00_CENTRAL_CCHICAGOD = 'GMT-06:00, Central CChicagoD';
					
	const _GMT_06_00_MEXICO_CMEXICO_CITY_TEGUCIGALPAD = 'GMT-06:00, Mexico CMexico City,TegucigalpaD';
					
	const _GMT_06_00_CENTRAL_CREGINAD = 'GMT-06:00, Central CReginaD';
					
	const _GMT_05_00_S__AMERICA_PACIFIC_CBOGOTAD = 'GMT-05:00, S. America Pacific CBogotaD';
					
	const _GMT_05_00_EASTERN_CNEW_YORKD = 'GMT-05:00, Eastern CNew YorkD';
					
	const _GMT_05_00_EASTERN_CINDIANAD = 'GMT-05:00, Eastern CIndianaD';
					
	const _GMT_04_00_ATLANTIC_CHALIFAXD = 'GMT-04:00, Atlantic CHalifaxD';
					
	const _GMT_04_00_S__AMERICA_WESTERN_CCARACASD = 'GMT-04:00, S. America Western CCaracasD';
					
	const _GMT_03_30_NEWFOUNDLAND_CNEWFOUNDLANDD = 'GMT-03:30, Newfoundland CNewfoundlandD';
					
	const _GMT_03_00_S__AMERICA_EASTERN_CBRASILIAD = 'GMT-03:00, S. America Eastern CBrasiliaD';
					
	const _GMT_03_00_S__AMERICA_EASTERN_CBUENOS_AIRESD = 'GMT-03:00, S. America Eastern CBuenos AiresD';
					
	const _GMT_02_00_MID_ATLANTIC_CMID_ATLANTICD = 'GMT-02:00, Mid-Atlantic CMid-AtlanticD';
					
	const _GMT_01_00_AZORES_CAZORESD = 'GMT-01:00, Azores CAzoresD';
					
	const _GMT_00_00_GREENWICH_CCASABLANCAD = 'GMT+00:00, Greenwich CCasablancaD';
					
	const _GMT_00_00_GMT_CLONDOND = 'GMT+00:00, GMT CLondonD';
					
	const _GMT_01_00_EUROPE_CAMSTERDAMD = 'GMT+01:00, Europe CAmsterdamD';
					
	const _GMT_01_00_EUROPE_CPARISD = 'GMT+01:00, Europe CParisD';
					
	const _GMT_01_00_EUROPE_CPRAGUED = 'GMT+01:00, Europe CPragueD';
					
	const _GMT_01_00_EUROPE_CBERLIND = 'GMT+01:00, Europe CBerlinD';
					
	const _GMT_02_00_GREECE_CATHENSD = 'GMT+02:00, Greece CAthensD';
					
	const _GMT_02_00_EASTERN_EUROPE_CBUCHARESTD = 'GMT+02:00, Eastern Europe CBucharestD';
					
	const _GMT_02_00_EGYPT_CCAIROD = 'GMT+02:00, Egypt CCairoD';
					
	const _GMT_02_00_SOUTH_AFRICA_CPRETORIAD = 'GMT+02:00, South Africa CPretoriaD';
					
	const _GMT_02_00_NORTHERN_EUROPE_CHELSINKID = 'GMT+02:00, Northern Europe CHelsinkiD';
					
	const _GMT_02_00_ISRAEL_CTEL_AVIVD = 'GMT+02:00, Israel CTel AvivD';
					
	const _GMT_03_00_SAUDI_ARABIA_CBAGHDADD = 'GMT+03:00, Saudi Arabia CBaghdadD';
					
	const _GMT_03_00_RUSSIAN_CMOSCOWD = 'GMT+03:00, Russian CMoscowD';
					
	const _GMT_03_00_NAIROBI_CNAIROBID = 'GMT+03:00, Nairobi CNairobiD';
					
	const _GMT_03_00_IRAN_CTEHRAND = 'GMT+03:00, Iran CTehranD';
					
	const _GMT_04_00_ARABIAN_CABU_DHABI_MUSCATD = 'GMT+04:00, Arabian CAbu Dhabi, MuscatD';
					
	const _GMT_04_00_BAKU_CBAKUD = 'GMT+04:00, Baku CBakuD';
					
	const _GMT_04_00_AFGHANISTAN_CKABULD = 'GMT+04:00, Afghanistan CKabulD';
					
	const _GMT_05_00_WEST_ASIA_CEKATERINBURGD = 'GMT+05:00, West Asia CEkaterinburgD';
					
	const _GMT_05_00_WEST_ASIA_CISLAMABADD = 'GMT+05:00, West Asia CIslamabadD';
					
	const _GMT_05_30_INDIA_CBOMBAYD = 'GMT+05:30, India CBombayD';
					
	const _GMT_06_00_COLUMBO_CCOLUMBOD = 'GMT+06:00, Columbo CColumboD';
					
	const _GMT_06_00_CENTRAL_ASIA_CALMATYD = 'GMT+06:00, Central Asia CAlmatyD';
					
	const _GMT_07_00_BANGKOK_CBANGKOKD = 'GMT+07:00, Bangkok CBangkokD';
					
	const _GMT_08_00_CHINA_CBEIJINGD = 'GMT+08:00, China CBeijingD';
					
	const _GMT_08_00_AUSTRALIA_WESTERN_CPERTHD = 'GMT+08:00, Australia Western CPerthD';
					
	const _GMT_08_00_SINGAPORE_CSINGAPORED = 'GMT+08:00, Singapore CSingaporeD';
					
	const _GMT_08_00_TAIPEI_CHONG_KONGD = 'GMT+08:00, Taipei CHong KongD';
					
	const _GMT_09_00_TOKYO_CTOKYOD = 'GMT+09:00, Tokyo CTokyoD';
					
	const _GMT_09_00_KOREA_CSEOULD = 'GMT+09:00, Korea CSeoulD';
					
	const _GMT_09_30_YAKUTSK_CYAKUTSKD = 'GMT+09:30, Yakutsk CYakutskD';
					
	const _GMT_09_30_AUSTRALIA_CENTRAL_CADELAIDED = 'GMT+09:30, Australia Central CAdelaideD';
					
	const _GMT_09_30_AUSTRALIA_CENTRAL_CDARWIND = 'GMT+09:30, Australia Central CDarwinD';
					
	const _GMT_10_00_AUSTRALIA_EASTERN_CBRISBANED = 'GMT+10:00, Australia Eastern CBrisbaneD';
					
	const _GMT_10_00_AUSTRALIA_EASTERN_CSYDNEYD = 'GMT+10:00, Australia Eastern CSydneyD';
					
	const _GMT_10_00_WEST_PACIFIC_CGUAMD = 'GMT+10:00, West Pacific CGuamD';
					
	const _GMT_10_00_TASMANIA_CHOBARTD = 'GMT+10:00, Tasmania CHobartD';
					
	const _GMT_10_00_VLADIVOSTOK_CVLADIVOSTOKD = 'GMT+10:00, Vladivostok CVladivostokD';
					
	const _GMT_11_00_CENTRAL_PACIFIC_CSOLOMON_ISD = 'GMT+11:00, Central Pacific CSolomon IsD';
					
	const _GMT_12_00_NEW_ZEALAND_CWELLINGTOND = 'GMT+12:00, New Zealand CWellingtonD';
					
	const _GMT_12_00_FIJI_CFIJID = 'GMT+12:00, Fiji CFijiD';
					
	const _GMT_09_00_ALASKA_CANCHORAGED = 'GMT-09:00, Alaska CAnchorageD';
					
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'timeZoneType';
	}
	
}
		
