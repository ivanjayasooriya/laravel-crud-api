# Laravel CRUD API

A REST API built with Laravel for managing customers, inventory items, and customer orders. Orders contain one or more items; creating an order checks and reduces inventory, and deleting an order restores it.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm (only needed to build the frontend assets)

The default database connection is SQLite. MySQL and other Laravel-supported databases can be configured through `.env`.

## Setup

1. Install PHP dependencies:

   ```sh
   composer install
   ```

2. Copy `.env.example` to `.env` (`copy .env.example .env` in Windows Command Prompt, or `cp .env.example .env` on macOS/Linux).
3. Generate the application key:

   ```sh
   php artisan key:generate
   ```

4. Create the SQLite database file if it does not exist (`New-Item -ItemType File -Force database\database.sqlite` in PowerShell, or `touch database/database.sqlite` on macOS/Linux).
5. Run the migrations:

   ```sh
   php artisan migrate
   ```

6. Start the API:

   ```sh
   php artisan serve
   ```

The API is available at `http://localhost:8000/api`. Import [Laravel CRUD API.postman_collection.json](./Laravel%20CRUD%20API.postman_collection.json) into Postman to try the requests.

## Authentication

This API uses Laravel Sanctum for stateless API authentication. Public authentication endpoints are:

- `POST /api/register` - register a new user and create an API token
- `POST /api/login` - log in an existing user and create an API token
- `POST /api/logout` - revoke the current token
- `GET /api/me` - fetch the authenticated user
- `GET /api/user` - alias for the authenticated user, also backed by Sanctum

After a successful login or registration, include the returned token in the `Authorization` header for protected requests:

```http
Authorization: Bearer <token>
```

### Register a user

```http
POST /api/register
Content-Type: application/json
```

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

### Log in

```http
POST /api/login
Content-Type: application/json
```

```json
{
  "email": "ada@example.com",
  "password": "secret123"
}
```

The login endpoint is rate-limited to 5 requests per minute to reduce brute-force attacks.

## API

All request and response bodies use JSON. The list endpoints return the 10 most recently created records per page, with Laravel pagination metadata.

| Resource | List | Create | Read, update, or delete |
| --- | --- | --- | --- |
| Auth | - | `POST /api/register`, `POST /api/login` | `POST /api/logout`, `GET /api/me` |
| Customers | `GET /api/customers` | `POST /api/customers` | `GET`, `PUT`, `PATCH`, or `DELETE /api/customers/{id}` |
| Items | `GET /api/items` | `POST /api/items` | `GET`, `PUT`, `PATCH`, or `DELETE /api/items/{id}` |
| Orders | `GET /api/orders` | `POST /api/orders` | `GET`, `PUT`, `PATCH`, or `DELETE /api/orders/{id}` |

Use `?page=2` to request another page. The customer, item, and order CRUD routes are protected by Sanctum authentication and require a valid bearer token. `GET /api/user` also requires a valid Sanctum-authenticated request.

### Customers

Create a customer with `POST /api/customers`:

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com",
  "phone": "+1-555-0100",
  "address": "123 Example Street"
}
```

`name` and a unique, valid `email` are required. `phone` and `address` are optional.

### Items

Create an inventory item with `POST /api/items`:

```json
{
  "name": "Notebook",
  "description": "A lined notebook",
  "price": 8.5,
  "stock": 25
}
```

`name`, `price` (at least `0.01`), and non-negative integer `stock` are required. `description` is optional. An item cannot be deleted while it is referenced by an order; the API responds with `409 Conflict`.

### Orders

Create an order with `POST /api/orders`:

```json
{
  "customer_id": 1,
  "order_date": "2026-11-03",
  "status": "pending",
  "items": [
    {
      "item_id": 1,
      "quantity": 2
    }
  ]
}
```

`customer_id`, a valid `order_date`, and at least one item are required. Each item must reference an existing item, have a positive integer `quantity`, and appear only once in the order. `status` is optional and defaults to `pending`; allowed values are `pending`, `processing`, `completed`, and `cancelled`.

The API calculates `total_amount` from the current item prices and records those prices on the order. Order creation is transactional and fails if there is not enough stock. Deleting an order restores its item quantities to inventory. Updating an order changes only its customer, date, and status; its items are not editable through the update endpoint.

## Responses and errors

- Successful create requests return `201 Created`.
- Invalid input returns Laravel's validation response (`422 Unprocessable Content`).
- Requests for missing records return `404 Not Found`.
- Deleting an item referenced by an order returns `409 Conflict`.
- Delete requests return a JSON message confirming the deletion.

## Tests

Run the test suite with:

```sh
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
