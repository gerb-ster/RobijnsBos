#!/usr/bin/env bash

# check if .env file exists, if not this is a initial install, so install everything
if [ ! -f "/var/www/.env" ]; then
    # copy .env file
    echo "⭐️ Copy .env file";
    cp /var/www/docker/docker.env /var/www/.env

    # move to webroot directory
    cd /var/www

    # run composer
    echo "⭐️ Run composer install";
    composer install

    # make sure folder permissions are set
    echo "⭐️ Set folder access Laravel";
    chown www-data /var/www/storage -R
    chmod a+w -R /var/www/storage
    chmod a+w -R /var/www/vendor

    # run laraval's Mix
    echo "⭐️ Run NPM install";
    npm install
    npx mix

    # run artisan migrate & seed
    echo "⭐️ Run artisan migrate";
    php artisan migrate --seed
else
    # run composer
    echo "⭐️ Run composer update";
    composer update

    # run laraval's Mix
    echo "⭐️ Run NPM install";
    npm install
    npx mix
fi

# enable port-forwarding for port 7040, to allow Dusk Test /w Selenium to work
socat TCP-LISTEN:7041,fork TCP:frontend:7041 &

# run apache in foreground
apache2-foreground
