<?php

namespace Oracle\Oci\Common\Auth;

use PHPUnit\Framework\TestCase;

class SecurityTokenAdapterTest extends TestCase
{
    public function testJwt()
    {
        $token = "eyJraWQiOiJhc3dfb2MxX2RlMWIxZmM3IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiJvY2lkMS5pbnN0YW5jZS5vYzEuaWFkLmFudXdjbGp0NDZjbGRkeWM3N3Q3cGpzeHFxdXF3aHBuZG03YnJ1YXlmd2U2M2ZkcmRldW80Y2VvazRwYSIsIm9wYy1jZXJ0dHlwZSI6Imluc3RhbmNlIiwiaXNzIjoiYXV0aFNlcnZpY2Uub3JhY2xlLmNvbSIsImZwcmludCI6IjZDOjMzOkMwOjdBOjAxOjE3OjNEOjNBOkQ5OjUwOkYzOjNEOkUxOjdCOkVDOkE5OjgzOjExOjlDOjU3IiwicHR5cGUiOiJpbnN0YW5jZSIsImF1ZCI6Im9jaSIsIm9wYy10YWciOiJWMSxvY2lkMS5keW5hbWljZ3JvdXAub2MxLi5hYWFhYWFhYSwzZ215cHZ4eGsyangyem54aDI3cjZ3YTV6aGFmYmxudnl0YmlmbGp0ajdkNm5oc2dkNGdhLGg1ZXc3ZXFqM3FiZ2tpeXkzbW50aG54Z3RvN2hxYnlrd29nbzNiYjRndWZzZ21rd3VxZnEiLCJ0dHlwZSI6Ing1MDkiLCJvcGMtaW5zdGFuY2UiOiJvY2lkMS5pbnN0YW5jZS5vYzEuaWFkLmFudXdjbGp0NDZjbGRkeWM3N3Q3cGpzeHFxdXF3aHBuZG03YnJ1YXlmd2U2M2ZkcmRldW80Y2VvazRwYSIsImV4cCI6MTYyOTk3MDg2Niwib3BjLWNvbXBhcnRtZW50Ijoib2NpZDEudGVuYW5jeS5vYzEuLmFhYWFhYWFhazdxc2o2bHRmd2VrNWN4bDZ4cHR6bmFucjJrdDdmeHJiaG83ZGkzdmNoYWY2bTVhcHhlcSIsImlhdCI6MTYyOTk2OTY2NiwianRpIjoiMTRiYTRiYjYtNzg3OC00ODIwLTgyZjMtZTk0ZmE5M2JkMjc3IiwidGVuYW50Ijoib2NpZDEudGVuYW5jeS5vYzEuLmFhYWFhYWFhazdxc2o2bHRmd2VrNWN4bDZ4cHR6bmFucjJrdDdmeHJiaG83ZGkzdmNoYWY2bTVhcHhlcSIsImp3ayI6IntcImtpZFwiOlwiNkM6MzM6QzA6N0E6MDE6MTc6M0Q6M0E6RDk6NTA6RjM6M0Q6RTE6N0I6RUM6QTk6ODM6MTE6OUM6NTdcIixcIm5cIjpcInVjazZ3Mk5RREMwdXl0eFNBMlBWQkZtOGUxRUNabVBJaERvenAxVVdjblIyVEJTWUEtQ2V4V2hUbksyLS0wNGNvbWVtVTdBT1Utd1RqTFZNRlZLR3lCWXpVdWdQV0Vhbmx1UVRmN241djdLem5JR2c2YkJGU3lHdlM5SUJUOWVWVUlQWXRoaFBPeTZnMFhWUXdERnJCakV0Z0IzNzVUSWhrU21RY2RhbllnOTJpVU1vYVg4ZHhzNUtuUmpUbExrVExaTUh5ZlduWldCc0hLWXNIZDJ3T01qUDJ5ZDJWR2ZvNGJGTWdFeC1tTnVxVkxlQXd1UGZ0dm56MU5JQWp2cUZ6c253Zlhtemo0cTNRaS1HSGFub0JtSG1yTldHbnlMQ0xVb2p1ZFZ3clk2bWtONV9sUGFXaS1tNThueXRnNVc4WWFhMjRfckJsTkdwZHRIU3h5RDRBd1wiLFwiZVwiOlwiQVFBQlwiLFwia3R5XCI6XCJSU0FcIixcImFsZ1wiOlwiUlMyNTZcIixcInVzZVwiOlwic2lnXCJ9Iiwib3BjLXRlbmFudCI6Im9jaWQxLnRlbmFuY3kub2MxLi5hYWFhYWFhYWs3cXNqNmx0ZndlazVjeGw2eHB0em5hbnIya3Q3ZnhyYmhvN2RpM3ZjaGFmNm01YXB4ZXEifQ.KG8tVWD6Nl8SiuG9zDXnZjGZVzbaGoyBABZwBQZLyaxu0lB5xXdm_RYtvyR0yygULwLvuIPzbkwTSJcJE5HjReQUIdqTP_P4E4fGbVdwrU6dYNsTOMf2jZZVQtUP7iWsJKrvOzs1ZwI1nmoZ5uRUSNq6sUbznHWk8qvdPycEuR6n5aElrXp0cHgfg9IxAU1CnnVjklIJHZslCIL-7GV2jWjn5ae456pAYFF0ihpEHpaMTnBdNkYjHq3rDnOXG3194E5_rQzwdLYhliID5uYhRr8bvfm2fvH9UMSA8ZLkSPg3Xc_z_2xrlawguLjw5Bw5-UojFYFfcJIiyXE6Qqtlqw";
        $jwt = new JWT($token);
        
        $this->assertEquals("RS256", $jwt->getHeader()->alg);
        $this->assertEquals("6C:33:C0:7A:01:17:3D:3A:D9:50:F3:3D:E1:7B:EC:A9:83:11:9C:57", $jwt->getPayload()->fprint);
        $this->assertEquals("instance", $jwt->getPayload()->ptype);
        $this->assertEquals("oci", $jwt->getPayload()->aud);
        $this->assertEquals("V1,ocid1.dynamicgroup.oc1..aaaaaaaa,3gmypvxxk2jx2znxh27r6wa5zhafblnvytbifljtj7d6nhsgd4ga,h5ew7eqj3qbgkiyy3mnthnxgto7hqbykwogo3bb4gufsgmkwuqfq", $jwt->getPayload()->{'opc-tag'});
        $this->assertEquals("x509", $jwt->getPayload()->ttype);
        $this->assertEquals("ocid1.instance.oc1.iad.anuwcljt46clddyc77t7pjsxqquqwhpndm7bruayfwe63fdrdeuo4ceok4pa", $jwt->getPayload()->{'opc-instance'});
        $this->assertEquals("ocid1.tenancy.oc1..aaaaaaaak7qsj6ltfwek5cxl6xptznanr2kt7fxrbho7di3vchaf6m5apxeq", $jwt->getPayload()->{'opc-tenant'});
        $this->assertEquals("1629970866", $jwt->getPayload()->exp);
        $this->assertEquals("1629969666", $jwt->getPayload()->iat);
        // No key? $this->assertEquals("1629969666", $jwt->getPayload()->key);
        $this->assertEquals("authService.oracle.com", $jwt->getPayload()->iss);
        // No nbf? $this->assertEquals("1629969666", $jwt->getPayload()->nbf);

        $this->assertEquals("6C:33:C0:7A:01:17:3D:3A:D9:50:F3:3D:E1:7B:EC:A9:83:11:9C:57", $jwt->getClaims()->kid);
        $this->assertEquals("uck6w2NQDC0uytxSA2PVBFm8e1ECZmPIhDozp1UWcnR2TBSYA-CexWhTnK2--04comemU7AOU-wTjLVMFVKGyBYzUugPWEanluQTf7n5v7KznIGg6bBFSyGvS9IBT9eVUIPYthhPOy6g0XVQwDFrBjEtgB375TIhkSmQcdanYg92iUMoaX8dxs5KnRjTlLkTLZMHyfWnZWBsHKYsHd2wOMjP2yd2VGfo4bFMgEx-mNuqVLeAwuPftvnz1NIAjvqFzsnwfXmzj4q3Qi-GHanoBmHmrNWGnyLCLUojudVwrY6mkN5_lPaWi-m58nytg5W8Yaa24_rBlNGpdtHSxyD4Aw", $jwt->getClaims()->n);
        $this->assertEquals("AQAB", $jwt->getClaims()->e);
        $this->assertEquals("RSA", $jwt->getClaims()->kty);
        $this->assertEquals("RS256", $jwt->getClaims()->alg);
        $this->assertEquals("sig", $jwt->getClaims()->use);
    }
}
