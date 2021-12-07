<?php

namespace Oracle\Oci\Common;

use InvalidArgumentException;

class Realm
{
    /*string*/ protected $realmId;
    /*string*/ protected $realmDomainComponent;
    /*Region[]*/ protected $regionsInRealm = [];

    protected static $wasInitialized = false;
    protected static $knownRealms = [];
    protected static $unknownRegionRealm;

    private static $OC1;
    private static $OC2;
    private static $OC3;
    private static $OC4;
    private static $OC8;

    public function __construct(
        /*string*/
        $realmId,
        /*string*/
        $realmDomainComponent
    ) {
        $this->realmId = strtolower($realmId);
        $this->realmDomainComponent = $realmDomainComponent;
        Realm::$knownRealms[$realmId] = $this;
    }

    public static function __init()
    {
        if (!Realm::$wasInitialized) {
            Realm::$OC1 = new Realm("oc1", "oraclecloud.com");
            Realm::$OC2 = new Realm("oc2", "oraclegovcloud.com");
            Realm::$OC3 = new Realm("oc3", "oraclegovcloud.com");
            Realm::$OC4 = new Realm("oc4", "oraclegovcloud.uk");
            Realm::$OC8 = new Realm("oc8", "oraclecloud8.com");
            // don't use Realm::OC1() here yet, $wasInitialized is not true yet
            Realm::$unknownRegionRealm = Realm::$OC1;
            Realm::$wasInitialized = true;
        }
    }

    public static function OC1() // : Realm
    {
        Realm::__init();
        return Realm::$OC1;
    }

    public static function OC2() // : Realm
    {
        Realm::__init();
        return Realm::$OC2;
    }

    public static function OC3() // : Realm
    {
        Realm::__init();
        return Realm::$OC3;
    }

    public static function OC4() // : Realm
    {
        Realm::__init();
        return Realm::$OC4;
    }

    public static function OC8() // : Realm
    {
        Realm::__init();
        return Realm::$OC8;
    }

    public function getRealmId() // : string
    {
        Realm::__init();
        return $this->realmId;
    }

    public function getRealmDomainComponent() // : string
    {
        return $this->realmDomainComponent;
    }

    public static function getRealm(
        /*string*/
        $realmId
    ) // : ?Realm
    {
        Realm::__init();
        $id = strtolower($realmId);
        if (array_key_exists($id, Realm::$knownRealms)) {
            return Realm::$knownRealms[$id];
        }
        return null;
    }

    public static function getRealmForUnknownRegion() // : Realm
    {
        Realm::__init();
        return Realm::$unknownRegionRealm;
    }

    public function addRegionToRealm(Region $region)
    {
        if ($region->getRealm()->getRealmId() != $this->realmId) {
            throw new InvalidArgumentException("Trying to register region $region in the wrong realm {$this->realmId}");
        }
        $this->regionsInRealm[] = $region;
    }

    public function getRegionsInRealm() // : Region[]
    {
        Region::__init();
        return $this->regionsInRealm;
    }

    public function __toString()
    {
        return "{$this->realmId} ({$this->realmDomainComponent})";
    }
}

class Region
{
    /*string*/ protected $regionId;
    /*string*/ protected $regionCode;
    /*Realm*/ protected $realm;

    protected static $wasInitialized = false;
    protected static $knownRegions = [];
    protected static $knownRegionsByCode = [];

    // OC1
    private static $AP_CHUNCHEON_1;
    private static $AP_MELBOURNE_1;
    private static $AP_HYDERABAD_1;
    private static $AP_MUMBAI_1;
    private static $AP_OSAKA_1;
    private static $AP_SEOUL_1;
    private static $AP_SYDNEY_1;
    private static $AP_TOKYO_1;
    private static $CA_MONTREAL_1;
    private static $CA_TORONTO_1;
    private static $EU_AMSTERDAM_1;
    private static $EU_FRANKFURT_1;
    private static $EU_ZURICH_1;
    private static $ME_JEDDAH_1;
    private static $ME_DUBAI_1;
    private static $SA_SAOPAULO_1;
    private static $UK_LONDON_1;
    private static $US_ASHBURN_1;
    private static $US_PHOENIX_1;
    private static $US_SANJOSE_1;
    private static $UK_CARDIFF_1;
    private static $SA_SANTIAGO_1;
    private static $SA_VINHEDO_1;
    private static $IL_JERUSALEM_1;

    // OC2
    private static $US_LANGLEY_1;
    private static $US_LUKE_1;

    // OC3
    private static $US_GOV_ASHBURN_1;
    private static $US_GOV_CHICAGO_1;
    private static $US_GOV_PHOENIX_1;

    // OC4
    private static $UK_GOV_LONDON_1;
    private static $UK_GOV_CARDIFF_1;

    // OC8
    private static $AP_CHIYODA_1;
    private static $AP_IBARAKI_1;

    public function __construct(
        /*string*/
        $regionId,
        /*string*/
        $regionCode,
        Realm $realm
    ) {
        $this->regionId = strtolower($regionId);
        $this->regionCode = strtolower($regionCode);
        $this->realm = $realm;
        Region::$knownRegions[$regionId] = $this;
        Region::$knownRegionsByCode[$regionCode] = $this;
        $realm->addRegionToRealm($this);
    }

