<?php

namespace DragonsOfMugloar\Tests\Entity;

use DragonsOfMugloar\Entity\Weather;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{
    /**
     * @covers              \DragonsOfMugloar\Entity\Weather::retrieveForecast()
     * @expectedException   \RuntimeException
     */
    public function testExceptionIsRaisedForBadApiResponse()
    {
        $httpClient = $this->getMockedHttpClient(400);

        (new Weather($httpClient))->retrieveForecast(1);
    }

    private function getMockedHttpClient($responseCode, $responseBody = null)
    {
        $mock = new MockHandler([new Response($responseCode, [], $responseBody)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
