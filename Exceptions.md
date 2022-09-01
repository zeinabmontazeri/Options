# Exception Convention

### If you want to throw an exception, you should use the following convention:

- In `Exception` Folder, we have `HttpExceptionEnum`.  
  This file contains all possible `HttpException` we need in our project.  
  If you want to add new Exception, please check `ExceptionFactory`, `HttpExceptionEnum` and `ExceptionSubscriber`
  files.

### How to use Exceptions?
  Following codes automatically convert to our convention.
  ```php
  'status' => 'failed',
  'data' => 'Exception'
```
- If you want throw `BadRequestException`:

  ```php
  throw new BadRequestHttpException('Your custom message');
  ```
  Above code automatically convert it to our convention.
- If user doesn't have permission to access the resource, you can throw `AccessDeniedException`:
  ```php
  throw new AccessDeniedException('Your custom message');
  ```
- `NotFoundHttpException` for resource or page throws `404`.
- If something with given `id` doesn't exist in our database,  
  It's better to throw `BadRequesException` instead of `NotFountHttpException`.
  If you use `paramConvertor` in your controller, Exception automatically it to `BadRequestHttpException`;  
  But you can customize it.