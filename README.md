[![Build Status](https://travis-ci.org/DivanteLtd/open-loyalty.svg?branch=master)](https://travis-ci.org/DivanteLtd/open-loyalty)

# Open Loyalty

Open Loyalty is technology for loyalty solutions.
It's a loyalty platform in open source, with ready-to-use gamification and loyalty features, easy to set up and customize, ready to work on-line and off-line.

See Open Loyalty product tour - https://youtu.be/cDZZemHxgAk.


## Business applications

There is variety of applications for Open Loyalty. Based on it you can build loyalty solutions like: loyalty modules for eCommerce, full loyalty programs for off-line and on-line, motivational programs for sales department or customer care programs with mobile application.

## Screenshots

![Dashboard](https://cloud.githubusercontent.com/assets/26326842/24359309/428f7dc4-1304-11e7-99c2-36ff23fe5036.png)
![Client Cockpit](https://cloud.githubusercontent.com/assets/26326842/24359396/7f489fd4-1304-11e7-9ae5-f05c88eb8c56.png)
![eCommerce Cockpit](https://cloud.githubusercontent.com/assets/26326842/24359495/d65c1210-1304-11e7-86bf-9e63ab754360.png)
![POS Cockpit](https://cloud.githubusercontent.com/assets/26326842/24359465/b796e260-1304-11e7-9da5-4bfc0a026a16.png)

## Quick install

This project has full support for running in [Docker](https://www.docker.com/>).

Execute bellow command to run application: 

```
docker-compose up
```

Then use another command to setup database, elastcsearch and load some demo data:

```
docker-compose exec backend phing setup
```

That's all. Now you can go to admin panel [127.0.0.1:8182](http://127.0.0.1:8182).
Default login is **admin** and password **open**. You can also go to customer panel [127.0.0.1:8183](http://127.0.0.1:8183).

If you are developer and want to attach source code then:

```
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up
```

## Documentation

Technical documentation is located [here](backend/doc/index.rst).
