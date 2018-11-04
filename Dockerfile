
FROM php:5.6 

ARG HHVM_PACKAGE
ARG HHVM_REPO_DISTRO=xenial
ENV HHVM_DISABLE_NUMA true

# RUN apt-get update -y && apt-get install -y software-properties-common apt-transport-https 
# RUN add-apt-repository ppa:ondrej/php || true
# RUN apt-get update -y 
# RUN apt-get install -y --allow-unauthenticated php5.6

RUN \
    apt-get update -y \
    && apt-get install -y apt-transport-https software-properties-common gnupg \
    && apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xB4112585D386EB94 \
    && add-apt-repository https://dl.hhvm.com/debian \
    && apt-get -y update \
    && apt-get install -y hhvm 

