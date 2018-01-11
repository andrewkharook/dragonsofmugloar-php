<?php

namespace DragonsOfMugloar;


require_once __DIR__.'/../vendor/autoload.php';

use DragonsOfMugloar\Entity\Game;
use DragonsOfMugloar\Entity\Knight;
use DragonsOfMugloar\Entity\Dragon;
use DragonsOfMugloar\Entity\Weather;
use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$httpClient = new Client();

$logDir = __DIR__.'/../var/log';
$logger = new Logger('game');
$logger->pushHandler(new StreamHandler($logDir.'/game.log.txt'));

$game = new Game($httpClient);
$game->setLogger($logger);

$gamesNumber = getGamesNumber();
$victories = $defeats = 0;

echo "**** Running $gamesNumber games in a row. Hold your breath.\n";

for ($i = 1; $i <= $gamesNumber; $i++) {
    if (0 === ($i % 10)) {
        echo ".\n";
    }

    $game->init();
    $weather = (new Weather($httpClient))->getForecast($game->getGameId());
    $knight = new Knight($game->getKnightsSkills());
    $dragon = new Dragon($game->getDragonsSkills($weather, $knight));
    $result = $game->startBattle($dragon);

    if (Game::GAME_RESULT_VICTORY === $result) {
        $victories++;
    } else {
        $defeats++;
    }

    $game->getLogger()->info('New game started.', [
        'game_id' => $game->getGameId(),
        'weather' => $weather->getForecastCode(),
        'knight'  => $knight->__toString(),
        'dragon'  => $dragon,
        'result'  => $result,
    ]);
}

echo "**** Final scores in $gamesNumber games:\n"
    ."     $victories victories\n"
    ."     $defeats defeats\n"
    ."**** Log written to $logDir\n";

return 0;

function getGamesNumber(): int {
    $gamesNumber = 0;
    $cliParams = getopt('g::', ['games::']);

    if (isset($cliParams['g']) && is_numeric($cliParams['g'])) {
        $gamesNumber = (int) $cliParams['g'];
    } elseif (isset($cliParams['games']) && is_numeric($cliParams['games'])) {
        $gamesNumber = (int) $cliParams['games'];
    }

    return $gamesNumber > 0 ? $gamesNumber : 10;
}
