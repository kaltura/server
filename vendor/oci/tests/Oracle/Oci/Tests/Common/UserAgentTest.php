<?php

namespace Oracle\Oci\Common;

use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
    public function testUserAgent()
    {
        $previousAppendUserAgent = getenv("OCI_SDK_APPEND_USER_AGENT");
        try {
            UserAgent::init();
            putenv("OCI_SDK_APPEND_USER_AGENT=");
            UserAgent::setAdditionalClientUserAgent("");

            $this->assertContains("Oracle-PhpSDK", UserAgent::getUserAgent());
            $this->assertContains("(PHP/", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudShell", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudDevelopmentKit", UserAgent::getUserAgent());

            UserAgent::setAdditionalClientUserAgent("Oracle-CloudShell");
            $this->assertContains("Oracle-PhpSDK", UserAgent::getUserAgent());
            $this->assertContains("(PHP/", UserAgent::getUserAgent());
            $this->assertContains("Oracle-CloudShell", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudDevelopmentKit", UserAgent::getUserAgent());

            putenv("OCI_SDK_APPEND_USER_AGENT=Oracle-CloudDevelopmentKit");
            UserAgent::init();

            $this->assertContains("Oracle-PhpSDK", UserAgent::getUserAgent());
            $this->assertContains("(PHP/", UserAgent::getUserAgent());
            $this->assertContains("Oracle-CloudShell", UserAgent::getUserAgent());
            $this->assertContains("Oracle-CloudDevelopmentKit", UserAgent::getUserAgent());

            UserAgent::setAdditionalClientUserAgent("");

            $this->assertContains("Oracle-PhpSDK", UserAgent::getUserAgent());
            $this->assertContains("(PHP/", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudShell", UserAgent::getUserAgent());
            $this->assertContains("Oracle-CloudDevelopmentKit", UserAgent::getUserAgent());

            putenv("OCI_SDK_APPEND_USER_AGENT=");
            UserAgent::init();

            $this->assertContains("Oracle-PhpSDK", UserAgent::getUserAgent());
            $this->assertContains("(PHP/", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudShell", UserAgent::getUserAgent());
            $this->assertNotContains("Oracle-CloudDevelopmentKit", UserAgent::getUserAgent());
        } finally {
            putenv("OCI_SDK_APPEND_USER_AGENT=$previousAppendUserAgent");
            UserAgent::init();
        }
    }
}
