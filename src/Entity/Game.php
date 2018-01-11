<?php

namespace DragonsOfMugloar\Entity;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class Game
{
    /**
     * API endpoint for retrieving initial game data
     */
    const GAME_API_URL_START = 'http://www.dragonsofmugloar.com/api/game';

    /**
     * API endpoint for submitting solution
     */
    const GAME_API_URL_SUBMIT = 'http://www.dragonsofmugloar.com/api/game/%gameId%/solution';

    /**
     * Game result code
     */
    const GAME_RESULT_VICTORY = 'Victory';

    /**
     * Game result code
     */
    const GAME_RESULT_DEFEAT = 'Defeat';

    /**
     * @var \StdClass
     */
    private $gameState;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Game constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Initiates a new game
     *
     * @return Game
     */
    public function init(): Game
    {
        $response = $this->httpClient->request(
            'GET',
            self::GAME_API_URL_START
        );

        $this->gameState = $this->parseApiResponse($response);

        return $this;
    }

    /**
     * @param Weather $weather
     * @param Knight  $knight
     * @return array|string
     */
    public function getDragonsSkills(Weather $weather, Knight $knight): array
    {
        if (isset($this->gameState->dragon)) {
            return (array) $this->gameState->dragon;
        }

        $forecastCode = $weather->getForecastCode();
        switch ($forecastCode) {
            case Weather::FORECAST_STORM:
                // Everybody dies
            case Weather::FORECAST_DRY:
                $skills = [
                    'attack'    => 5,
                    'armor'     => 5,
                    'agility'   => 5,
                    'endurance' => 5,
                ];
                break;
            case Weather::FORECAST_RAIN:
                $skills = [
                    'attack'    => 5,
                    'armor'     => 10,
                    'agility'   => 5,
                    'endurance' => 0,
                ];
                break;
            default:
                $winningStrategy = [2, -1, -1, 0];
                $opponentsSkills = $knight->getSkills();
                arsort($opponentsSkills);
                $keys = array_keys($opponentsSkills);
                $values = array_map(function ($a, $b) {
                    return $a + $b;
                }, $opponentsSkills, $winningStrategy);
                $skills = array_combine($keys, $values);
        }

        $this->gameState->dragon = (object) $skills;

        return $skills;
    }

    /**
     * @param Dragon $dragon
     * @return string
     */
    public function startBattle(Dragon $dragon): string
    {
        $url = str_replace('%gameId%', $this->getGameId(), self::GAME_API_URL_SUBMIT);
        $response = $this->httpClient->request(
            'PUT',
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => $dragon->__toString(),
            ]
        );

        $gameResult = $this->parseApiResponse($response);
        if (null !== $gameResult
            && self::GAME_RESULT_VICTORY === $gameResult->status) {
            return self::GAME_RESULT_VICTORY;
        }

        return self::GAME_RESULT_DEFEAT;
    }

    /**
     * @param ResponseInterface $response
     * @return null|\stdClass
     * @throws \RuntimeException
     */
    protected function parseApiResponse(ResponseInterface $response): ?\stdClass
    {
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Failed querying API.');
        }

        $contents = $response->getBody()->getContents();

        return json_decode($contents);
    }

    /**
     * @return int
     */
    public function getGameId(): int
    {
        if (null !== $this->gameState) {
            return $this->gameState->gameId;
        }

        return $this->init()
                    ->gameState
                    ->gameId;
    }

    public function getKnightsSkills(): array
    {
        if (null !== $this->gameState) {
            return (array) $this->gameState->knight;
        }

        return (array) $this->init()
                            ->gameState
                            ->knight;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

}
