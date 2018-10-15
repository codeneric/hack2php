#!/usr/bin/env bash
docker exec -it -w $(pwd) HHVM hhvm "$@" 