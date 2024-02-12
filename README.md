# Microservice Student Inscriptions

The Microservice Student Inscriptions is a standalone service handling student-related operations within a distributed system.

## Overview

The Microservice Student Inscriptions provides CRUD (Create, Read, Update, Delete) operations for managing student entities. It is designed as a RESTful API, supporting standard HTTP methods.

## Endpoints

### 1. Get inscription Information

- **Method:** GET
- **Endpoint:** `/api/useruc/{userEmail}/{ucCode}`
- **Description:** Retrieve information.
- **Example:** `GET /api/useruc/john.doe@example.com/PCC`

### 2. Create a New inscription

- **Method:** POST
- **Endpoint:** `/api/useruc`
- **Description:** Create a new inscription.
- **Example Request:**
  ```json
  {
    "user": "john.doe@example.com",
    "uc": "PCC"
}

### 3. Update inscription

- **Method:** PUT
- **Endpoint:** `/api/useruc/{userEmail}/{ucCode}`
- **Description:** update a inscription.
- **Example Request:**
  ```json
  {
    "status": 1
  }

### 4. Delete inscription
- **Method:** DELETE
- **Endpoint:** `/api/useruc/{userEmail}/{ucCode}`
- **Description:** Delete  .
- **Example:** `DELETE /api/useruc/john.doe@example.com/PCC`