# User registration
**URL** : `/api/v1/signup`

**Method** : `POST`

**Body**:

```json
{
    "name": "Vishnu B",
    "email": "vishnu@example.com",
    "password": "adminers"
}
```

## Success Response

**Code** : `201`

```json
{
    "data": {
        "api_token": "CJ8UwpijQr7XJdYq5vPzNr5gY4G0sTfKdfzgbjKYZQWEXbKu8qdN3Ujj74HIHSLcixWX3XLpzyHfotjO",
        "name": "Vishnu B",
        "email": "vishnu@example.com"
    }
}
```

## Validation Failed Response

**Code** : `422`

```json
{
    "errors" : {
        "email": [
            "The email must be a valid email address."
        ]
    }
}
```
