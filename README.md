## Getting Started
This is an example of how you may give instructions on setting up your project locally. To get a local copy up and running follow these simple example steps.

### Prerequisites

* [docker](https://www.docker.com/)

### Installation

1. Clone the repo
   ```sh
   git clone git@github.com:ghost-brigade/miniPA-S1.git
   ```
2. Build the docker image
   ```sh
   docker-compose build
   ```
3. Run the docker image
   ```sh
   docker-compose up -d
   ```   
4. Install composer dependencies
   ```sh
   docker-compose exec php composer install
   ```
5. Install database
   ```sh
   docker-compose exec php bin/console doctrine:database:create
   docker-compose exec php bin/console doctrine:migrations:migrate
   ```

