Feed Reader demo
====================

## Requirements

To run the project, Docker (depends on your operating system) and [Docker compose](https://docs.docker.com/compose/install/) should be available on your computer.

## Run the project
There are some helper scripts to deliver smoother experience, but they require `sh`-like environment (e.g. `Bash`, `zsh`).

The way to use scripts (this will take a while):

```bash
./start.sh
```

After all steps are finished, wait Docker containers to start and execute following:

```bash
./run_migrations.sh
```

To run tests launch the following command:
```bash
./run_tests.sh
```

If it does not work for you for some reason, do the following steps in your console manually:

1. Build the project:
```shell script
docker-compose build
docker-compose up
```

2. Run migrations:
```shell script
docker exec feed_php bin/console doctrine:migrations:migrate
```
3. Run tests:

```shell script
docker exec feed_php bin/phpunit
```

4. Launch the project

Go to http://localhost:4210/

### Technologies used

#### Infrastructure
- DB: MySQL 8
- PHP 7.4
- Nginx

#### Languages and frameworks

- Angular7 + TypeScript
- PHP + Symfony5
- PHPUnit


## Trade-offs, assumptions, simplifications

- Sensitive data are stored in project (like DB password for Docker setup). In production, all this should be done through special utilities like `helm` for Docker or `Chef` for hardware setup.
- Backend is not fully covered with all possible tests. Frontend is not covered with tests at all.
- PHP session is used for authentication(Any token authentication system should be used instead).
- No API docs generators ([NelmioApiDocBundle](https://symfony.com/doc/current/bundles/NelmioApiDocBundle/index.html) can be used).
- Not a real REST project ([API Platform](https://api-platform.com/) or [FOSRestBundle](https://symfony.com/doc/master/bundles/FOSRestBundle/index.html) can be used instead.
- No pre-commit hooks with Code style validation and static analysis added (I would use [GrumPHP](https://github.com/phpro/grumphp)) for that)
