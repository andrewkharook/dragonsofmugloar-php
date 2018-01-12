# «Dragons Of Mugloar» game
PHP application that talks to [battles API](http://www.dragonsofmugloar.com/api) and [weather API](http://www.dragonsofmugloar.com/weather) to get the battle conditions and tries to win the fight.

Current implementation keeps the victory rate at about 96%.

### To run the game:
Clone or download the repository to your computer, and run the `app.php` in terminal with optional `-g` or `--games` parameter stating how many battles you would like to hold (default number of games is 10):
```
php src/app.php --games=100
```

After you've run the application simply wait for it to display the results:
```
**** Final scores in 100 games:
     100 victories
     0 defeats
**** Log written to /Users/username/dragonsofmugloar/var/log
```
Detailed log is saved to `./var/log/game.log.txt`.

Have fun.
