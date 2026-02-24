# Laravel 12 Sanctum API — Postman Guide

> A complete guide on how to use and test the Laravel 12 Sanctum API using Postman.

---

## Table of Contents

1. [Setup Postman](#1-setup-postman)
2. [Import the Collection](#2-import-the-collection)
3. [Set the Base URL](#3-set-the-base-url)
4. [Authentication](#4-authentication)
   - [Register](#-register)
   - [Login](#-login)
   - [Get Current User](#-get-current-user-me)
   - [Logout](#-logout)
5. [Posts CRUD](#5-posts-crud)
   - [List All Posts](#-list-all-posts)
   - [Create a Post](#-create-a-post)
   - [Show a Post](#-show-a-post)
   - [Update a Post](#-update-a-post)
   - [Delete a Post](#-delete-a-post)
6. [Sending form-data vs JSON](#6-sending-form-data-vs-json)
7. [How to Set the Bearer Token](#7-how-to-set-the-bearer-token)
8. [Understanding the Response](#8-understanding-the-response)
9. [Common Errors](#9-common-errors)

---

## 1. Setup Postman

Download and install Postman from [https://www.postman.com/downloads](https://www.postman.com/downloads) if you haven't already.

Make sure your Laravel server is running:

```bash
php artisan serve
```

Your API base URL will be:
```
http://127.0.0.1:8000/api/v1
```

---

## 2. Import the Collection

1. Open Postman
2. Click **Import** (top left)
3. Select the file `Laravel-Sanctum-API.postman_collection.json`
4. Click **Import**

All endpoints will appear in your left sidebar ready to use.

---

## 3. Set the Base URL

The collection uses a variable `{{base_url}}` so you only set the URL once.

1. Click the collection name in the sidebar
2. Go to the **Variables** tab
3. Set `base_url` to:

```
http://127.0.0.1:8000/api/v1
```

---

## 4. Authentication

### ✅ Register

Create a new account.

| Setting | Value |
|---------|-------|
| Method | `POST` |
| URL | `{{base_url}}/auth/register` |
| Body | `raw → JSON` |

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Success Response `201`:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123xyz..."
  }
}
```

> 💡 Copy the `token` value — you'll need it for all protected routes.

---

### ✅ Login

Login with your credentials.

| Setting | Value |
|---------|-------|
| Method | `POST` |
| URL | `{{base_url}}/auth/login` |
| Body | `raw → JSON` |

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password"
}
```

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "2|def456uvw..."
  }
}
```

> 💡 Each login generates a **new token**. Save it for the next requests.

---

### ✅ Get Current User (Me)

Returns the currently authenticated user.

| Setting | Value |
|---------|-------|
| Method | `GET` |
| URL | `{{base_url}}/auth/me` |
| Auth | Bearer Token |

**Success Response `200`:**
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### ✅ Logout

Revokes the current token.

| Setting | Value |
|---------|-------|
| Method | `POST` |
| URL | `{{base_url}}/auth/logout` |
| Auth | Bearer Token |

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Logged out successfully",
  "data": null
}
```

> ⚠️ After logout, your token is invalid. You must login again to get a new one.

---

## 5. Posts CRUD

> 🔒 All post endpoints require a **Bearer Token**. See [How to Set the Bearer Token](#7-how-to-set-the-bearer-token).

---

### 📋 List All Posts

Returns a paginated list of your posts.

| Setting | Value |
|---------|-------|
| Method | `GET` |
| URL | `{{base_url}}/posts` |
| Auth | Bearer Token |

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Posts retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "My First Post",
        "body": "Hello from the API!",
        "status": "published",
        "created_at": "2026-02-24T03:00:00+00:00"
      }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

### ➕ Create a Post

| Setting | Value |
|---------|-------|
| Method | `POST` |
| URL | `{{base_url}}/posts` |
| Auth | Bearer Token |
| Body | `raw → JSON` or `form-data` |

**Body:**
```json
{
  "title": "My First Post",
  "body": "This is the post content.",
  "status": "published"
}
```

> `status` accepts: `published` or `draft`

**Success Response `201`:**
```json
{
  "success": true,
  "message": "Post created successfully",
  "data": {
    "id": 1,
    "title": "My First Post",
    "body": "This is the post content.",
    "status": "published",
    "created_at": "2026-02-24T03:00:00+00:00"
  }
}
```

---

### 🔍 Show a Post

Get a single post by its ID.

| Setting | Value |
|---------|-------|
| Method | `GET` |
| URL | `{{base_url}}/posts/1` |
| Auth | Bearer Token |

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Post retrieved successfully",
  "data": {
    "id": 1,
    "title": "My First Post",
    "body": "This is the post content.",
    "status": "published",
    "created_at": "2026-02-24T03:00:00+00:00"
  }
}
```

---

### ✏️ Update a Post

> ⚠️ **Important:** Postman does not support `PUT` with `form-data`. Use the method below.

#### Option A — Using `raw → JSON` (simplest)

| Setting | Value |
|---------|-------|
| Method | `PUT` |
| URL | `{{base_url}}/posts/1` |
| Auth | Bearer Token |
| Body | `raw → JSON` |

```json
{
  "title": "Updated Title",
  "body": "Updated content.",
  "status": "draft"
}
```

#### Option B — Using `form-data` (for file uploads)

| Setting | Value |
|---------|-------|
| Method | `POST` ← change to POST |
| URL | `{{base_url}}/posts/1` |
| Auth | Bearer Token |
| Body | `form-data` |

| Key | Value |
|-----|-------|
| `_method` | `PUT` ← this is required |
| `title` | Updated Title |
| `body` | Updated content |
| `status` | draft |

> 💡 The `_method` field tells Laravel to treat this `POST` as a `PUT` request. This is called **method spoofing** and is the standard way to send `form-data` updates in Laravel.

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Post updated successfully",
  "data": {
    "id": 1,
    "title": "Updated Title",
    "body": "Updated content.",
    "status": "draft",
    "updated_at": "2026-02-24T04:00:00+00:00"
  }
}
```

---

### 🗑️ Delete a Post

| Setting | Value |
|---------|-------|
| Method | `DELETE` |
| URL | `{{base_url}}/posts/1` |
| Auth | Bearer Token |

**Success Response `200`:**
```json
{
  "success": true,
  "message": "Post deleted successfully",
  "data": null
}
```

---

## 6. Sending form-data vs JSON

| Feature | `raw → JSON` | `form-data` |
|---------|-------------|-------------|
| Simple text fields | ✅ | ✅ |
| File uploads | ❌ | ✅ |
| PUT / PATCH support | ✅ native | ⚠️ needs `_method` field |
| Required headers | `Content-Type: application/json` | none needed |
| Best for | Most API requests | File/image uploads |

---

## 7. How to Set the Bearer Token

After login or register, copy your token and add it to Postman in **one of two ways**:

### Option A — Per Request (Manual)

1. Open the request
2. Click the **Authorization** tab
3. Select **Bearer Token** from the dropdown
4. Paste your token in the **Token** field

### Option B — Collection Level (Apply to All Requests)

1. Click the **collection name** in the sidebar
2. Go to **Authorization** tab
3. Select **Bearer Token**
4. Set the token value to `{{token}}`
5. Go to **Variables** tab and set `token` = your token value

Now all requests in the collection automatically use the token — no need to set it per request.

---

## 8. Understanding the Response

Every response from this API follows the same format:

### Success
```json
{
  "success": true,
  "message": "Descriptive message",
  "data": { }
}
```

### Validation Error `422`
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthenticated `401`
```json
{
  "success": false,
  "message": "Unauthenticated. Please login."
}
```

### Forbidden `403`
```json
{
  "success": false,
  "message": "You are not authorized to access this post."
}
```

### Not Found `404`
```json
{
  "success": false,
  "message": "Resource not found."
}
```

---

## 9. Common Errors

| Error | Cause | Fix |
|-------|-------|-----|
| `Route not found` | Wrong URL or missing route | Check URL matches exactly, run `php artisan route:list` |
| `Unauthenticated` | Missing or expired token | Login again and update your token |
| `Validation failed` | Missing or invalid fields | Check required fields and correct format |
| `403 Forbidden` | Accessing another user's post | Make sure the post belongs to the logged-in user |
| `404 Not Found` | Post ID doesn't exist | Check the ID in the URL is correct |
| PUT with form-data not working | Laravel limitation | Use `POST` + `_method: PUT` in form-data |
| Token invalid after logout | Token was revoked | Login again to get a fresh token |

---

## Quick Reference — All Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/v1/auth/register` | ❌ | Register new user |
| `POST` | `/api/v1/auth/login` | ❌ | Login |
| `GET` | `/api/v1/auth/me` | ✅ | Get current user |
| `POST` | `/api/v1/auth/logout` | ✅ | Logout |
| `GET` | `/api/v1/posts` | ✅ | List all posts |
| `POST` | `/api/v1/posts` | ✅ | Create a post |
| `GET` | `/api/v1/posts/{id}` | ✅ | Show a post |
| `PUT` | `/api/v1/posts/{id}` | ✅ | Update a post |
| `DELETE` | `/api/v1/posts/{id}` | ✅ | Delete a post |

---

*Built with Laravel 12 + Sanctum · API Version 1*