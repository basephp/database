# BasePHP Package: Database
Database and Query Builder for BasePHP

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

And now you should have full access to your database and all query builder methods below.


Usage Examples:
---------------

```php

// add the access to the DB::class
use \Base\Support\Facades\DB;

// get the test more recent users
$users = DB::table('users')->select(['id','name'])->where(['status'=>'enabled'])->limit(10)->order('id DESC')->results();

// get just a single item (user) from the db table by id
$user = DB::table('users')->where('id',32212)->row();

// Write a RAW SQL Statement? Find all users that have a gmail email address
$user = DB::table('users')->where('email LIKE "%gmail.com" ')->results();

// Delete the item that matches an ID
DB::table('users')->where('id',7455)->delete();

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


```

`SQL` Builder:
---------------

*These methods are stackable*

|Method           |Option                       |Description          |
|---              |---                          |---                  |
|`table()`        | Required **MUST BE FIRST**  | `FROM table`        |
|`select()`       | *Optional* Default: `*`     | `SELECT fields`     |
|`where()`        | *Optional*                  | `WHERE`             |
|`in()`           | *Optional*                  | `IN (values)`       |
|`not()`          | *Optional*                  | `NOT IN (values)`   |
|`limit()`        | *Optional*                  | `LIMIT number`      |
|`offset()`       | *Optional*                  | Adding an offset    |
|`order()`        | *Optional*                  | `ORDER BY fields`   |
|`group()`        | *Optional*                  | `GROUP BY fields`   |

*Note: Calling `table()` will reset the current SQL. For SQL protection, `table()` is required to be first in chain.


`READ` Queries:
---------------

*These methods return database results*

|Method             | Description                                   |
|---                |---                                            |
|`row()`            | Run the query and return one item             |
|`results()`        | Run the query and return all items            |
|`first()`          | Run the query and return the first item       |
|`count()`          | Run a `COUNT` query and return the number     |
|`sum('field')`     | Get the `SUM(field)` of a table               |
|`avg('field')`     | Get the `AVG(field)` of a table               |
|`min('field')`     | Get the `MIN(field)` of a table               |
|`max('field')`     | Get the `MAX(field)` of a table               |

`WRITE` Queries:
---------------

*These methods write to the database*

|Method                       | Description                                   |
|---                          |---                                            |
|`update(array)`              | Run the `UPDATE` query                        |
|`insert(array)`              | Run the `INSERT` query                        |
|`delete()`                   | Run the `DELETE` query                        |
|`increment(field, value)`    | Run the `UPDATE` query                        |
|`decrement(field, value)`    | Run the `UPDATE` query                        |
|`truncate()`                 | Run the `TRUNCATE` query                      |


Database Support
---------------

*Currently only supports MySQLi connections*
