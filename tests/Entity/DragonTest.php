<?php

namespace DragonsOfMugloar\Tests\Entity;

use DragonsOfMugloar\Entity\Dragon;
use PHPUnit\Framework\TestCase;

class DragonTest extends TestCase
{
    /**
     * @covers \DragonsOfMugloar\Entity\Dragon::__toString()
     */
    public function testIsDumpedToJson()
    {
        $fixture = file_get_contents(__DIR__.'/../Fixtures/Game/dragon-default-skills.json');
        $fixtureArr = json_decode($fixture, true);

        $dragon = new Dragon($fixtureArr['dragon']);

        $this->assertJson($dragon->__toString());
    }
}
