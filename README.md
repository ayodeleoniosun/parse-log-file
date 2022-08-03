# Legal One Backend Assessment

This repository is a solution to the assessment test as provided [Here](/devbox/resources/.assessment.md)

# Getting Started

- Development Requirements
- Installation
- Starting Development Server
- Documentation
- Testing

## Development Requirements

This application currently runs on `Symfony 5.4.11` and the development requirements to get this application up and
running are as follows:

- PHP 8.1+
- Docker (If preferred)
- MySQL
- redis
- git
- Composer

## Installation

### Step 1: Clone the repository

```bash
git clone https://github.com/ayodeleoniosun/legal-one-backend-assessment.git
```

### Step 2: Switch to the repo folder

```bash
cd legal-one-backend-assessment/devbox
```

### Step 3: Install all composer dependencies

```bash
composer install
```

#### Step 4: Setup environment variable

- Copy `.env.example` to `.env.local` i.e `cp .env.example .env.local`
- Copy `.env.example` to `.env` i.e `cp .env.example .env`
- Update other variables as needed

### Step 5: Run database migrations

You can choose to run this app within or without docker container

#### Running migrations without docker container

By default, the value for `DATABASE_URL` in the `.env.local`, `env.test` and `.env` file is for docker configurations.

To run the migrations without docker, update the `DATABASE_URL` to this value in three `.env` files

```bash
mysql://root:@127.0.0.1:3306/legal_one
```

#### Create database

```bash
php bin/console doctrine:database:create
```

#### Create database for testing

```bash
php bin/console doctrine:database:create --env=test
```

#### Run migrations

```bash
php bin/console doctrine:migrations:migrate
```

#### Run migrations for testing

```bash
php bin/console doctrine:migrations:migrate --env=test
```

###

### Running migrations in docker container

To run the migrations within docker, update the `DATABASE_URL` to this value (provided it was changed while running
without docker containers)

```bash
mysql://root:root@devbox-mysql:3306/legal_one
```

#### Build docker

```bash
docker-compose up --build
```

#### Run docker containers

```bash
make run && make install
```

After running the above commands, the app will start running on port `9002`

#### Enter container CLI to run commands

```bash
make enter
```

#### Create database

```bash
php bin/console doctrine:database:create
```

#### Create database for testing

```bash
php bin/console doctrine:database:create --env=test
```

#### Run migrations

```bash
php bin/console doctrine:migrations:migrate
```

#### Run migrations for testing

```bash
php bin/console doctrine:migrations:migrate --env=test
```

#### Exit container CLI to run other commands

```bash
exit
```

## Starting Development Server

After the installation of the packages and running migrations, then, it's time to start the development server.

Development server can be started in two ways:

- Using `symfony server:start`
- Using Docker as highlighted above

I recommend using docker to start the development server to ensure that the application works perfectly across all
developers' machines regardless of their operating systems.

### API Documentation

Kindly refer to the documentation here to test the `count` endpoint

### Testing

To run tests without docker containers:

```bash
php bin/phpunit
```

To run tests within docker containers:

#### Enter container CLI to run commands

```bash
make enter
```

#### Run tests

```bash
php bin/phpunit
```

##

### `Entdecken! Explore!`
