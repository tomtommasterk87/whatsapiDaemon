<?php
/**
 * Created by PhpStorm.
 * User: martin
 * Date: 19.05.14
 * Time: 18:49
 */

namespace test\mawalu\whatsapiDaemon;

use mawalu\whatsapiDaemon\events;

class eventsTest extends \PHPUnit_Framework_TestCase
{
    public function testEventHandler()
    {
        $events = new events();
        $events->registerHandler("127.0.0.1:1337", ["onGetMessage"]);
        $this->assertEquals("127.0.0.1:1337", $events->searchForHandler("onGetMessage")[0]);
    }
    public function testFireEventAndCallHandler()
    {
        $expected = array("testVal1", "TestVal2");
        $events = new events();
        $events->registerHandler("127.0.0.1:1337", ["onGetMessage"]);
        $events->handleEvent("onGetMessage", ["testVal1", "TestVal2"]);
        $this->assertEquals($expected, $events->getTodo()["127.0.0.1:1337"][0]["onGetMessage"]);
        $events->doneTodo();
        $this->assertCount(0, $events->getTodo());
    }
}
 