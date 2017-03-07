FROM node:5

RUN apt-get update \
    && apt-get install -y ruby-full rubygems \
    && gem instal sass
RUN npm install -g gulp node-sass yarn
COPY . /var/www
WORKDIR /var/www
EXPOSE 3000

CMD npm rebuild node-sass && yarn install && gulp config --prod && gulp compile && gulp prod