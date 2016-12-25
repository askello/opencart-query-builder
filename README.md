# opencart-query-builder
Opencart Query Builder is a simple package

# Installation
Upload the contents of the 'upload' folder to the root directory of your OpenCart installation. These are some files should be overwritten. Windows will merge folders of the same name. For Mac you can use this command line command: cp -R -v

# Configuration
Query builder configurations are defined in system/library/db/QueryBuilder/config.php file.
```php
<?php
// Default limit for data selecting
QB_DEFAULT_LIMIT = 15; // 0 -> no limit
```
# Getting started
You may use the `table` method on the DB class to begin a query. The `table` method returns a fluent query builder instance for the given table, allowing you to chain more constraints onto the query and then finally get the results using the get method:

# Selecting data from DB
To retrive data from database you may use `get` method. The `get` method returns an array containing the results where each result is an associative array. You may access each column's value by accessing the column as a key of the array:

1. Select all data from a table:
```php
$products = DB::table('product')->get();
/*
$products => array(
  array('product_id' => 1, 'model' => 'p1', ...),
  array('product_id' => 2, 'model' => 'p2', ...),
  ...
);
```
2. Select only specific fields:
```php
$products = DB::table('product')->get(['product_id', 'model']);
```
3. Select content of the specific field:
```php
$name = DB::table('product')->find(1)->get('name');
// $name => 'John';
```
4. If result of query will contain more than one row, result of get method will array of values:
```php
$names = DB::table('product')->get('name');
// $names => array('John', 'Leo', 'Michael', ...);
```
5. Aggregates:
```php
$cnt = DB::table('product')->count();

$min = DB::table('product')->min('price');

$max = DB::table('product')->max('price');

$avg = DB::table('product')->avg('price');

$sum = DB::table('product')->sum('price');
```

# Query Conditions
Condition descriptio...
1. where(string field, mixed value)
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
If you wish to add multiple conditions, you are free to call `where` method a few times. All conditions will be diveded by `AND` keyword:
```php
// ... WHERE `firstname` = 'John' AND `lastname` = 'Dou'
$query->where('firstname', 'John')->where('lastname', 'Dou');
```
If you need to split your conditions by `OR` keyword, you may use `orWhere` method:
```php
// ... WHERE `firstname` = 'John' OR `firstname` = 'Leo'
$query->where('firstname', 'John')->orWhere('firstname', 'Leo');
```
2. where(string rawSql)
```php
// ... WHERE price BETWEN 100 AND 200 ...
$query->where('price BETWEN 100 AND 200');
```
3. where(array conditions)
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
# Inserting data
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

# Updating data
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

# Deleting data
To delete records from database use `delete` method:
```php
DB::table('product')->delete();

DB::table('product')->find(1)->delete();
```
If you wish to truncate the entire table, which will remove all rows and reset the auto-incrementing ID to zero, you may use the `clear` method:
```php
DB::table('product')->clear();
```
