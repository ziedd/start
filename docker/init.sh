#!/bin/bash

docker-compose -f $1 run api cd /var/app shell