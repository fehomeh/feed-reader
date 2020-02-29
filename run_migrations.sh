#!/usr/bin/env sh
docker exec feed_php bin/console doctrine:migrations:migrate
