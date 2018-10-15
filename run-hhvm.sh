#!/usr/bin/env bash
docker rm -f HHVM 2&> /dev/null
docker run --rm -d -t -v $(pwd):$(pwd) --name HHVM hhvm/hhvm
docker exec -d HHVM hh_server "$(pwd)"