# Employee Dashboard

## Demo
https://github.com/user-attachments/assets/9aabf9d1-e93c-4a18-9cf7-3f7ef30a0941

## Tech Stack
- PHP 8.2
- MySQL 8
- Apache (via Docker)
- Composer (dependency management)
- PHPUnit (unit testing)
- HTML, CSS, JavaScript (Vanilla frontend)

## Instructions
### Clone the repo:
```bash
git clone https://github.com/hsingh124/employee-dashboard.git
cd employee-dashboard
```

### Start the app with Docker Compose:
```bash
docker-compose up --build
```

### Open the app:
Visit [localhost:8080](http://localhost:8080/) on your browser to view the app.
> Note: The first run may take a few seconds while MySQL initializes.

### Unit tests:
You can run tests in two ways:

#### Locally
```bash
./vendor/bin/phpunit
```
#### Inside Docker
```bash
docker-compose exec web ./vendor/bin/phpunit tests/
```
> Note: This app does not have full code coverage. I have added some basic unit tests.
