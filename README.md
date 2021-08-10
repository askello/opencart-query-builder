- [Compatibility](#compatibility)
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
Opencart Query Builder provides convenient interface to work with database. It is totally fast and secure.

Features:
- super simple interface (inspired by modern frameworks: laravel, yii, codeigniter...)
- no more tons of `$this->db->escape('...')`, builder automaticaly makes secure sql
- no more table prefixes, just table names
- doesn't break your code (`$this->db->query()` and other default functions still work)
- easy debug functionality

Let see how it looks:

```php
// somewhere above in controller
$db = $this->db;

// Take exact what you need:
$email = $db->table('customer')->find(1)->get('email');

// How to set minimal price for your products? Easy:
$db->table('product')->where('price <', 100)->set('price', 100);

// Happy customer bought three products at a time? Don't forget to mark it:
$db->table('product')->find(1)->decrement('quantity', 3);
```

Like it? More interesting features and code examples you will find bellow.

## Compatibility
Compatible with OpenCart 1.&ast;, OpenCart 2.* and OpenCart 3.*

Tested only with: MySql, MariaDB

<a name="installation"></a>
## Installation
Upload the contents of the 'upload' folder to the root directory of your OpenCart installation.

To start using QueryBuilder, we need tell our Registry to use it instead of our old DB class. To do it, after:
```php
$registry->set('db', ...);
```
write this:
```php
// QueryBuilder
$registry->set('db', new DB\QueryBuilder\QueryBuilder($registry->get('db')));
```

OpenCart 2.2.* or higher:

![alt text](https://askello.github.io/opencart-query-builder/installation-new.jpg)


OpenCart 2.0.0 - 2.1.0.2:

![alt text](https://askello.github.io/opencart-query-builder/installation-old.jpg)

OpenCart 1.* (first versions of OpenCart also need autoloader):

![alt text](https://askello.github.io/opencart-query-builder/installation-v1.jpg)

<a name="general"></a>
## General Info
After installation, your old `$this->db` class has new method - `table`. Method `table` returns a fluent query builder instance for the given table, which allows you to chain more constraints onto the query and then run other commands to work with your data.
```php
// somewhere in controller
$db = $this->db;

// Retriving instance of query working with oc_product table.
$query = $db->table('product');
```
Note that there is no need to prefix your table names with DB_PREFIX, query builder will do it automatically.

If your query would work with two or more tables, usually you must use table prefixes for your fields:
```php
// retrive products models and names (where few products left)
$products = $db->table('product')
               ->join('product_description', 'product_id')
               ->where('product.quantity < ', 10)
               ->get(['product.model', 'product_description.name']);
```
To write less code, you always can add your own aliases for each table:
```php
// Add alias `p` to `oc_product` table
$products = $db->table('product p')
               ->join('product_description pd', 'product_id')
               ->where('p.quantity', 10);
               ->get(['p.model', 'pd.name']);
```
Also you could use `AS` keyword if you like (case insensitive):
```php
$query = $db->table('product as p')->...;
$query = $db->table('product AS p')->...;
```

<a name="select"></a>
## Selecting data from DB
Retrieving data from database can be done by `get` method.

Select all data from a table:
```php
// SELECT * FROM `oc_product`
$products = $db->table('product')->get();
```
Select only specific fields:
```php
// SELECT `product_id`,`model` FROM `oc_product`
$products = $db->table('product')->get(['product_id', 'model']);
```
Get fields as aliases:
```php
// SELECT `product_id` AS `id` FROM `oc_product`
$products = $db->table('product')->get(['product_id' => 'id']);
```
Select content of the specific field:
```php
$name = $db->table('customer')->find(1)->get('firstname');
// $name => 'John';
```
If database returns more than one row, result will be array of values:
```php
$names = $db->table('customer')->get('firstname');
// $names => array('John', 'Leo', 'Michael', ...);
```
Check if record with specific primary key exists:
```php
if ( $db->table('product')->has($id) ) {
  ...
} else {
  exit('There is no product with id ' . $id . '!');
}
```
Aggregates:
```php
$cnt = $db->table('product')->count();

$min = $db->table('product')->min('price');

$max = $db->table('product')->max('price');

$avg = $db->table('product')->avg('price');

$sum = $db->table('product')->sum('price');
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
// ... WHERE (`firstname` = 'John' OR `age` > 20)
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
Also it is possible to use raw expressions:
```php
// ... ORDER BY RAND() ...
$query->sortBy('RAND()');
```
But note that example above has more convenient solution by using `random()` method.

<a name="join"></a>
## Joins
`join`, `crossJoin`:
```php
// ... INNER JOIN `oc_product_description` ...
$db->table('product')->join('product_description');

// ... CROSS JOIN `oc_product_description` ...
$db->table('product')->crossJoin('product_description');
```
Other `join` variants:
```php
// ... INNER JOIN `oc_manufacturer` USING(`manufacturer_id`)
$db->table('product')->join('manufacturer', 'manufacturer_id');

// ... INNER JOIN `oc_manufacturer` AS `m` ON `p`.`manufacturer_id` = `m`.`manufacturer_id`
$db->table('product p')->join('manufacturer m', 'p.manufacturer_id', 'm.manufacturer_id');

// ... INNER JOIN `oc_product_description` AS `pd` ON (p.product_id = pd.product_id AND `pd`.`language_id` = 1)
$db->table('product p')->join('product_description pd', [
  'p.product_id = pd.product_id',
  'pd.language_id' => 1
]);
```
Also there are `leftJoin` and `rightJoin` methods, which accept same type of input conditions. For example:
```php
// ... LEFT OUTER JOIN `oc_manufacturer` AS `m` ON `p`.`manufacturer_id` = `m`.`manufacturer_id`
$db->table('product p')->leftJoin('manufacturer m', 'p.manufacturer_id', 'm.manufacturer_id');

// ... RIGHT OUTER JOIN `oc_manufacturer` AS `m` ON `p`.`manufacturer_id` = `m`.`manufacturer_id`
$db->table('product p')->rightJoin('manufacturer m', 'p.manufacturer_id', 'm.manufacturer_id');
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

$query->random(10,.."'");

// Example (get email of last registered customer)
$email = $db->table('customer')->last()->get('email');
```

<a name="insert"></a>
## Inserting data
To insert data into database use `add` method:
```php
$id = $db->table('product')->add([
  'model' => 'm1',
  'price' => 100,
  ...
]);
```
Insert multiple records:
```php
$ids = $db->table('product')->add([
  [
    'model' => 'm1',
    'price' => 100,
    ...
  ], [
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
$db->table('product')->set('price', 200);

$db->table('product')->find(1)->set('price', 200);
```
Update multiple fields:
```php
$db->table('product')->find(1)->set([
  'model' => 'm2',
  'price' => 200,
  ...
]);
```
The query builder also provides convenient methods for incrementing or decrementing the value of a given column:
```php
$db->table('customer')->increment('followers');

$db->table('customer')->increment('followers', 3);

$db->table('customer')->decrement('followers');

$db->table('customer')->decrement('followers', 3);
```
Also there is a `toggle` method for switching  boolean values:
```php
$db->table('product')->toggle('status');
```
Notice, all methods above (`set`, `increment`, `decrement` and `toggle`) return count of updated rows:
```php
$countUpdated = $db->table('product')->where('price <', 100)->set('status', 0);
```

<a name="delete"></a>
## Deleting data
To delete records from database use `delete` method:
```php
$db->table('product')->delete();

$db->table('product')->find(1)->delete();
```
If you wish to truncate the entire table, which will remove all rows and reset the auto-incrementing ID to zero, you may use the `clear` method:
```php
$db->table('product')->clear();
```
Notice, `delete` and `clear` methods also return count of deleted rows:
```php
$countDeleted = $db->table('product')->where('price <', 100)->delete();
```

<a name="logger"></a>
## DB Logger
Query builder provides some methods for debugging:
```php
// Enable logger (by default it's disabled)
$db->enableLog();

// Available methods
$queries = $db->getExecutedQueries();

$db->printExecutedQueries();
```
