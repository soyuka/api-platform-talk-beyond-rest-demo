# Api Platform - Beyond REST

Slides to come.

- Apéro PHP - Lille 14/03/2019
- Api Platform Meetup - Paris 26/03/2019
...

## Requirements

- docker
- nodejs
- composer
- authbind (use apt-get or brew, it's used so that the user running pm2 can use the `80` port)
- pm2 (`npm install pm2 -g`)

## Installation

Go to the `front` directory, it's a [Next.js](nextjs.org) project (no reason behind that I just wanted to try their tools).
Just run `npm install` in there.

Go to the `api` directory and run `make`.

## Launch

/!\ Disclaimer this is NOT a production build it's a "in-development" proof of concept.

Basically:

```
docker-compose up -d
authbind --deep pm2 start pm2.json
```

/!\ Some docker services like Elasticsearch may not start properly, if that's the case the services launched by `pm2` will fail.

For example, this is needed on my computer if I want Elasticsearch to start:

```
sudo sysctl -w vm.max_map_count=262144
```

To be sure just run docker services without daemon:

```
docker-compose up
```

and make sure that everything started up.

Then use `authbind --deep pm2 start pm2.json && pm2 logs` to log the services.

`pm2` is used to start:

- a php server (`bin/console server:run`) on port `8080`
- the `bin/console messenger:consume-messages` command that handles messages
- the `nextjs` server on port `3000`
- a proxy on port `80` that facilitates the demo access on `http://localhost`

Obviously on a production environment these would just be behind `nginx` and `php-fpm` would be used as a php server.
Also `phppm` could be interesting to run the messenger handlers.
