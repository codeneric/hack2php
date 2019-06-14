#!/usr/bin/env bash
docker rm -f HHVM 2&> /dev/null
docker run --rm -p 8999:8999 -d -t -v $(pwd):$(pwd) --name HHVM codeneric/custom-transpiler 
docker exec -d HHVM hh_server "$(pwd)" 