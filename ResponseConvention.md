# Response Convention

### All responses are JSON objects with the following properties in order:

- ### _data_
    - You should put the data you want to return in this property.  
      The important point is if you return an empty object, you should return an **empty array**.  
      ex: If you successfully delete an object, you should return an empty object.
- ### _message_
    - You should put a message in this property.  
      Message is a sentence that describes the result of the request.
- ### _status_
    - This property shows that if a request is successful or not.
        - If it is successful, it should be `success`.
        - If it is not successful, it should be `failed`.
- ### _code_
    - This property shows the status code of the request.
        - `200`: Successful request
        - `201`: Successful request with new object created
        - `400`: Bad request
        - `401`: Unauthorized
        - `403`: Forbidden
        - `404`: Not found
        - `405`: Method not allowed
        - `500`: Internal server error
    - For more readability, It's better to use `Response::HTTP_*` constants  
      instead of hard coding the status code.  
      ex: `Response::HTTP_OK` NOT `Jsonresponse::HTTP_OK`
