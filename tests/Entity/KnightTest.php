<?php

namespace DragonsOfMugloar\Tests\Entity;

use DragonsOfMugloar\Entity\Knight;
use PHPUnit\Framework\TestCase;

class KnightTest extends TestCase
{
    /**
     * @covers \DragonsOfMugloar\Entity\Knight::__toString()
     */
    public function testIsDumpedToJson()
    {
        $fixture = file_get_contents(__DIR__.'/../Fixtures/Game/knight-zeroish-skills.json');
        $fixtureArr = json_decode($fixture, true);

        $knight = new Knight($fixtureArr['knight']);

        $this->assertJson($knight->__toString());
    }
}
