# Introduction

This repository implements the standalone API server.

It currently includes a web-interface, which will be extracted into it's own repository at a later stage.

The server is very configurable, allowing you to select various types of storage backends.

# Installation

## Configuration

    cp config.yml.dist config.yml
    
Edit your `config.yml` to match your preferences.

## Install dependencies 

    composer install
    
## Starting server

    cd web
    php -S 0.0.0.0:9321

now open 127.0.0.1:9321 in your browser
