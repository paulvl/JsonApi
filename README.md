# JsonApi

[TOC]

## **Introduction**

## **Quick Installation**

Begin by installing this package through Composer.

You can run:

    composer require paulvl/json-api

Or edit your project's composer.json file to require paulvl/json-api.
```
    "require-dev": {
        "paulvl/json-api": "dev-master"
    }
```
Next, update Composer from the Terminal:

    composer update --dev

Once the package's installation completes, the final step is to add the service provider. Open `config/app.php`, and add a new item to the providers array:

```
PaulVL\JsonApi\JsonApiServiceProvider::class,
```

Finally publish package's configuration file:

    php artisan vendor:publish

Then the file `config/json-api.php` will be created.

That's it! You're all set to go. Run the artisan command from the Terminal to see the new `json-api` commands.

    php artisan

## **Models, Controllers and Routing**
To fast deploy your RESTful JsonApi server you have to use ```PaulVL\JsonApi\Model``` and ```PaulVL\JsonApi\Controller``` classes.

### **Models**
Begin creating a model using `json-api:make-model` command. For example:

    php artisan json-api:make-model Models\MyModel

This will create the `Models/MyModel.php` file that will look like this:

```
<?php

namespace App\Models;

use PaulVL\JsonApi\Model;

class MyModel extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes excluded from queries and the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The relationship included on the model's JSON form.
     *
     * @var array
     */
    protected $displayable_relations = [
        //'relation_example'
        ];

    /**
     * Validation rules. If any field needs unique validation, add this rule at the end of the line like example
     *
     * @var array
     */
    static protected $rules = [
        //'field_example' => 'required|email|max:255|unique:table,column'
    ];

    /*
    public function relation_example() {
        return $this->belongsTo('Class', 'foreign_key', 'primary_key');
    }
    */
}
```

As we can see there are two new attributes, *displayable_relations* and *rules*.

#### displayable_relations
The `displayable_relations` attribute will load all named relations to the json-api result, for example:

If we define `relation_example` funcion as above:
```
...
public function relation_example() {
        return $this->belongsTo('Class', 'foreign_key', 'primary_key');
}
...
```
And then add the relation name to the `displayable_relations` array:

```
...
protected $displayable_relations = [
        'relation_example'
        ];
...
```
this will produce a json-api result response that will look like this:
```
{
  "data": [
    {
      "type": "mymodel",
      "id": 1,
      "attributes": [
        "name": "name",
        "class_id": 1,
        "created_at": "2015-01-01 00:00:00",
        "updated_at": "2015-01-01 00:00:00"
      ],
      "relationships": {
        "relation_example": {
          "data": {
            "type": "class",
            "id": 1
          }
        }        
      }
    }
  ],
  "meta": {
    "copyright": "Copyright 2015 Your Bussiness App.",
    "authors": [
      "Paul Vidal"
    ]
  },
  "jsonapi": {
    "version": "1.0"
  }
}
```

#### rules
Now you can specify validation rules inside model itself, just add the validation rultes to the `rules` array:
```
...
static protected $rules = [
        'field_example' => 'required|email|max:255|unique:table,column'
    ];
...
```
>**Note:** if one field need to have a unique validation rule, that must be specified at the end, this because update rules will be generate automatically and add the corresponding exception rules.
>To get update rules use `getUpdateRules` function:
>
```$update_rules = $mymodel_instance->getUpdateRules();```

### **Controllers**
Continue creating a controller using json-api:make-controller command. For example:
    
    php artisan json-api:make-controller MyController
    
This will create the Http/Controllers/MyController.php file that will look like this:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PaulVL\JsonApi\Controller as JsonApiController;
use PaulVL\JsonApi\Response;

class MyController extends JsonApiController
{
    /**
     * Sets the model class that will be handled by this class.
     * @var string
     */
    protected $model_class = '';

    /**
     * saveData functions allows you to manual handle save actions triggered from store function on.
     * Uncomment this method only if you want to manual handle save actions.
     * 
     * @param  array  $inputs There are the request's inputs inyected from store function.
     * @return PaulVL\JsonApi\response returns response according to action.
     */
    
    /*
    public function saveData(array $inputs) {
        try {
            $response = new Response;

            // handle inputs and create and $object and store it.

            $response->handleData($object); // You should pass an object to response's handleData method.
            return $response->responseCreated(); // You must return a responseCreated.
        } catch (Exception $e) { // If any error is fired always return a responseInternalServerError.
            $response = new Response;
            return $response->responseInternalServerError();
        }   
    }
    */
    
    /**
     * updateData functions allows you to manual handle update actions triggered from update function on .
     * Uncomment this method only if you want to manual handle update actions.
     * 
     * @param  [type] $object Object inyected from update function.
     * @param  array  $inputs There are the request's inputs inyected from store function.
     * @return PaulVL\JsonApi\response returns response according to action.
     */
    
    /*
    public function updateData($object, array $inputs) {
        try {
            $response = new Response;

            // handle inputs and update the $object.

            $response->handleData($user); // You should pass the updated object to response's handleData method.
            return $response->response(); // You must return a responseOk or just response.
        } catch (Exception $e) { // If any error is fired always return a responseInternalServerError.
            $response = new Response;
            return $response->responseInternalServerError();
        }   
    }
    */
    
