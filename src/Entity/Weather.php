<?php

namespace DragonsOfMugloar\Entity;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Weather
{
    const FORECAST_NORMAL = 'NMR';

    const FORECAST_STORM = 'SRO';

    const FORECAST_RAIN = 'HVA';

    const FORECAST_DRY = 'T E';

    const WEATHER_API_URL = 'http://www.dragonsofmugloar.com/weather/api/report/%gameId%';

    /**
     * @var \StdClass
     */
    private $forecast;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Weather constructor.
     *
     * @param Client $httpClient
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getForecast(int $gameId): Weather
    {
        $url = str_replace('%gameId%', $gameId, self::WEATHER_API_URL);
        $response = $this->httpClient->request(
            'GET',
            $url
        );

        $this->forecast = $this->parseApiResponse($response);

        return $this;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getForecastCode(): string
    {
        if (!isset($this->forecast->code)) {
            throw new \RuntimeException('Unable to get weather report code.');
        }

        return $this->forecast->code;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getForecastMessage(): string
    {
        if (!isset($this->forecast->message)) {
            throw new \RuntimeException('Unable to get weather report details.');
        }

        return $this->forecast->message;
    }

    /**
     * @param ResponseInterface $response
     * @return \SimpleXMLElement
     * @throws \RuntimeException
     */
    protected function parseApiResponse(ResponseInterface $response): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to get weather report.');
        }

        $contents = $response->getBody()->getContents();

        $xml = simplexml_load_string($contents);
        if (false === $xml) {
            throw new \RuntimeException('Invalid XML response from Weather API.');
        }

        return $xml;
    }
}
