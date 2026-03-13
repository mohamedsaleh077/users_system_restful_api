# PHP Auth API

A vanilla PHP 8.x REST API for user authentication — JWT tokens, HttpOnly cookies, custom MVC router, and Docker-based deployment.

## What It Does

- User registration and login
- Session validation
- JWT-based authentication via `Authorization` header or secure `HttpOnly` cookie
- Works with any frontend or mobile client

## Tech Stack

| Layer          | Technology                      |
|----------------|---------------------------------|
| Backend        | PHP 8.x (strict types)         |
| Database       | MySQL 8                        |
| Auth           | JWT (JSON Web Tokens)          |
| Infrastructure | Docker + Docker Compose        |
| Security       | bcrypt, HttpOnly cookies, SameSite |
| Architecture   | Custom MVC (OOP)               |

## Project Structure

```
.
├── website/
│   ├── app/
│   │   ├── Controllers/    # HTTP request handlers
│   │   ├── Core/           # Router, App, JWT, View
│   │   ├── Models/         # Database layer
│   │   ├── Units/          # Business logic (User, Uploaders)
│   │   └── Traits/         # Shared helpers (APIHelper, Errors)
│   └── public/
│       └── index.php       # Entry point
├── db/                     # Schema initialization
└── docker-compose.yml
```

## Request Flow

```
Client → index.php → App → Router → Controller → Unit/Model → JSON Response
```

## Routing

All requests pass through `public/index.php`. The URL is parsed as:

```
/controller/action/parameters
```

Examples:

```
POST /user/signup
POST /user/login
POST /user/isloggedin
```

---

## Authentication

The API supports two authentication methods simultaneously:

### Bearer Token

Send the JWT in the `Authorization` header. Best for mobile apps, external clients, and API testing tools.

```
Authorization: Bearer <jwt>
```

### HttpOnly Cookie

The server sets a `Token` cookie on login. Best for web applications.

| Property | Value     |
|----------|-----------|
| HttpOnly | `true`    |
| SameSite | `Lax`     |
| Secure   | `false` (set to `true` in production) |

This mitigates XSS and CSRF attacks.

---

## API Reference

### `POST /user/signup`

Create a new account.

**Request:**

```json
{
  "username": "dev_user",
  "email": "user@example.com",
  "password": "securepassword"
}
```

**Validation:**

| Field    | Rule                                  |
|----------|---------------------------------------|
| username | 3–50 chars, alphanumeric plus `._-`   |
| email    | RFC-compliant format                  |
| password | Minimum 6 characters                  |

**Response** `200 OK`:

```json
{
  "ok": 1,
  "errors": [],
  "jwt_token": "eyJhbGciOi..."
}
```

---

### `POST /user/login`

Authenticate with username or email.

**Request:**

```json
{
  "username_email": "dev_user",
  "password": "securepassword"
}
```

The `username_email` field accepts either a username or an email address.

**Response** `200 OK`:

```json
{
  "ok": 1,
  "user": {
    "id": 1,
    "username": "dev_user",
    "email": "user@example.com"
  },
  "jwt_token": "eyJhbGciOi..."
}
```

The server also sets:

```
Set-Cookie: Token=<jwt>; HttpOnly; SameSite=Lax
```

---

### `POST /user/isloggedin`

Check if the current session is valid. Useful for SPA auth checks and page reloads.

Provide either the `Authorization: Bearer <jwt>` header or the `Token` cookie.

**Response** `200 OK`:

```json
{
  "ok": 1,
  "user": {
    "id": 1,
    "username": "dev_user",
    "email": "user@example.com"
  }
}
```

---

## Error Format

All errors follow a consistent structure:

```json
{
  "ok": 0,
  "message": "Error description",
  "errors": {
    "field_name": ["Specific error message"]
  }
}
```

### Status Codes

| Code | Meaning                |
|------|------------------------|
| 200  | Success                |
| 400  | Bad Request            |
| 401  | Unauthorized           |
| 405  | Method Not Allowed     |
| 415  | Unsupported Media Type |
| 422  | Validation Error       |

---

## Security

- **Passwords** — hashed with `password_hash()` (bcrypt) and verified with `password_verify()`
- **Input validation** — regex filters, length checks, email format validation
- **SQL injection** — all queries use prepared statements via a custom QueryBuilder
- **URL sanitization** — the router strips malicious characters from incoming URLs

Username regex example:

```
/^[a-zA-Z0-9._-]{3,50}$/
```

---

## Getting Started

**1. Clone the repo:**

```bash
git clone <repo-url>
cd <project-dir>
```

**2. Configure environment:**

```bash
cp website/.env.example website/.env
```

Edit `website/.env` with your database credentials.

**3. Start the containers:**

```bash
docker-compose up --build
```

**4. Access:**

| Service    | URL                     |
|------------|-------------------------|
| API        | http://localhost        |
| phpMyAdmin | http://localhost:8081   |

**5. Test it:**

```bash
curl -X POST http://localhost/user/signup \
  -H "Content-Type: application/json" \
  -d '{"username":"dev_user","email":"dev@mail.com","password":"securepass"}'
```

---

## Known Issue

There is a bug in the login controller — the password verification condition is inverted:

```diff
- if(password_verify($this->post["password"], $this->userData["results"]["password_hash"])){
+ if(!password_verify($this->post["password"], $this->userData["results"]["password_hash"])){
      $this->results["errors"]["password"][] = "Invalid Password.";
  }
```

Without the `!` negation, every login attempt will fail. Fix this before using in production.
