<?php

use Oracle\Oci\Common\Logging\StringBufferLogAdapter;
use PHPUnit\Framework\TestCase;

class LogAdapterTest extends TestCase
{
    public function testSuppressSensitive()
    {
        $logger = new StringBufferLogAdapter(LOG_INFO);
        $logger->debug("Debug doesn't show up.");
        $logger->info("Info does show up.");
        $logger->info("But sensitive info does not show up 1.", "some\\log\\name\\sensitive");
        $logger->info("But sensitive info does not show up 2.", "some\\log\\name\\sensitive\\more");
        $logger->info("But sensitive info does not show up 3.", "sensitive\\some\\log\\name\\more");

        $this->assertNotContains("Debug doesn't show up.", $logger->getString());
        $this->assertContains("Info does show up.", $logger->getString());
        $this->assertNotContains("But sensitive info does not show up 1.", $logger->getString());
        $this->assertNotContains("But sensitive info does not show up 2.", $logger->getString());
        $this->assertNotContains("But sensitive info does not show up 3.", $logger->getString());
    }

    public function testEnableSpecificSensitive()
    {
        $logger = new StringBufferLogAdapter(LOG_INFO, ["some\\log\\name\\sensitive" => LOG_INFO]);
        $logger->debug("Debug doesn't show up.");
        $logger->info("Info does show up.");
        $logger->info("This sensitive info shows up because it was specifically enabled.", "some\\log\\name\\sensitive");
        $logger->info("But sensitive info does not show up 2.", "some\\log\\name\\sensitive\\more");
        $logger->info("But sensitive info does not show up 3.", "sensitive\\some\\log\\name\\more");

        $this->assertNotContains("Debug doesn't show up.", $logger->getString());
        $this->assertContains("Info does show up.", $logger->getString());
        $this->assertContains("This sensitive info shows up because it was specifically enabled.", $logger->getString());
        $this->assertNotContains("But sensitive info does not show up 2.", $logger->getString());
        $this->assertNotContains("But sensitive info does not show up 3.", $logger->getString());
    }

    public function testEnableAllSensitive()
    {
        // set an empty array of regexes (3rd parameter)
        $logger = new StringBufferLogAdapter(LOG_INFO, [], []);
        $logger->debug("Debug doesn't show up.");
        $logger->info("Info does show up.");
        $logger->info("All sensitive shows up 1.", "some\\log\\name\\sensitive");
        $logger->info("All sensitive shows up 2.", "some\\log\\name\\sensitive\\more");
        $logger->info("All sensitive shows up 3.", "sensitive\\some\\log\\name\\more");

        $this->assertNotContains("Debug doesn't show up.", $logger->getString());
        $this->assertContains("Info does show up.", $logger->getString());
        $this->assertContains("All sensitive shows up 1.", $logger->getString());
        $this->assertContains("All sensitive shows up 2.", $logger->getString());
        $this->assertContains("All sensitive shows up 3.", $logger->getString());
    }

    public function testEnableSpecialSensitiveSuppression()
    {
        // set a regex for anything with secret in it and set it to not log
        $logger = new StringBufferLogAdapter(LOG_INFO, [], ["/secret/" => 0]);
        $logger->debug("Debug doesn't show up.");
        $logger->info("Info does show up.");
        $logger->info("All sensitive shows up 1.", "some\\log\\name\\sensitive");
        $logger->info("Secret information does not show up.", "some\\log\\name\\secret\\more");
        $logger->info("All sensitive shows up 3.", "sensitive\\some\\log\\name\\more");

        $this->assertNotContains("Debug doesn't show up.", $logger->getString());
        $this->assertContains("Info does show up.", $logger->getString());
        $this->assertContains("All sensitive shows up 1.", $logger->getString());
        $this->assertNotContains("Secret information does not show up.", $logger->getString());
        $this->assertContains("All sensitive shows up 3.", $logger->getString());
    }
}
