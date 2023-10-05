# CSV data reader example (Vue 3 + PHP)

## Getting started
Add DB credentials to environmemt variables in .htaccess

## Building the app and starting the container for serving it
```bash
$ git clone git@github.com:pikil/csv-reader-example.git
$ docker build -t csv-data-reader . && docker run -p 8089:80 csv-data-reader
```

## Stopping the container (when there are multiple containers are running)
```bash
$ docker ps
$ docker stop <_container_id_>
```

## Stopping the container (when there is only one container is running)
```bash
$ docker stop $(docker ps -q)
```