    public static function __init()
    {
        if (!Region::$wasInitialized) {
            // OC1
            Region::$AP_CHUNCHEON_1 = new Region("ap-chuncheon-1", "yny", Realm::getRealm("oc1"));
            Region::$AP_MELBOURNE_1 = new Region("ap-melbourne-1", "mel", Realm::getRealm("oc1"));
            Region::$AP_HYDERABAD_1 = new Region("ap-hyderabad-1", "hyd", Realm::getRealm("oc1"));
            Region::$AP_MUMBAI_1 = new Region("ap-mumbai-1", "bom", Realm::getRealm("oc1"));
            Region::$AP_OSAKA_1 = new Region("ap-osaka-1", "kix", Realm::getRealm("oc1"));
            Region::$AP_SEOUL_1 = new Region("ap-seoul-1", "icn", Realm::getRealm("oc1"));
            Region::$AP_SYDNEY_1 = new Region("ap-sydney-1", "syd", Realm::getRealm("oc1"));
            Region::$AP_TOKYO_1 = new Region("ap-tokyo-1", "nrt", Realm::getRealm("oc1"));
            Region::$CA_MONTREAL_1 = new Region("ca-montreal-1", "yul", Realm::getRealm("oc1"));
            Region::$CA_TORONTO_1 = new Region("ca-toronto-1", "yyz", Realm::getRealm("oc1"));
            Region::$EU_AMSTERDAM_1 = new Region("eu-amsterdam-1", "ams", Realm::getRealm("oc1"));
            Region::$EU_FRANKFURT_1 = new Region("eu-frankfurt-1", "fra", Realm::getRealm("oc1"));
            Region::$EU_ZURICH_1 = new Region("eu-zurich-1", "zrh", Realm::getRealm("oc1"));
            Region::$ME_JEDDAH_1 = new Region("me-jeddah-1", "jed", Realm::getRealm("oc1"));
            Region::$ME_DUBAI_1 = new Region("me-dubai-1", "dxb", Realm::getRealm("oc1"));
            Region::$SA_SAOPAULO_1 = new Region("sa-saopaulo-1", "gru", Realm::getRealm("oc1"));
            Region::$UK_LONDON_1 = new Region("uk-london-1", "lhr", Realm::getRealm("oc1"));
            Region::$US_ASHBURN_1 = new Region("us-ashburn-1", "iad", Realm::getRealm("oc1"));
            Region::$US_PHOENIX_1 = new Region("us-phoenix-1", "phx", Realm::getRealm("oc1"));
            Region::$US_SANJOSE_1 = new Region("us-sanjose-1", "sjc", Realm::getRealm("oc1"));
            Region::$UK_CARDIFF_1 = new Region("uk-cardiff-1", "cwl", Realm::getRealm("oc1"));
            Region::$SA_SANTIAGO_1 = new Region("sa-santiago-1", "scl", Realm::getRealm("oc1"));
            Region::$SA_VINHEDO_1 = new Region("sa-vinhedo-1", "vcp", Realm::getRealm("oc1"));
            Region::$IL_JERUSALEM_1 = new Region("il-jerusalem-1", "mtz", Realm::getRealm("oc1"));

            // OC2
            Region::$US_LANGLEY_1 = new Region("us-langley-1", "lfi", Realm::getRealm("oc2"));
            Region::$US_LUKE_1 = new Region("us-luke-1", "luf", Realm::getRealm("oc2"));

            // OC3
            Region::$US_GOV_ASHBURN_1 = new Region("us-gov-ashburn-1", "ric", Realm::getRealm("oc3"));
            Region::$US_GOV_CHICAGO_1 = new Region("us-gov-chicago-1", "pia", Realm::getRealm("oc3"));
            Region::$US_GOV_PHOENIX_1 = new Region("us-gov-phoenix-1", "tus", Realm::getRealm("oc3"));

            // OC4
            Region::$UK_GOV_LONDON_1 = new Region("uk-gov-london-1", "ltn", Realm::getRealm("oc4"));
            Region::$UK_GOV_CARDIFF_1 = new Region("uk-gov-cardiff-1", "brs", Realm::getRealm("oc4"));

            // OC8
            Region::$AP_CHIYODA_1 = new Region("ap-chiyoda-1", "nja", Realm::getRealm("oc8"));
            Region::$AP_IBARAKI_1 = new Region("ap-ibaraki-1", "ukb", Realm::getRealm("oc8"));

            Region::$wasInitialized = true;
        }
    }

