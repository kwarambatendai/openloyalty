Run command inside docker container
===================================

Simplest way to run command inside container is to execute:

  docker-compose exec open_loyalty_backend bash

This will bring you container bash shell where you will be able to execute any command needed.

Just make sure that you are in correct directory (`/var/www/ol/backend`)

and now feel free to execute composer install, phing setup or any other command.