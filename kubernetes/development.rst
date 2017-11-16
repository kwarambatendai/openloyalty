======================================================================
Developing Kubernetes
======================================================================
How to develop and test application on Kubernetes.

*********************************
Requirements
*********************************
- Docker - `go to official site <https://www.docker.com>`_

- Minikube - `go to installation guideline <https://kubernetes.io/docs/getting-started-guides/minikube/>`_


======================================================================
Architecture
======================================================================
*********************************
Base images
*********************************
Final images use the base images built previously.

- The definitions are located in `docker/base` and there are three files: `nginx-dockerfile`, `nodejs-dockerfile` and `php-fpm-dockerfile`.

- You can build them on your own running script ``docker/base/build.sh``

- This script build ready to use images with minimal requirements provided. There will be used by building final images as a base image.

*********************************
Production images
*********************************
These images are used by kubernetes to provide services basing on running containers.

- Definitions are located in `docker/prod`: `php/fpm-dockerfile`, `web/app-dockerfile`

fpm-dockerfile
===============
- uses multistage architecture, two stages: 1. composer 2. base-php-fpm
- installs supervisord, crontab, and phing.
- copies backend api's source code.
- sets permissions for some directories
- runs composer install and initializing scripts
- keeps container running with php-fpm and cron in foreground as a command ``init.sh``

app-dockerfile
===============
- uses multistage with two stages: 1. compiling angular frontend application 2. uses base-nginx
- compiled frontend application is moved to second stage's filesystem from previous stage.
- front controller from backend api is copied and served from nginx
- nginx server definitions are copied: `backend.conf` and `frontend.conf`
- `backend.conf` contains a definition of typical Symfony nginx configuration with `fastcgi_pass php:9000;` describing php-fpm service described above.
- `frontend.conf` describes 3 front applications on different ports: admin, client and pos.

Other images
===============
Other images are descibed simply in `docker/docker-compose.yml`: elasticsearch, mailhog, postresql.


======================================================================
How to run
======================================================================

*********************************
Starting application
*********************************
1. run ``minikube start`` command (only locally)
2. after that you can reach a dashboard panel with command: ``minikube dashboard`` (only locally)
3. being in `kubernetes` directory or directory with kubernetes configuration run these commands in following order:
    - ``kubectl apply -f storage.yml``
    - ``kubectl apply -f claims.yml``
    - ``kubectl apply -f config.yml``
    - ``kubectl apply -f deployment.yml``
4. After the last command your application should be available on dashboard in `Overview` section and namespace `Openloyalty`


***********************
Using private registry
***********************
If you are using only private repository to provide docker images you also may need to authorize an access to this repository.


1. You can do it with this command:

``kubectl create secret docker-registry registry --docker-server=<server_name:port> --docker-username=<username>--docker-password=<password> --docker-email=example@example.com --dry-run -o yaml``

2. Now you can save output ``*yml`` file somewhere on host and apply to kubernetes with command: ``kubectl apply -f registry.yml``

then you can apply deployment configuration once again with registered and authorized access to private repository.