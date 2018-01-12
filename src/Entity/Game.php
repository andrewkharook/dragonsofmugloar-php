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
     * Builds a set of dragon's skills based on weather and knight's skills
     *
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
                // Defeat. Everybody dies
            case Weather::FORECAST_FOG:
                // Victory. Knight is useless in the fog
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
                $winningStrategy = [0, -1, -1, 2];
                $skills = $knight->getSkills();
                asort($skills, SORT_NUMERIC);
                foreach ($skills as $name => $value) {
                    $adjustment = array_shift($winningStrategy);

                    if (0 === $value) {
                        $winningStrategy[0] += $adjustment;

                        continue;
                    }

                    $skills[$name] += $adjustment;
                }
        }

        $this->gameState->dragon = (object) $skills;

        return $skills;
    }

    /**
     * Submits a solution to the API
     *
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
     * Parses the API response
     *
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
     * Retrieves the Game Id from current state
     *
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

    /**
     * Rettrieves the list of Knight's skills from current state
     *
     * @return array
     */
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
     * Getter method for game's logger
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Setter method for game's logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

}
