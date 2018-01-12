<?php

namespace DragonsOfMugloar\Tests\Entity;

use DragonsOfMugloar\Entity\AbstractPlayer;
use PHPUnit\Framework\TestCase;

class AbstractPlayerTest extends TestCase
{
    /**
     * @covers              \DragonsOfMugloar\Entity\AbstractPlayer::__construct()
     * @expectedException   \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testExceptionIsRaisedOnWrongSetOfSkills()
    {
        $fixture = file_get_contents(__DIR__.'/../Fixtures/Game/knight-zeroish-skills.json');
        $fixtureArr = json_decode($fixture, true);
        $fixtureArr['knight']['hobby'] = 'Kerling';

        $this->getMockForAbstractClass(AbstractPlayer::class, [$fixtureArr['knight']]);
    }

    /**
     * @covers              \DragonsOfMugloar\Entity\AbstractPlayer::__construct()
     * @expectedException   \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testExceptionIsRaisedOnMissingSkills()
    {
        $fixture = file_get_contents(__DIR__.'/../Fixtures/Game/dragon-default-skills.json');
        $fixtureArr = json_decode($fixture, true);
        unset($fixtureArr['dragon']['attack']);

        $this->getMockForAbstractClass(AbstractPlayer::class, [$fixtureArr['dragon']]);
    }
}
