# RobijnsBos Deploy

This directory contains a deploy script.

Create `deploy/.ssh-password` with the SSH password for the `robijnsbos` host before running `deploy.sh`.

The script builds into `deploy/build`, syncs each top-level directory in that folder to the matching directory on the remote host, runs `php artisan migrate` in `robijnsbos_dt_app`, and then clears `deploy/build`.
