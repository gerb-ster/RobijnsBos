#!/usr/bin/env bash

# enable port-forwarding for port 7040, to allow Dusk Test /w Selenium to work
socat TCP-LISTEN:7041,fork TCP:frontend:7041 &

# run apache in foreground
apache2-foreground
