# careye

### Autostart containers
`docker update --restart unless-stopped $(docker ps -q)`

### Enter container
`docker exec -it php8_full bash`

`docker exec -it --user=app php8_full bash`

### Add new cron
Add cron config to `config/crontab` and then run:

`bin/console app:crontab:update`

