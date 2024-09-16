
# News Aggregator API

This project is a **News Aggregator API** built with Laravel. The API allows users to manage their news preferences and fetch personalized news articles based on sources, categories, and authors. It also provides a search functionality and integrates with external news sources.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Database Setup](#database-setup)
  - [Migrations](#migrations)
  - [Seeders](#seeders)
  - [Factories](#factories)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [License](#license)

## Features

- **User Preferences**: Manage preferences for news sources, categories, and authors.
- **Personalized News Feed**: Fetch personalized news articles based on preferences.
- **Article Search**: Search for articles by keyword, category, source, and date range.
- **API Documentation**: Generated with Swagger UI.
- **Testing**: Unit tests for key functionalities.
  
## Installation

Follow the steps below to get the project running locally.

### Prerequisites

- PHP 8.x
- Composer
- MySQL or PostgreSQL
- Node.js & npm (optional for Laravel Mix)
- Postman (optional but recommended for API testing)

### Steps

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/Rohitkumark0989/news_aggregator.git
   cd news_aggregator
   ```

2. **Install Dependencies**:

   ```bash
   composer install
   npm install  # Optional, if frontend is involved
   ```

3. **Set Up the Environment**:

   Copy the `.env.example` file to create a new `.env` file:

   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**:

   Generate the application key using Artisan:

   ```bash
   php artisan key:generate
   ```

5. **Configure Environment**:

   Set up your database credentials in the `.env` file:

   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=news_aggregator_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

## Database Setup

### Migrations

Run the migrations to create the necessary tables:

```bash
php artisan migrate
```

### Seeders

To populate your database with test data, you can run the seeders:

```bash
php artisan db:seed
```

This will insert default users, articles, and other necessary records.

### Factories

Laravel Factories allow you to easily create dummy data for testing or development:

```php
User::factory()->count(5)->create();  // Create 5 users
Article::factory()->count(20)->create();  // Create 20 articles
```

## API Documentation

### Swagger UI

The API documentation is available via Swagger UI and is automatically generated. You can access it at:

```
http://localhost:8000/api/documentation
```

Swagger provides detailed information about each API endpoint, including parameters, responses, and request types.

### Important Annotations

Annotations for Swagger have been added to all controller methods. For example:

- `getPreferences()`
- `getPersonalizedNewsFeed()`
- `search()`
- `show()`

These annotations generate the documentation and enable token-based authorization using Laravel Sanctum.

## Testing

Unit testing has been set up for core functionalities. Tests are written using Laravel’s built-in testing framework, ensuring that the application behaves as expected.

### Running Tests

Run the test suite using:

```bash
php artisan test
```

This will execute all test cases and provide feedback on the functionality.

### Example Test

Here’s an example of a test case for the `search` method:

```php
public function test_search_articles()
{
    // Create dummy articles
    Article::factory()->create(['title' => 'Technology news', 'content' => 'Latest tech updates']);
    
    // Test search endpoint
    $response = $this->getJson('/api/articles/search?keyword=Technology');
    
    $response->assertStatus(200)
             ->assertJsonCount(1); // Expect one article matching the keyword
}
```

### Testing with Database

Testing uses the `RefreshDatabase` trait to ensure the database is reset before each test. You can use an SQLite database for testing purposes.

To configure a testing database, update the `.env.testing` file:

```plaintext
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## Example API Usage

Here are some example API calls you can make:

1. **Search for Articles**:
   ```
   GET /api/articles/search?keyword=technology&category=Technology&source=The+Guardian
   ```
   
2. **Fetch Personalized News Feed** (Requires Authentication):
   ```
   GET /api/user/news-feed
   ```

3. **Get User Preferences** (Requires Authentication):
   ```
   GET /api/user/preferences
   ```

## License

This project is licensed under the MIT License.
