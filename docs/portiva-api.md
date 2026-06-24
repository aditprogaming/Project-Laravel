# Portiva API

Base URL: `/api/portiva`

Authentication: session-based via the existing Portiva login, so browser requests must be made from the same app session.

## Endpoints

### `GET /profiles`
Returns all portfolio records with user data.

### `POST /profiles`
Creates a new portfolio for the logged-in user.

Required fields: `name`, `profession`, `about`, `skills`, `experience`, `contact`.

Optional fields: `template`, `photo`, `use_for_account`.

### `GET /profiles/{id}`
Returns one portfolio record.

### `PUT /profiles/{id}`
Updates an existing portfolio owned by the logged-in user.

### `DELETE /profiles/{id}`
Deletes an existing portfolio owned by the logged-in user.

### `GET /account`
Returns the current account summary and owned portfolios.

### `GET /users`
Returns all users for admin sessions only.

## Response Shape

All successful responses use:

```json
{
  "success": true,
  "message": "...",
  "data": {},
  "redirect_to": "/path"
}
```

Errors use:

```json
{
  "success": false,
  "message": "..."
}
```

## Frontend Usage

The Portiva portfolio form submits to the API with fetch and falls back to the normal web route if JavaScript is unavailable.