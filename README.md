# Bird API Documentation

A RESTful API for managing bird information.

## Base URL
```
http://localhost/FugleAPI
```

## Endpoints

### Get All Birds
```http
GET /?limit={limit}&offset={offset}
```
Returns a paginated list of birds.

**Query Parameters:**
- `limit` (optional): Number of results per page (default: 10)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
    "count": 20,
    "next": "http://localhost/FugleAPI?offset=10&limit=10",
    "prev": null,
    "results": [
        {
            "name": "Shoebill",
            "url": "http://localhost/FugleAPI?id=1"
        }
    ]
}
```

### Get Single Bird
```http
GET /?id={id}
```
Returns detailed information about a specific bird.

**Response:**
```json
{
    "id": 1,
    "name": "Shoebill",
    "habitat": "Swamps of central tropical Africa",
    "diet": "Fish, frogs, small reptiles",
    "weight": 5500,
    "wingspan": 2.3,
    "features": "Massive shoe-shaped bill, dinosaur-like posture",
    "url": ["http://example.com/shoebill.jpg"]
}
```

### Create Bird
```http
POST /
```
Creates a new bird entry.

**Required Form Data:**
- `name`: Bird name
- `habitat`: Natural habitat description
- `diet`: Dietary information
- `weight`: Weight in grams
- `wingspan`: Wingspan in meters
- `features`: Distinctive features

**Response:** Status 201 Created

### Update Bird
```http
PUT /?id={id}
```
Updates an existing bird entry.

**Required Body Parameters:**
- `name`: Bird name
- `habitat`: Natural habitat description
- `diet`: Dietary information
- `weight`: Weight in grams
- `wingspan`: Wingspan in meters
- `features`: Distinctive features

**Response:** Updated bird object

### Delete Bird
```http
DELETE /?id={id}
```
Deletes a bird entry.

