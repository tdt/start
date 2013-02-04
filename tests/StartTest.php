<?php

require "vendor/autoload.php";
require "app/core/Config.php";

use app\core\Config;

class StartTest extends \PHPUnit_Framework_TestCase{
    public function testConfigClass(){
        // Regular config
        $config = array(
            "general" => array(
                "hostname" => "http://localhost.be",
                "subdir" => "",
                "cache" => array(
                    "system" => "MemCache",
                    "port" => 50123
                    )
                ),
            "db" => array(
                "host" => "localhost",
                "port" => 3366
                )
            );

        Config::setConfig($config);
        $this->assertEquals($config, Config::getConfigArray(), "Config array doesn't match");
        $this->assertCount(3, Config::get('general'), "Number of items doesn't match");
        $this->assertEquals(3366, Config::get('db', 'port'), "First level value not matching");
        $this->assertEquals("MemCache", Config::get('general', 'cache', 'system'), "Second level value not matching");
        $this->assertEmpty(Config::get('general', 'cache', 'host'), "Non-existing key not returning empty value");

        // Empty array
        $config = array();
        Config::setConfig($config);
        $this->assertEquals($config, Config::getConfigArray(), "Config array doesn't match");
        $this->assertEmpty(Config::get('general'), "Previous config is still in the array");
    }
}