#!/usr/bin/env bash

HOME_DIR=/home/ubuntu/ol
USER=www-data

mkdir $HOME_DIR

echo "Copying files"
cp -R /vagrant/* $HOME_DIR

echo "Fixing permissions"
sudo chown -R $USER:$USER $HOME_DIR

# remove unnecessary files
echo "Remove unnecessary files"
sudo rm -rf $HOME_DIR/frontend/node_modules
sudo rm -rf $HOME_DIR/backend/var/*

echo "Install docker-compose"
sudo apt-get install -y python-pip
sudo pip install docker-compose

echo "//------------------------------------------"
echo "vagrant ssh (Login to vagrant using)"
echo "cd ol"
echo "docker-compose up (Fetch & build containers - may take a while)"
echo "docker-compose exec backend phing demo (Installing databases & demo data. It may take more than 10 minutes)"
echo "//------------------------------------------"
echo "Than it's ready to use!"
echo "http://localhost:8181/api - API"
echo "http://localhost:8181/doc - API DOC"
echo "http://localhost:8182/ - ADMIN"
echo "http://localhost:8183/ - CUSTOMER"
echo "http://localhost:8184/ - MERCHANT"
