Laravel Key-Value Store API

This project is a simple RESTful API built with Laravel for storing and retrieving version-controlled key-value pairs. This guide explains how to set up and run the project in a local development environment.
Prerequisites

Before you begin, ensure you have the following installed on your system:

    PHP 8.1 or higher

    Composer

    A local database (SQLite, MySQL, PostgreSQL, etc.)

Setup and Installation

Follow these steps to get the application running on your local machine.

1. Clone the Repository

First, clone this repository to your local machine using Git.

git clone <your-repository-url>
cd <project-directory>

2. Install Dependencies

Install the project's PHP dependencies using Composer.

composer install

3. Configure Your Environment

Copy the example environment file and generate a new application key.

cp .env.example .env
php artisan key:generate

Next, open the .env file in a text editor and configure your database connection details (e.g., DB_CONNECTION, DB_HOST, DB_DATABASE, etc.). 4. Run Database Migrations

Set up the necessary database tables by running the Laravel migrations.

php artisan migrate

5. Start the Development Server

You can now start the local development server, which will typically run on http://127.0.0.1:8000.

php artisan serve

How to Access the API

The application provides several endpoints to manage key-value data. You can use tools like Postman, Insomnia, or curl to interact with them. The base URL will be your local server address (e.g., http://127.0.0.1:8000).

1. Store a New Key-Value Pair

This endpoint creates a new version for a given key. The request body must be a JSON object with a single, dynamic key.

    Method: POST

    Endpoint: /api/object

    Body (JSON):
    A JSON object with one key. The value can be a string, number, boolean, or a JSON object/array.

    {
        "app_version": "1.2.3"
    }

    or

    {
        "feature_flags": {
            "new_dashboard": true,
            "beta_access": false
        }
    }

    Success Response (201 Created):
    Returns the creation time of the new object record.

        "Time: 11:07 AM"

2. Get the Latest Value for a Key

This endpoint retrieves the most recent value stored for a specific key.

    Method: GET

    Endpoint: /api/object/{key}

    Example URL: http://127.0.0.1:8000/api/object/app_version
    Example URL: http://127.0.0.1:8000/api/object/feature_flags

    Success Response (200 OK):
    Returns the value of object either its string or JSON.

        "1.2.3"

    OR

        {
            "new_dashboard": true,
            "beta_access": false
        }

3. Get a Historical Value for a Key

Retrieve the value for a key as it was at a specific point in time by providing a Unix timestamp.

    Method: GET

    Endpoint: /api/object/{key}?timestamp={unix_timestamp}

    Example URL: http://127.0.0.1:8000/api/object/app_version?timestamp=1727060000
    Example URL: http://127.0.0.1:8000/api/object/feature_flags?timestamp=1727060000

    Success Response (200 OK):
    Returns the value of object either its string or JSON at the given timestamp.

        "1.2.3"

    OR

        {
            "new_dashboard": true,
            "beta_access": false
        }

4. Get All Object Records

This endpoint is primarily for debugging and retrieves all records from the database.

    Method: GET

    Endpoint: /api/object/get_all_records

    Success Response (200 OK):
    Returns a JSON array of all objects.

    [
        {
            "id": 1,
            "key": "app_version",
            "value": "1.2.3",
            "created_at": 1727064420,
        },
        {
            "id": 2,
            "key": "feature_flags",
            "value": { "new_dashboard": true },
            "created_at": 1727064450,
        }
    ]

Running the Test Suite

To run the project's test suite, use the following artisan command:

php artisan test

To generate a code coverage report (requires Xdebug or PCOV to be installed), you can run:

php artisan test --coverage-html=coverage
