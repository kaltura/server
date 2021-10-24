<?php

namespace Oracle\Oci\Common;

class Realm
{
    /*string*/ protected $realmId;
    /*string*/ protected $realmDomainComponent;

    protected static $wasInitialized = false;
    protected static $knownRealms = [];
    protected static $unknownRegionRealm;

    public function __construct(
        /*string*/ 
        $realmId,
        /*string*/ 
        $realmDomainComponent
    )
    {
        $this->realmId = strtolower($realmId);
        $this->realmDomainComponent = $realmDomainComponent;
        Realm::$knownRealms[$realmId] = $this;
    }

    public static function __init()
    {
        if (!Realm::$wasInitialized) {
            $OC1 = new Realm("oc1", "oraclecloud.com");
            $OC2 = new Realm("oc2", "oraclegovcloud.com");
            $OC3 = new Realm("oc3", "oraclegovcloud.com");
            $OC4 = new Realm("oc4", "oraclegovcloud.uk");
            $OC8 = new Realm("oc8", "oraclecloud8.com");
            Realm::$unknownRegionRealm = $OC1;
            Realm::$wasInitialized = true;
        }
    }

    public function getRealmId() // : string
    {
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

    public function __construct(
        /*string*/ 
        $regionId,
        /*string*/ 
        $regionCode,
        Realm $realm
    )
    {
        $this->regionId = strtolower($regionId);
        $this->regionCode = strtolower($regionCode);
        $this->realm = $realm;
        Region::$knownRegions[$regionId] = $this;
        Region::$knownRegionsByCode[$regionCode] = $this;
    }

    public static function __init()
    {
        if (!Region::$wasInitialized) {
            // OC1
            $AP_CHUNCHEON_1 = new Region("ap-chuncheon-1", "yny", Realm::getRealm("oc1"));
            $AP_MELBOURNE_1 = new Region("ap-melbourne-1", "mel", Realm::getRealm("oc1"));
            $AP_HYDERABAD_1 = new Region("ap-hyderabad-1", "hyd", Realm::getRealm("oc1"));
            $AP_MUMBAI_1 = new Region("ap-mumbai-1", "bom", Realm::getRealm("oc1"));
            $AP_OSAKA_1 = new Region("ap-osaka-1", "kix", Realm::getRealm("oc1"));
            $AP_SEOUL_1 = new Region("ap-seoul-1", "icn", Realm::getRealm("oc1"));
            $AP_SYDNEY_1 = new Region("ap-sydney-1", "syd", Realm::getRealm("oc1"));
            $AP_TOKYO_1 = new Region("ap-tokyo-1", "nrt", Realm::getRealm("oc1"));
            $CA_MONTREAL_1 = new Region("ca-montreal-1", "yul", Realm::getRealm("oc1"));
            $CA_TORONTO_1 = new Region("ca-toronto-1", "yyz", Realm::getRealm("oc1"));
            $EU_AMSTERDAM_1 = new Region("eu-amsterdam-1", "ams", Realm::getRealm("oc1"));
            $EU_FRANKFURT_1 = new Region("eu-frankfurt-1", "fra", Realm::getRealm("oc1"));
            $EU_ZURICH_1 = new Region("eu-zurich-1", "zrh", Realm::getRealm("oc1"));
            $ME_JEDDAH_1 = new Region("me-jeddah-1", "jed", Realm::getRealm("oc1"));
            $ME_DUBAI_1 = new Region("me-dubai-1", "dxb", Realm::getRealm("oc1"));
            $SA_SAOPAULO_1 = new Region("sa-saopaulo-1", "gru", Realm::getRealm("oc1"));
            $UK_LONDON_1 = new Region("uk-london-1", "lhr", Realm::getRealm("oc1"));
            $US_ASHBURN_1 = new Region("us-ashburn-1", "iad", Realm::getRealm("oc1"));
            $US_PHOENIX_1 = new Region("us-phoenix-1", "phx", Realm::getRealm("oc1"));
            $US_SANJOSE_1 = new Region("us-sanjose-1", "sjc", Realm::getRealm("oc1"));
            $UK_CARDIFF_1 = new Region("uk-cardiff-1", "cwl", Realm::getRealm("oc1"));
            $SA_SANTIAGO_1 = new Region("sa-santiago-1", "scl", Realm::getRealm("oc1"));
            $SA_VINHEDO_1 = new Region("sa-vinhedo-1", "vcp", Realm::getRealm("oc1"));
            $IL_JERUSALEM_1 = new Region("il-jerusalem-1", "mtz", Realm::getRealm("oc1"));

            // OC2
            $US_LANGLEY_1 = new Region("us-langley-1", "lfi", Realm::getRealm("oc2"));
            $US_LUKE_1 = new Region("us-luke-1", "luf", Realm::getRealm("oc2"));

            // OC3
            $US_GOV_ASHBURN_1 = new Region("us-gov-ashburn-1", "ric", Realm::getRealm("oc3"));
            $US_GOV_CHICAGO_1 = new Region("us-gov-chicago-1", "pia", Realm::getRealm("oc3"));
            $US_GOV_PHOENIX_1 = new Region("us-gov-phoenix-1", "tus", Realm::getRealm("oc3"));

            // OC4
            $UK_GOV_LONDON_1 = new Region("uk-gov-london-1", "ltn", Realm::getRealm("oc4"));
            $UK_GOV_CARDIFF_1 = new Region("uk-gov-cardiff-1", "brs", Realm::getRealm("oc4"));

            // OC8
            $AP_CHIYODA_1 = new Region("ap-chiyoda-1", "nja", Realm::getRealm("oc8"));
            $AP_IBARAKI_1 = new Region("ap-ibaraki-1", "ukb", Realm::getRealm("oc8"));

            Region::$wasInitialized = true;
        }
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
