# Open loyalty

Open Loyalty is technology for loyalty solutions.
It's a loyalty platform in open source, with ready-to-use gamification and loyalty features, easy to setup and customize, ready to work on-line and off-line.

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

That's all. Now you can go to [127.0.0.1:8181](http://127.0.0.1:8181).

If you are developer and want to attach source code then:

```
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up
```

## Documentation

Technical documentation is located [here](backend/doc/index.rst).