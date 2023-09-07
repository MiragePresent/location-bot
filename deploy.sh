#!/bin/bash

# Script options
PROJECT_ROOT=$1
BOT_WEBHOOK=$2
COMPOSE_FILE="$PROJECT_ROOT/docker-compose.yml"
SYNC_GEO=0
SYNC_CHURCHES=0
PATCH_ADDR=0
VERBOSE=0
QUIET=0
INVALID_INPUT=0

while [[ $# -gt 0 ]]; do
  case $1 in
    -g|--sync-geo)
      SYNC_GEO=1
      shift
      ;;
    -c|--sync-churches)
      SYNC_CHURCHES=1
      shift
      ;;
    -a|--patch-addresses)
      PATCH_ADDR=1
      shift
      ;;
    -v)
      VERBOSE=1
      shift
      ;;
    -q)
      QUIET=1
      shift
      ;;
    -*|--*)
      INVALID_INPUT=1
      shift
      ;;
    *)
      shift # past argument
      ;;
  esac
done

function printVerbose() {
    if [ "$QUIET" == "0" ]; then 
        if [ "$VERBOSE" == "1" ]; then 
            echo "$1"
        fi 
    fi
}

function validateComposeFile() {
    if [ ! -f "$COMPOSE_FILE" ]; then 
        echo "Compose file [$COMPOSE_FILE] is not found"
    fi
}

printVerbose "Deploying options:"
printVerbose "Project root: $PROJECT_ROOT"
printVerbose "Sync Geo: $SYNC_GEO"
printVerbose "Sync Churches: $SYNC_CHURCHES"
printVerbose "Patch Addresses: $PATCH_ADDR"
printVerbose ""

validateComposeFile "$COMPOSE_FILE"

# Stopping containers...
if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        --ansi never \
        --progress quiet \
        -q stop
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        --progress plain \
        --ansi never \
        stop
fi

# Rebuilding images...
if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        --ansi never \
        --progress quiet \
        -q build
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        --ansi never \
        --progress plain \
        build
fi

# Starting containers...
if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        --ansi never \
        --progress quiet \
        up -d
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        --ansi never \
        --progress plain \
        up -d
fi

# Installing dependencies
if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        -q \
        exec \
        -u www-data:www-data \
        lumen \
        composer install -q --optimize-autoloader --no-dev --no-ansi
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        exec \
        -u www-data:www-data \
        lumen \
        composer install --optimize-autoloader --no-dev --no-ansi
fi

# Apply migrations
if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        -q \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan -q migrate --no-ansi --force
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan migrate --no-ansi --force
fi

# Sync geography (create regions and cities from .csv file)
if [ "$SYNC_GEO" == "1" ]; then
    if [ "$QUIET" == 1 ]; then  
        docker-compose \
            -f "$COMPOSE_FILE" \
            -q \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan -q sync:geography --no-ansi
    else 
        docker-compose \
            -f "$COMPOSE_FILE" \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan sync:geography --no-ansi
    fi
else 
    printVerbose "Sync geo is not enabled"
fi 

# Sync churges (load churches details from API)
if [ "$SYNC_CHURCHES" == "1" ]; then 
    if [ "$QUIET" == 1 ]; then  
        docker-compose \
            -f "$COMPOSE_FILE" \
            -q \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan -q sync:churches --no-ansi
    else 
        docker-compose \
            -f "$COMPOSE_FILE" \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan sync:churches --no-ansi
    fi
else 
    printVerbose "Sync churges is not enabled"
fi

if [ "$PATCH_ADDR" == "1" ]; then 
    if [ "$QUIET" == 1 ]; then  
        docker-compose \
            -f "$COMPOSE_FILE" \
            -q \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan -q patch:church:import --no-ansi
    else 
        docker-compose \
            -f "$COMPOSE_FILE" \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan patch:church:import --no-ansi
    fi
    if [ "$QUIET" == 1 ]; then  
        docker-compose \
            -f "$COMPOSE_FILE" \
            -q \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan -q locations:fix --no-ansi
    else 
        docker-compose \
            -f "$COMPOSE_FILE" \
            exec \
            -u www-data:www-data \
            lumen \
            php artisan locations:fix --no-ansi
    fi
fi

if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        -q \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan -q cache:clear --no-ansi
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan cache:clear --no-ansi
fi

if [ "$QUIET" == 1 ]; then  
    docker-compose \
        -f "$COMPOSE_FILE" \
        -q \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan -q bot:set-webhook $BOT_WEBHOOK --no-ansi
else 
    docker-compose \
        -f "$COMPOSE_FILE" \
        exec \
        -u www-data:www-data \
        lumen \
        php artisan bot:set-webhook $BOT_WEBHOOK --no-ansi
fi

