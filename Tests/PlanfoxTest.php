<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
class PlanfoxTest extends PHPUnit_Framework_TestCase
{
    public function testCreateApplication()
    {
        $app = Planfox::createApplication(__DIR__, false);
        $this->assertEquals(true, $app instanceof \Planfox\Foundation\Application);
    }

    public function testCreateApplicationUsageConfigDirectory()
    {
        $app = Planfox::createApplication(__DIR__);
        $this->assertEquals(true, $app instanceof \Planfox\Foundation\Application);
    }
}