    /**
     * arrangeInputs functions allows you to manual handle inputs before validation.
     * Uncomment this method only if you want to manual arrange inputs.
     * 
     * @param  array  $inputs There are the request's inputs inyected from store function.
     * @return array  $inputs.
     */
    
    /*
    public function arrangeInputs(array $inputs) {
        // arrange inputs.
        return $inputs;
    }
    */   


    /**
     * Only if you need to implement create and edit function 
     * you can override them. They return error 404 as default.
     */
    
    // public function create() { }
    // public function edit($id) { }
    
}
```
As you can see there are many configurable things on the controller, but the most important is the `model_class` attribute, there you have to sets the model class name that will be handled by this controller, for example:

```
...
    protected $model_class = 'App\Models\MyModel';
...

```
This means that our MyController will handle MyModel.

### **Routing**

Finally once that we have already created our `MyController` controller and `MyModel` model classes, we just need to add the resource route to access our controller on `Http/routes.php` file, like this:
```
Route::resource('my-model', 'MyController', ['except' => ['create', 'edit']]);
```




## **Pagination**

JsonApi provides a simple way to paginate your results just add the "paginate" and "page" (optional) parameters to the url, for example:


> [http://example.com/api/user?](javascript:void(0))[**paginate={items_per_page}**](javascript:void(0))**&**[**page={current_page}**](javascript:void(0))


>> **Note:**
>>
>> - If there are no ***page*** parameter, pagination will be set on the first page.
>> - If ***page*** parameter is set with an unexisting value, JsonApi's will return an ***Ok*** response with empty **data**.


## **Query Builder**

JsonApi provides a fast way to running database queries. It can be used to perform most database operations in your application through URL requests, and works on all database systems supported by Laravel.

To run a query just add a **q** parameter to the url, and the content of this parameter must have this form ***"{clause}={argument_1},...,{argument_n}"***
```
http://example.com/api/user?q={clause}={argument_1},...,{argument_n}
```

> **Note:** To chain clauses just add a pipe **|** between clauses. for example:
>> [http://example.com/api/user?q=](javascript:void(0))[**clause1=arg1,arg2,arg3**](javascript:void(0))**|**[**clause2=arg1,arg2,arg3**](javascript:void(0))

### **Query Clauses**

#### **where**
The most basic call to *where* requires three arguments. The first argument is the name of the column. The second argument is an operator, which can be any of the database's supported operators. The third argument is the value to evaluate against the column.

For example, here is a query that verifies the value of the "votes" column is equal to 100:

> [http://example.com/api/user?q=](javascript:void(0))[**where=votes,=,100**](javascript:void(0))

For convenience, if you simply want to verify that a column is equal to a given value, you may pass the value directly as the second argument to the where clause:

> [http://example.com/api/user?q=](javascript:void(0))[**where=votes,100**](javascript:void(0))

You can also verify that a column is equal to a given value adding both values as url parameters:

> [http://example.com/api/user?](javascript:void(0))[**votes=100**](javascript:void(0))
> 
> **Note:** this will only work for `where` clauses, also you can chain them all:
>
> [http://example.com/api/user?](javascript:void(0))[**votes=100**](javascript:void(0))**&**[**name=Pepe**](javascript:void(0))

#### **orWhere**
You may chain where constraints together, as well as add or clauses to the query. The orWhere method accepts the same arguments as the where clause:

> [http://example.com/api/user?q=](javascript:void(0))[**where=votes,>,100**](javascript:void(0))**|**[**orWhere=name,Pepe**](javascript:void(0))

#### **whereBetween**
The `whereBetween` clause verifies that a column's value is between two values:

> [http://example.com/api/user?q=](javascript:void(0))[**whereBetween=votes,10,100**](javascript:void(0))

#### **whereNotBetween**
The `whereNotBetween` clause verifies that a column's value lies outside of two values:

> [http://example.com/api/user?q=](javascript:void(0))[**whereNotBetween=votes,10,100**](javascript:void(0))

#### **whereIn / whereNotIn**
The `whereIn` clauseverifies that a given column's value is contained within the given array:

> [http://example.com/api/user?q=](javascript:void(0))[**whereIn =votes,10,100**](javascript:void(0))

The `whereNotIn` clauseverifies that the given column's value is not contained in the given array:

> [http://example.com/api/user?q=](javascript:void(0))[**whereNotIn=votes,10,100**](javascript:void(0))


#### **whereNull / whereNotNull**
The `whereNull` clause verifies that the value of the given column is NULL:

> [http://example.com/api/user?q=](javascript:void(0))[**whereNull =updated_at**](javascript:void(0))

The `whereNotNull` clause verifies that the column's value is not NULL:

> [http://example.com/api/user?q=](javascript:void(0))[**whereNotNull=updated_at**](javascript:void(0))

#### **orderBy**
The `orderBy` clause allows you to sort the result of the query by a given column. The first argument to the `orderBy` clause should be the column you wish to sort by, while the second argument controls the direction of the sort and may be either `asc` or `desc`:

> [http://example.com/api/user?q=](javascript:void(0))[**orderBy=created_at,desc**](javascript:void(0))

