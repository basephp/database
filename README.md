# BasePHP: Database Package
Database and Query Builder for BasePHP. *This is an optional package, and is not a requirement for BasePHP*

* [BasePHP Framework](https://github.com/basephp/framework)
* [BasePHP Example Application](https://github.com/basephp/application)
* **BasePHP Database Package**

## Installation

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


## Examples

```php
use \Base\Support\Facades\DB;
```

Get results:

```php
// get a single user from the database
$user = DB::table('users')->where('id',912864)->first();

// get all users in the database, order the results
$users = DB::table('users')->order('id ASC')->get();

// get all users that have gmail email address (writing custom SQL)
$users = DB::table('users')->where('email LIKE "%gmail.com" ')->get();

// count how many users are enabled
$enabledUsers = DB::table('users')->where(['status'=>'enabled'])->count();

// get most recent 10 enabled users
$users = DB::table('users')
            ->select(['id','name'])
            ->where(['status'=>'enabled'])
            ->limit(10)
            ->order('id DESC')
            ->get();

// get all the cities that have over 1,000,000 population
$cities = DB::table('postal_codes')
            ->group('city')
            ->having('population > 1000000')
            ->get();

// get the average price of all ebooks from the products table
$avgPrice = DB::table('products')->where('category','ebooks')->avg('price');
```

**Update items:**

```php
// change user's name
DB::table('users')
    ->where('id',912864)
    ->update([
        'name' => 'John Smith'
    ]);

// increase this users "page view" count
DB::table('users')->where('id',9983287)->increment('page_view',1);
```

**Insert items:**

```php
// add a new user to the table, and return the new ID
$newUserId =  DB::table('users')
                ->insert([
                    'name' => 'John Smith',
                    'email' => 'jsmith@email.com'
                ]);
```

**Delete items:**

```php
// delete a user by id
DB::table('users')
    ->where('id',912864)
    ->delete();

// delete all users with deleted = 1
DB::table('users')
    ->where(['deleted' => 1])
    ->delete();
```


**Writing RAW SQL:**

*Note: Raw Queries will return results into a `Collection`, unless you are "writing" to the database.*

```php
// Writing a RAW SQL query to get 10 products from the database.
$products = DB::query("SELECT * FROM products WHERE status = 'enabled' LIMIT 10");
foreach($products as $product)
{
    // display products here
}

// writing an update query
DB::query("UPDATE products SET price = 61.41 WHERE id = '$id' ");

// writing raw queries (without the query builder)
$newUserId = DB::query("INSERT INTO users WHERE name = 'John Smith', email = 'jsmith@email.com' ");
```


## Query Builder

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


## Execute Queries

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


*These methods execute "write" queries*

|Method                       | Description                                   |
|---                          |---                                            |
|`update(array)`              | Run the `UPDATE` query                        |
|`insert(array)`              | Run the `INSERT` query, returns `insert id`   |
|`delete()`                   | Run the `DELETE` query                        |
|`increment(field, value)`    | Run the `UPDATE` query                        |
|`decrement(field, value)`    | Run the `UPDATE` query                        |
|`truncate()`                 | Run the `TRUNCATE` query                      |


Utility Methods:
---------------

|Method                       | Description                                              |
|---                          |---                                                       |
|`query(string)`              | Write a raw SQL query and return its results             |
|`escape(string)`             | Escape a string for database injection                   |
|`isWriteSql(string)`         | Checks a string if the SQL statement is write type       |
|`isInsertSql(string)`        | Checks a string if the SQL statement is `INSERT`         |

*Note: all values passed into query methods (not custom SQL's) automatically run through `escape()`*


## Database Support

*Currently only supports MySQL connections*
