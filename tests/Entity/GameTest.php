<?php

namespace DragonsOfMugloar\Tests\Entity;


use DragonsOfMugloar\Entity\AbstractPlayer;
use DragonsOfMugloar\Entity\Game;
use DragonsOfMugloar\Entity\Knight;
use DragonsOfMugloar\Entity\Weather;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /**
     * @covers              \DragonsOfMugloar\Entity\Game::init()
     * @expectedException   \RuntimeException
     */
    public function testExceptionIsRaisedForBadApiResponse()
    {
        $httpClient = $this->getMockedHttpClient(400);

        (new Game($httpClient))->init();
    }

    /**
     * @covers      \DragonsOfMugloar\Entity\Game::getDragonsSkills()
     */
    public function testDragonSkillsArePositive()
    {
        $gameResponse = file_get_contents(__DIR__.'/../Fixtures/Game/knight-zeroish-skills.json');
        $httpClient = $this->getMockedHttpClient(200, $gameResponse);
        $game = (new Game($httpClient))->init();

        $weather = $this->createMock(Weather::class);
        $weather->method('getForecastCode')
            ->willReturn(Weather::FORECAST_NORMAL);

        $knightsSkills = $game->getKnightsSkills();
        unset($knightsSkills['name']);
        $knight = $this->createMock(Knight::class);
        $knight->method('getSkills')
            ->willReturn($knightsSkills);

        $skills = $game->getDragonsSkills($weather, $knight);
        foreach ($skills as $skill) {
            $this->assertGreaterThanOrEqual(0, $skill);
        }
    }

    /**
     * @covers      \DragonsOfMugloar\Entity\Game::getDragonsSkills()
     */
    public function testDragonSkillsEqualTotalSkillsConst()
    {
        $gameResponse = file_get_contents(__DIR__.'/../Fixtures/Game/knight-zeroish-skills.json');
        $httpClient = $this->getMockedHttpClient(200, $gameResponse);
        $game = (new Game($httpClient))->init();

        $weather = $this->createMock(Weather::class);
        $weather->method('getForecastCode')
            ->willReturn(Weather::FORECAST_NORMAL);

        $knightsSkills = $game->getKnightsSkills();
        unset($knightsSkills['name']);
        $knight = $this->createMock(Knight::class);
        $knight->method('getSkills')
            ->willReturn($knightsSkills);

        $skills = $game->getDragonsSkills($weather, $knight);

        $this->assertEquals(
            AbstractPlayer::SKILLS_TOTAL,
            array_sum($skills)
        );
    }

    private function getMockedHttpClient($responseCode, $responseBody = null)
    {
        $mock = new MockHandler([new Response($responseCode, [], $responseBody)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
