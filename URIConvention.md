# URI Convention

### _Rules_:

- URI should be in [snake_case](https://en.wikipedia.org/wiki/Snake_case).
- In this phase, we will use prefix `api/v1` for all API endpoints.
- All the resources should be in [plural](https://www.vinaysahni.com/best-practices-for-a-pragmatic-restful-api).
- If URI has path parameters, it's better to use `{resource_id}` instead of using `{id}`.  
  ex: `/api/v1/users/{user_id}` NOT `/api/v1/users/{id}`; Just for more readability.
- If URI has query parameters, it's better to use `{parameter}` instead of using `{parameter_name}`.  
  ex: `/api/v1/users?page={page}` NOT `/api/v1/users?page_number={page}`; Just for more readability.
- If you want to declare permissions for an api, use `User::ROLE_*` constants instead of string format.  
  ex: `#[AcceptableRoles(User::ROLE_ADMIN, User::ROLE_EXPERIENCER)]`
  NOT `#[AcceptableRoles('ROLE_ADMIN', 'ROLE_EXPERIENCE')]`