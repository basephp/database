# BasePHP: Database Package
Database and Query Builder for BasePHP. *This is an optional package, and is not a requirement for BasePHP*

* [BasePHP Framework](https://github.com/basephp/framework)
* [BasePHP Example Application](https://github.com/basephp/application)
* **BasePHP Database Package**

Installation:
---------------

**(1) Install using composer**

`php composer.phar require basephp/database dev-master`

**(2) Add database to your `.env` file**

```
DB_USER=admin
DB_PASS=password
DB_HOST=127.0.0.1
DB_NAME=database
```

**(3) Add the `example_db.php` to your `config/` directory**


Usage Examples:
---------------

```php
// add the access to the DB::class
use \Base\Support\Facades\DB;

// get the test more recent users
$users = DB::table('users')
            ->select(['id','name'])
            ->where(['status'=>'enabled'])
            ->limit(10)
            ->order('id DESC')
            ->get();

// get just a single item (user) from the db table by id
$user = DB::table('users')->where('id',32212)->first();

// Write a RAW SQL Statement? Find all users that have a gmail email address
$user = DB::table('users')->where('email LIKE "%gmail.com" ')->get();

// Delete the item that matches an ID
DB::table('users')->where('id',7455)->delete();

// Get all the cities that have over 1,000,000 population
$cities = DB::table('postal_codes')
            ->group('city')
            ->having('population > 1000000')
            ->get();


// Update user's name by ID
DB::table('users')
    ->where('id',1233)
    ->update(['name' => 'John']);


// Insert a new user
DB::table('users')->insert([
    'name' => 'Joe Smith',
    'email' => 'joesmith@email.com'
]);


// Get a total count for how many enabled users are in the database
$userCount = DB::table('users')->where(['status'=>'enabled'])->count();

// increase this users "page view" count
DB::table('users')->where('id',9983287)->increment('page_view',1);

// Get the average price of all ebooks
$avgPrice = DB::table('products')->where('category','ebooks')->avg('price');

```

Query Builder:
---------------

*These methods are stackable*

|Method           |Option                         |Description           |
|---              |---                            |---                   |
|`table()`        | Required - **MUST BE FIRST**  | `FROM table`         |
|`join()`         | *Optional*                    | `JOIN table`         |
|`select()`       | *Optional* - Default: `*`     | `SELECT fields`      |
|`where()`        | *Optional*                    | `WHERE`              |
|`in()`           | *Optional*                    | `IN (values)`        |
|`not()`          | *Optional*                    | `NOT IN (values)`    |
|`limit()`        | *Optional*                    | `LIMIT number`       |
|`offset()`       | *Optional*                    | `LIMIT offset,limit` |
|`order()`        | *Optional*                    | `ORDER BY fields`    |
|`group()`        | *Optional*                    | `GROUP BY fields`    |
|`having()`       | *Optional*                    | `HAVING`             |
|`distinct()`     | *Optional*                    | `SELECT DISTINCT`    |

*Note: For every new query, first use the `table()` method.*


`READ` Queries:
---------------

*These methods execute "read" queries and return database results*

|Method             | Description                                                 |
|---                |---                                                          |
|`get()`            | Run the query and return all items as a `Collection` object |
|`first()`          | Run the query and return the first item                     |
|`last()`           | Run the query and return the last item                      |
|`count()`          | Run a `COUNT` query and return the number                   |
|`sum('field')`     | Get the `SUM(field)` of a table and return the number       |
|`avg('field')`     | Get the `AVG(field)` of a table and return the number       |
|`min('field')`     | Get the `MIN(field)` of a table and return the number       |
|`max('field')`     | Get the `MAX(field)` of a table and return the number       |

`WRITE` Queries:
---------------

*These methods execute "write" queries*

|Method                       | Description                                   |
|---                          |---                                            |
|`update(array)`              | Run the `UPDATE` query                        |
|`insert(array)`              | Run the `INSERT` query                        |
|`delete()`                   | Run the `DELETE` query                        |
|`increment(field, value)`    | Run the `UPDATE` query                        |
|`decrement(field, value)`    | Run the `UPDATE` query                        |
|`truncate()`                 | Run the `TRUNCATE` query                      |


Utility Methods:
---------------

|Method                       | Description                                              |
|---                          |---                                                       |
|`escape(string)`             | Escape a string for database injection                   |

*Note: all values passed into query methods (not custom SQL's) automatically run through `escape()`*


Database Support
---------------

*Currently only supports MySQL connections*
