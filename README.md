# Todo list based on API platform

## Backend:

This exercise makes use of the API-platform framework as backend:

https://api-platform.com

It's a platform specializing in API requests made on top of Symfony 4.3:

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

//Install Backend Dependencies:
docker-compose exec php composer install

//Install Frontend dependencies:
docker-compose exec client yarn install

//Create database schema:
docker-compose exec php bin/console doctrine:schema:update --force

//Load fixtures:
docker-compose exec php bin/console hautelook:fixtures:load -q
```

You should now be able to access the web app at:

```https://localhost/```

And can directly communicate with the API at:

```https://localhost:8443/```

## Accessing App from a VM / Remote Server

If the App is running inside a VM and you want to access it from the host machine, you need to do the following things:

Assuming you usually access the VM under the domain ```local.test```:

- Make the file ```api/.env.local```

And inside put the following lines:

```
TRUSTED_HOSTS='^localhost|api|local.test$'
CORS_ALLOW_ORIGIN=^https?://localhost(:[0-9]+)?|https?://local.test(:[0-9]+)?$
```

This will tell the API backend to trust requests with local.test as host header and allow CORS requests coming from it.

- In the project root make the file: ```docker-compose.override.yml```

And put the following lines inside:

```
services:
  client:
    environment:
      - REACT_APP_API_ENTRYPOINT=https://local.test:8443
```

This will set the entrypoint of the React API calls to the custom domain instead of localhost

If you had already started containers, restart them:

```
docker-compose down && \
docker-compose up && \
docker-compose exec client yarn install
```
*(the frontend dependencies aren't in a volume hence you need to run yarn install again when recreating the containers)*

Now you should be able to access the app at:

```https://local.test```

And the API backend at:

```https://local.test:8443```

## Accessing App over HTTP instead of HTTPS

Because this is just a quick test app, it doesn't include a valid SSL certificate. That's why there will be a warning from the browser stating that the connection is unsafe when opening the website. I usually just ignore the warning and continue, but for some older browsers that might not be enough to get it to work.
To make the api make regular HTTP calls instead of HTTPS, make the file: ```docker-compose.override.yml```

And put the following lines inside:

```
services:
  client:
    environment:
      - REACT_APP_API_ENTRYPOINT=http://localhost:8080
```

This will make the app make calls to port 8080 which is a regular HTTP port of the api back-end.

Afterwards you should be able to visit  http:localhost, and everything should work normally.

## Querying backend

Check ```https://localhost:8443/``` for all API endpoints.

Example querying the backend directly using curl:

Will retrieve Todo List with ID 1:

```
curl -kX GET "https://localhost:8443/todo_lists/1" -H "accept: application/ld+json"
```

## Running Tests

Run the following command to start the PHPunit tests:

```
docker-compose exec php bin/phpunit
```

There are no unit tests as there was no custom code needed (besides the entities/api models) to make the API backend. But I wrote functional tests for both the TodoList and TodoTask API endpoints.
You can find them under ```api/tests```