    // OC1
    public static function AP_CHUNCHEON_1()
    {
        Region::__init();
        return Region::$AP_CHUNCHEON_1;
    }
    public static function AP_MELBOURNE_1()
    {
        Region::__init();
        return Region::$AP_MELBOURNE_1;
    }
    public static function AP_HYDERABAD_1()
    {
        Region::__init();
        return Region::$AP_HYDERABAD_1;
    }
    public static function AP_MUMBAI_1()
    {
        Region::__init();
        return Region::$AP_MUMBAI_1;
    }
    public static function AP_OSAKA_1()
    {
        Region::__init();
        return Region::$AP_OSAKA_1;
    }
    public static function AP_SEOUL_1()
    {
        Region::__init();
        return Region::$AP_SEOUL_1;
    }
    public static function AP_SYDNEY_1()
    {
        Region::__init();
        return Region::$AP_SYDNEY_1;
    }
    public static function AP_TOKYO_1()
    {
        Region::__init();
        return Region::$AP_TOKYO_1;
    }
    public static function CA_MONTREAL_1()
    {
        Region::__init();
        return Region::$CA_MONTREAL_1;
    }
    public static function CA_TORONTO_1()
    {
        Region::__init();
        return Region::$CA_TORONTO_1;
    }
    public static function EU_AMSTERDAM_1()
    {
        Region::__init();
        return Region::$EU_AMSTERDAM_1;
    }
    public static function EU_FRANKFURT_1()
    {
        Region::__init();
        return Region::$EU_FRANKFURT_1;
    }
    public static function EU_ZURICH_1()
    {
        Region::__init();
        return Region::$EU_ZURICH_1;
    }
    public static function ME_JEDDAH_1()
    {
        Region::__init();
        return Region::$ME_JEDDAH_1;
    }
    public static function ME_DUBAI_1()
    {
        Region::__init();
        return Region::$ME_DUBAI_1;
    }
    public static function SA_SAOPAULO_1()
    {
        Region::__init();
        return Region::$SA_SAOPAULO_1;
    }
    public static function UK_LONDON_1()
    {
        Region::__init();
        return Region::$UK_LONDON_1;
    }
    public static function US_ASHBURN_1()
    {
        Region::__init();
        return Region::$US_ASHBURN_1;
    }
    public static function US_PHOENIX_1()
    {
        Region::__init();
        return Region::$US_PHOENIX_1;
    }
    public static function US_SANJOSE_1()
    {
        Region::__init();
        return Region::$US_SANJOSE_1;
    }
    public static function UK_CARDIFF_1()
    {
        Region::__init();
        return Region::$UK_CARDIFF_1;
    }
    public static function SA_SANTIAGO_1()
    {
        Region::__init();
        return Region::$SA_SANTIAGO_1;
    }
    public static function SA_VINHEDO_1()
    {
        Region::__init();
        return Region::$SA_VINHEDO_1;
    }
    public static function IL_JERUSALEM_1()
    {
        Region::__init();
        return Region::$IL_JERUSALEM_1;
    }

    // OC2
    public static function US_LANGLEY_1()
    {
        Region::__init();
        return Region::$US_LANGLEY_1;
    }
    public static function US_LUKE_1()
    {
        Region::__init();
        return Region::$US_LUKE_1;
    }

    // OC3
    public static function US_GOV_ASHBURN_1()
    {
        Region::__init();
        return Region::$US_GOV_ASHBURN_1;
    }
    public static function US_GOV_CHICAGO_1()
    {
        Region::__init();
        return Region::$US_GOV_CHICAGO_1;
    }
    public static function US_GOV_PHOENIX_1()
    {
        Region::__init();
        return Region::$US_GOV_PHOENIX_1;
    }

    // OC4
    public static function UK_GOV_LONDON_1()
    {
        Region::__init();
        return Region::$UK_GOV_LONDON_1;
    }
    public static function UK_GOV_CARDIFF_1()
    {
        Region::__init();
        return Region::$UK_GOV_CARDIFF_1;
    }

    // OC8
    public static function AP_CHIYODA_1()
    {
        Region::__init();
        return Region::$AP_CHIYODA_1;
    }
    public static function AP_IBARAKI_1()
    {
        Region::__init();
        return Region::$AP_IBARAKI_1;
    }

    public function getRegionId() // : string
    {
        return $this->regionId;
    }

    public function getRegionCode() // : string
    {
        return $this->regionCode;
    }

    public function getRealm() // : Realm
    {
        return $this->realm;
    }

    public static function getRegion(
        /*string*/
        $regionIdOrCode
    ) // : ?Region
    {
        $r = Region::getRegionById($regionIdOrCode);
        if ($r == null) {
            $r = Region::getRegionByCode($regionIdOrCode);
        }
        return $r;
    }

    public static function getRegionById(
        /*string*/
        $regionId
    ) // : ?Region
    {
        Region::__init();
        $id = strtolower($regionId);
        if (array_key_exists($id, Region::$knownRegions)) {
            return Region::$knownRegions[$id];
        }
        return null;
    }

    public static function getRegionByCode(
        /*string*/
        $regionCode
    ) // : ?Region
    {
        Region::__init();
        $code = strtolower($regionCode);
        if (array_key_exists($code, Region::$knownRegionsByCode)) {
            return Region::$knownRegionsByCode[$code];
        }
        return null;
    }

    public function __toString()
    {
        return "{$this->regionId} / {$this->regionCode} in realm {$this->realm}";
    }
}
