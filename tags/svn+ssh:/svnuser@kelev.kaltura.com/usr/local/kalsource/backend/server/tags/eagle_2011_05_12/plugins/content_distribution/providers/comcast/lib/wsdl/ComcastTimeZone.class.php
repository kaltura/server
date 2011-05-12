<?php


class ComcastTimeZone extends SoapObject
{				
	const _LINEISLANDS = 'LineIslands';
					
	const _WESTSAMOA = 'WestSamoa';
					
	const _HAWAII = 'Hawaii';
					
	const _ALASKA = 'Alaska';
					
	const _PACIFICUSCANADA = 'PacificUSCanada';
					
	const _ARIZONA = 'Arizona';
					
	const _MAZATLAN = 'Mazatlan';
					
	const _MOUNTAINUSCANADA = 'MountainUSCanada';
					
	const _CENTRALAMERICA = 'CentralAmerica';
					
	const _CENTRALUSCANADA = 'CentralUSCanada';
					
	const _MEXICOCITY = 'MexicoCity';
					
	const _SASKATCHEWAN = 'Saskatchewan';
					
	const _COLUMBIA = 'Columbia';
					
	const _EASTERNUSCANADA = 'EasternUSCanada';
					
	const _INDIANAEAST = 'IndianaEast';
					
	const _VENEZUELA = 'Venezuela';
					
	const _ATLANTICCANADA = 'AtlanticCanada';
					
	const _CHILE = 'Chile';
					
	const _NEWFOUNDLAND = 'Newfoundland';
					
	const _BRASILIA = 'Brasilia';
					
	const _ARGENTINE = 'Argentine';
					
	const _WESTERNGREENLAND = 'WesternGreenland';
					
	const _SOUTHGEORGIA = 'SouthGeorgia';
					
	const _AZORES = 'Azores';
					
	const _CAPEVERDE = 'CapeVerde';
					
	const _WESTERNEUROPEAN = 'WesternEuropean';
					
	const _GREENWICHMEAN = 'GreenwichMean';
					
	const _CENTRALEUROPEANBERLIN = 'CentralEuropeanBerlin';
					
	const _CENTRALEUROPEANPRAGUE = 'CentralEuropeanPrague';
					
	const _CENTRALEUROPEANPARIS = 'CentralEuropeanParis';
					
	const _CENTRALEUROPEANWARSAW = 'CentralEuropeanWarsaw';
					
	const _WESTAFRICAN = 'WestAfrican';
					
	const _EASTERNEUROPEANATHENS = 'EasternEuropeanAthens';
					
	const _EASTERNEUROPEANBUCHAREST = 'EasternEuropeanBucharest';
					
	const _EASTERNEUROPEANCAIRO = 'EasternEuropeanCairo';
					
	const _CENTRALAFRICAN = 'CentralAfrican';
					
	const _EASTERNEUROPEANHELSINKI = 'EasternEuropeanHelsinki';
					
	const _ISRAEL = 'Israel';
					
	const _ARABIABAGHDAD = 'ArabiaBaghdad';
					
	const _ARABIARIYADH = 'ArabiaRiyadh';
					
	const _MOSCOW = 'Moscow';
					
	const _EASTERNAFRICAN = 'EasternAfrican';
					
	const _IRAN = 'Iran';
					
	const _GULF = 'Gulf';
					
	const _AZERBAIJAN = 'Azerbaijan';
					
	const _AFGHANISTAN = 'Afghanistan';
					
	const _EKATERINBURG = 'Ekaterinburg';
					
	const _PAKISTAN = 'Pakistan';
					
	const _INDIA = 'India';
					
	const _NEPAL = 'Nepal';
					
	const _NOVOSIBIRSK = 'Novosibirsk';
					
	const _BANGLADESH = 'Bangladesh';
					
	const _SRILANKA = 'SriLanka';
					
	const _MYANMAR = 'Myanmar';
					
	const _INDOCHINA = 'Indochina';
					
	const _KRASNOYARSK = 'Krasnoyarsk';
					
	const _HONGKONG = 'HongKong';
					
	const _ULAANBATAAR = 'UlaanBataar';
					
	const _MALAYSIA = 'Malaysia';
					
	const _WESTERNAUSTRALIA = 'WesternAustralia';
					
	const _CHINA = 'China';
					
	const _JAPAN = 'Japan';
					
	const _KOREA = 'Korea';
					
	const _YAKUTSK = 'Yakutsk';
					
	const _CENTRALSOUTHAUSTRALIA = 'CentralSouthAustralia';
					
	const _CENTRALNORTHERNTERRITORYAUSTRALIA = 'CentralNorthernTerritoryAustralia';
					
	const _EASTERNQUEENSLANDAUSTRALIA = 'EasternQueenslandAustralia';
					
	const _EASTERNNEWSOUTHWALESAUSTRALIA = 'EasternNewSouthWalesAustralia';
					
	const _CHAMORRO = 'Chamorro';
					
	const _EASTERNHOBARTAUSTRALIA = 'EasternHobartAustralia';
					
	const _VLADIVOSTOK = 'Vladivostok';
					
	const _SOLOMONISLANDS = 'SolomonIslands';
					
	const _NEWZEALAND = 'NewZealand';
					
	const _FIJI = 'Fiji';
					
	const _TONGA = 'Tonga';
					
	const _KIRITIMATI = 'Kiritimati';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


