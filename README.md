# Sancartonfix

[![CI](https://github.com/ghost-brigade/sancartonfix/actions/workflows/ci.yml/badge.svg)](https://github.com/ghost-brigade/sancartonfix/actions/workflows/ci.yml)

## Description

Sancartonfix is a web application for the challenge IW5 2023.

## How to use

1. Clone the repository.
2. Run `docker-compose up -d` to start the applications.

### Backend

1. Run `cd api`
2. Run `make db` to create the database.
3. Run `make seed` to load fixtures.
4. Run `make jwt` to generate a JWT key.

### Frontend
1. Run `cd front`
1. Copy the `.env.example` file to `.env`.
2. Run `npm install` to install dependencies.
3. Run `npm run dev` to start the application.

Access to the **API** : https://localhost

Access to the **front** : http://localhost:5173

## Default users

* Administrateur : admin@localhost / password
* Vendeur : user@localhost / password

## Public access

The API is hosted at https://api.sancartonfix.mimso.net/

The application is hosted at https://sancartonfix.mimso.net/

## Contributors

* **Maxime Carluer** - [github](https://github.com/maximecarl)
* **Anthony Arjona** - [github](https://github.com/anthonyarjona)
* **Louis Moulin** - [github](https://github.com/MoulinLouis)
* **Julien Arbellini** - [github](https://github.com/JulienArbellini)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
