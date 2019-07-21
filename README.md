# Todo list

## Backend:

This exercise makes use of the API-platform framework as backend:

https://api-platform.com

It's a platform specializing in API requests on top of Symfony 4:

[API Documentation](./doc/APIPLATFORM.md)

To see the platform API interface, look at:

```https://localhost:8443/```

## Frontend

Front-end will be a small React app that handles the state of the TODO list.

## Installation

To run this demo you need to have docker and docker-compose (v3.4+) installed.
After installing these, go to the project root and run following commands:

```
//Pull docker images:
docker-compose pull

//Create and run containers:
docker-compose up -d

//Install App Dependencies
docker-compose exec php composer install

//Create database schema:
docker-compose exec php bin/console doctrine:schema:update --force

//Create fixtures:
docker-compose exec php bin/console hautelook:fixtures:load -v
```

You should now be able to access the web app at:

```https://localhost/```

And can directly communicate with the API at:

```https://localhost:8443/```

If you're running the server in a VM instead of the localhost, you should add the domain/IP to the trusted hosts list inside the *api/.env* file:

```TRUSTED_HOSTS='^localhost|api|myVM.test$'```

Then you can access the website & API using

```https://myVM.test/```

## Querying backend:

Check ```https://localhost:8443/``` for all API endpoints.

Example querying the backend directly using curl:

Will retrieve Todo List with ID 1:

```
curl -kX GET "https://localhost:8443/todo_lists/1" -H "accept: application/ld+json"
```


