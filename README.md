- [Installation](#installation)
- [General Info](#general)
- [Selecting data](#select)
  - [Where conditions](#where)
  - [Limit conditions](#limit)
  - [Ordering results](#order)
  - [Joins](#join)
  - [First, last, and random conditions](#first-last-random)
- [Inserting data](#insert)
- [Updating data](#update)
- [Deleting data](#delete)
- [Conditions](#conditions)
- [DB Logger](#logger)


# opencart-query-builder
Opencart Query Builder is a simple package developed for simplify work with database. It provide convenient interface to run sql queries and takes care about safety, so there is no need to clean strings being passed to your application.

<a name="installation"></a>
## Installation
Upload the contents of the 'upload' folder to the root directory of your OpenCart installation. This is `system/library/db.php` file should be overwritten. Don't worry, you will be able to continue using all OpenCart features, like `$this->db->query()` and no need to rewrite your old code after installation.

<a name="general"></a>
## General Info
You may use the table method on the DB class to begin a query. The table method returns a fluent query builder instance for the given table, allowing you to chain more constraints onto the query and then run other commands to working with your data.
```php
// Retriving instance of query working with oc_product class.
$query = DB::table('product');
```
Note that there is no need to prefix your table names with DB_PREFIX, query builder will do it automatically. Also you may add alias to your table:
```php
// Add alias `p` to `oc_product` table
$query = DB::table('product p');
```

<a name="select"></a>
## Selecting data from DB
To retrive data from database query builder provide `get` method. The `get` method returns an array containing the results where each result is an associative array. You may access each column's value by accessing the column as a key of the array:

Select all data from a table:
```php
// SELECT * FROM `oc_product`
$products = DB::table('product')->get();
```
Select only specific fields:
```php
// SELECT `product_id`,`model` FROM `oc_product`
$products = DB::table('product')->get(['product_id', 'model']);
```
Get fields as aliases:
```php
// SELECT `product_id` AS `id` FROM `oc_product`
$products = DB::table('product')->get(['product_id' => 'id']);
```
Select content of the specific field:
```php
$name = DB::table('product')->find(1)->get('name');
// $name => 'John';
```
If result of query will contain more than one row, result of get method will array of values:
```php
$names = DB::table('product')->get('name');
// $names => array('John', 'Leo', 'Michael', ...);
```
Aggregates:
```php
$cnt = DB::table('product')->count();

$min = DB::table('product')->min('price');

$max = DB::table('product')->max('price');

$avg = DB::table('product')->avg('price');

$sum = DB::table('product')->sum('price');
```

<a name="where"></a>
## Where conditions
You may use the `where` method on a query builder instance to add where clauses to the query. The most basic call to `where` requires two arguments. The first argument is the name of the column. Also after column name may be added condition operator. The second argument is the value to evaluate against the column.
where(string field, mixed value)
```php
// ... WHERE `product_id` = 1 ...
$query->where('product_id', 1);

// ... WHERE `price` > 200 ...
$query->where('price >', 200);

// ... WHERE `product_id` IN (1,2,3) ...
$query->where('product_id', [1, 2, 3]);

// ... WHERE `product_id` NOT IN (1,2,3) ...
$query->where('product_id !=', [1, 2, 3]);

// ... WHERE `name` IS NULL ...
$query->where('name', null);

// ... WHERE `ean` IS NOT NULL ...
$query->where('ean !=', null);
```
If you wish to add multiple conditions, you are free to call `where` method a few times. All conditions will be divided by `AND` keyword:
```php
// ... WHERE `firstname` = 'John' AND `lastname` = 'Dou'
$query->where('firstname', 'John')->where('lastname', 'Dou');
```
If you need to split your conditions by `OR` keyword, you may use `orWhere` method:
```php
// ... WHERE `firstname` = 'John' OR `firstname` = 'Leo'
$query->where('firstname', 'John')->orWhere('firstname', 'Leo');
```
where(string rawSql)
```php
// ... WHERE price BETWEN 100 AND 200 ...
$query->where('price BETWEN 100 AND 200');
```
where(array conditions)
```php
// ... WHERE `price` = 100 ...
$query->where(['price' => 100]);

// ... WHERE (`firstname` = 'John' AND `age` > 20)
$query->where([
  'firstname' => 'John',
  'age >'     => 20
]);
```
Use `OR` operator:
```php
// ... WHERE (`price` = 100 OR `price` = 200)
$query->where([
  'firstname' => 'John',
  'or',
  'age >'     => 20
]);
```
Find result by its primary key:
```php
// ... WHERE `primary_key_field` = 1 ...
$query->find(1);

// ... WHERE `primary_key_field` IN (1,2,3) ...
$query->find([1,2,3]);
```

<a name="limit"></a>
## Limit conditions
To limit the number of results returned from the query, you may use `limit` method.
```php
// ... LIMIT 10 ...
$query->limit(10);
```
Also query builder provides `skip` and `page` methods for simle navigation through database records:
```php
// ... LIMIT 5, 10 ...
$query->limit(10)->skip(5);

// ... LIMIT 20, 10 ...
$query->limit(10)->page(3);
```

<a name="order"></a>
## Ordering results
```php
// ... ORDER BY `price` ...
$query->sortBy('price');

// ... ORDER BY `price` DESC ...
$query->sortBy('price', 'desc');

// ... ORDER BY `price` ASC, model DESC ...
$query->sortBy([
  'price' => 'asc',
  'model' => 'desc'
]);
```

<a name="join"></a>
## Joins
`join`, `crossJoin`:
```php
// ... INNER JOIN `oc_store` AS `p` ...
DB::table('product')->join('store');

// ... CROSS JOIN `oc_store` AS `p` ...
DB::table('product')->crossJoin('store');
```
Other `join` variants:
```php
// ... INNER JOIN `oc_store` USING(`product_id`)
DB::table('product')->join('store', 'product_id');

// ... INNER JOIN `oc_store` AS `s` ON `p`.`store_id` = `s`.`store_id`
DB::table('product p')->join('store s', 'p.store_id', 's.store_id')

// ... INNER JOIN `oc_product` AS `p` ON (p.store_id = s.store_id AND `p`.`language_id` = 1)
DB::table('product p')->join('store s', [
  'p.store_id = s.store_id',
  's.language_id' => 1
]);
```
But `join`, there are `leftJoin` and `rightJoin` methods, which accept same type of input conditions. For example:
```php
// ... LEFT OUTER JOIN `oc_store` AS `s` ON `p`.`store_id` = `s`.`store_id`
DB::table('product p')->leftJoin('store s', 'p.store_id', 's.store_id')

// ... RIGHT OUTER JOIN `oc_store` AS `s` ON `p`.`store_id` = `s`.`store_id`
DB::table('product p')->rightJoin('store s', 'p.store_id', 's.store_id')
```

<a name="first-last-random"></a>
## First, last, and random conditions
Query Builder also provides `first`, `last` and `random` methods for easiest way to work with data in database. These methods have one optional parameter - limit of results. By default limit equals 1.
```php
$query->first();

$query->first(10);

$query->last();

$query->last(10);

$query->random();

$query->random(10);

// Example (get email of first registered customer)
$email = DB::table('customer')->first()->get('email');
```

<a name="insert"></a>
## Inserting data
To insert data to database use `add` method:
```php
DB::table('product')->add([
  'model' => 'm1',
  'price' => 100,
  ...
]);
```
Insert multiple records:
```php
DB::table('product')->add([
  [
    'model' => 'm1',
    'price' => 100,
    ...
  ],
  [
    'model' => 'm2',
    'price' => 620,
    ...
  ],
  ...
]);
```

<a name="update"></a>
## Updating data
To update field in database use `set` method:
```php
DB::table('product')->set('price', 200);

DB::table('product')->find(1)->set('price', 200);
```
Update multiple fields:
```php
DB::table('product')->find(1)->set([
  'model' => 'm2',
  'price' => 200,
  ...
]);
```
The query builder also provides convenient methods for incrementing or decrementing the value of a given column:
```php
DB::table('customer')->increment('followers');

DB::table('customer')->increment('followers', 3);

DB::table('customer')->decrement('followers');

DB::table('customer')->decrement('followers', 3);
```
Also there is a `toggle` method for switching  boolean values:
```php
DB::table('product')->toggle('status');
```

<a name="delete"></a>
## Deleting data
To delete records from database use `delete` method:
```php
DB::table('product')->delete();

DB::table('product')->find(1)->delete();
```
If you wish to truncate the entire table, which will remove all rows and reset the auto-incrementing ID to zero, you may use the `clear` method:
```php
DB::table('product')->clear();
```

<a name="logger"></a>
## DB Logger
With query builder there are couple methods to easy debug development process:
```php
// Enable logger (by default it is disabled)
DB::enableLog();

// Available methods
$queries = DB::getExecutedQueries();

$count = DB::getTotalQueries();

$query = DB::getLastQuery();

DB::printLastQuery();
```
