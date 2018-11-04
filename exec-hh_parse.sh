#!/usr/bin/env bash
docker exec -it -w $(pwd) HHVM hh_parse  "$@" 