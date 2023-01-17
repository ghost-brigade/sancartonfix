#!/bin/bash
docker login --username $GH_USERNAME --password=$GH_TOKEN ghcr.io

docker pull ghcr.io/ghost-brigade/sancartonfix-caddy:latest
docker pull ghcr.io/ghost-brigade/sancartonfix-php:latest

docker compose down
docker system prune -f

docker compose up -d
