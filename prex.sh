#!/bin/bash

command=$1

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
PURPLE='\033[1;35m'
NC='\033[0m'

ENV_FILE="--env-file giphy-integration/.env"

if [ ! $command ]; then
  echo -e "${RED}You need to use a parameter ${YELLOW}(install|up|bash|test|down)${NC}"
  exit
fi

wait_for_db() {
  title "Waiting for database to be ready..."

  while ! docker-compose $ENV_FILE exec -T mysql mysqladmin ping -h"localhost" --silent; do
    sleep 1
  done

  sleep 5

  title "Database is ready!"
}

logo() {
  echo -e "${NC}"
  echo -e "${PURPLE} _____                "
  echo -e "${PURPLE}|  __ \               "
  echo -e "${PURPLE}| |__) | __ _____  __ "
  echo -e "${PURPLE}|  ___/ '__/ _ \ \/ / "
  echo -e "${PURPLE}| |   | | |  __/>  <  "
  echo -e "${PURPLE}|_|   |_|  \___/_/\_\ "
  echo -e "${NC}"
}

title() {
  MESSAGE=$1
  echo -e ""
  echo -e "${YELLOW}${MESSAGE}${NC}"
}

run_dependencies() {
  title "Running migrations..."
  docker-compose $ENV_FILE exec app php artisan migrate

  title "Generating Passport keys..."
  docker-compose $ENV_FILE exec app php artisan passport:keys
  docker-compose $ENV_FILE exec app php artisan passport:client --personal --no-interaction

  title "Running database seeders..."
  docker-compose $ENV_FILE exec app php artisan db:seed

  echo -e "${GREEN}Containers are up and running!"
}

install_dependencies() {
  cd giphy-integration || exit

  title "Running composer install..."
  docker run --rm \
          -u "$(id -u):$(id -g)" \
          -v $(pwd):/var/www/html \
          -w /var/www/html \
          composer install --no-interaction --ignore-platform-reqs --prefer-dist

  cd ..
}

up() {
  EXTRA=$@

  title "Starting up containers..."
  docker-compose $ENV_FILE up -d $EXTRA
}

down() {
  title "Stopping and removing containers..."
  docker-compose $ENV_FILE down --volumes --remove-orphans
  docker-compose $ENV_FILE rm -f
  echo -e "${GREEN}Containers have been stopped and removed."
}

bash_shell() {
  title "Entering bash shell..."
  docker-compose $ENV_FILE exec app bash
}

run_tests() {
  EXTRA=$@

  title "Running tests..."
  docker-compose $ENV_FILE exec app php artisan test --env=testing $EXTRA
}

case "$command" in
  install|--install)
    up --build --force-recreate
    install_dependencies
    wait_for_db
    run_dependencies
    logo
    ;;
  up|--up)
    up
    logo
    ;;
  bash|--bash)
    bash_shell
    ;;
  test|--test)
    run_tests "${@:2}"
    ;;
  down|--down)
    down
    ;;
  *)
    echo -e "${RED}Invalid command. Use ${YELLOW}(install|up|bash|test|down)${NC}"
    exit 1
    ;;
esac

exit 0
