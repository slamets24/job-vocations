# Ayo Berkarier

The purpose of this project is to assist job seekers in easily finding their dream jobs without needing to rely on traditional methods, such as directly visiting companies with open job vacancies. This application was developed using Laravel v11 and requires at least PHP v8.2 and Node.js v20. If errors or bugs occur during installation or use, they might result from using an unsupported PHP version.

## Tech Stack

-   **Client :** Tailwind, Inertia Vue Js
-   **Server :** PHP with Laravel

## Run Locally

Clone the project

```bash
git clone https://github.com/Nameless-ID/job-vacations.git
```

Or Download ZIP

[Link](https://github.com/Nameless-ID/job-vacations/archive/refs/heads/main.zip)

Go to the project directory

```bash
cd job-vacations
```

### Create Database

Ensure that you have created a database named "job_vacations". Don’t worry, this database name can be changed later to suit your preferences. However, during the initial setup, the database must be named as such because we have configured it that way.

### Run the command

```bash
composer setup
```

### Setup Mail at .env

The application cannot run until you configure Mail settings in your .env file. Make sure you’ve set up Mail correctly so your application can send emails. Here’s an example configuration:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=bfvitddrgdethaoa
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="Your App Name"
```

Adjust these settings according to your own configuration. Once again, don’t forget!

### Run the application, make sure the web server is running

```bash
// If you are using Apache, enter the following URL in your web browser:
http://job-vacations.test

// If you are using Nginx, enter the following URL in your web browser:
http://job-vacations.test:8080
```

Another way to run the application is to use the command php artisan serve. Take the URL from the command output and enter it in your web browser

## Warning!!

I will make this repository private again, so please make sure to clone it first.
