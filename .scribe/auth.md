# Authenticating requests

The authentication system uses **Laravel Sanctum** tokens.  
Users must first register or log in to obtain an **API token**, which must be included in subsequent requests via the `Authorization` header.

#### Headers Example
```http
Authorization: Bearer your_api_token_here
Accept: application/json
```

### Authentication API

**Base route:** `/api`

| Method | Endpoint         | Description                     | Auth Required |
|--------|------------------|----------------------------------|--------------|
| POST   | `/api/login`     | Log in and receive an API token  | No           |
| POST   | `/api/logout`    | Log out and revoke the token     | Yes          |

---

## Protected API Endpoints

All routes require authentication (`auth:sanctum` middleware) and each request should include an authentication token obtained from the `/api/login` endpoint.

Use the `Authorization` header with the following format:

```http
Authorization: Bearer {YOUR_TOKEN_HERE}
```


