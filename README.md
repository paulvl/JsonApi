# JsonApi

## Pagination

If you want to paginate your results just add the "paginate" and "page" (optional) parameters to the url, for example:
```
http://example.com/api/user?paginate={items_per_page}&page={current_page}
```
> **Note:**
> 
> - If there are no ***page*** parameter, pagination will be set on the first page.
> - If ***page*** parameter is set with an unexisting value, JsonApi's with return an ***Ok*** response with empty **data**.


## Query Builder

If you need a customized query results you can