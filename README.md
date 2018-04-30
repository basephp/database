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
$users = DB::select(['id','name'])->table('users')->where(['status'=>'enabled'])->limit(10)->order('id DESC')->results();

// get just a single item (user) from the db table by id
$user = DB::table('users')->where('id',32212)->row();

// Delete the item that matches an ID
DB::table('users')->where('id',7455)->delete();

// Update
DB::table('users')
    ->set(['name' => 'John'])
    ->where('id',1233)
    ->update();


// Insert a new user
DB::table('users')->set([
        'name' => 'Joe',
        'email' => 'joe@email.com'
    ])->insert();


// Get a total count for how many enabled users are in the database
$userCount = DB::table('users')->where(['status'=>'enabled'])->count();


```

Query Builder:
---------------

*These methods are stackable*

|Method           |Option                       |Description          |
|---              |---                          |---                  |
|`select()`       | *Optional* Default: `*`     | `SELECT fields`     |
|`table()`        | Required                    | `FROM table`        |
|`set()`          | Required `INSERT/UPDATE`    | `SET fields`        |
|`where()`        | *Optional*                  | `WHERE`             |
|`in()`           | *Optional*                  | `IN (values)`       |
|`not()`          | *Optional*                  | `NOT IN (values)`   |
|`limit()`        | *Optional*                  | `LIMIT number`      |
|`offset()`       | *Optional*                  | Adding an offset    |
|`order()`        | *Optional*                  | `ORDER BY fields`   |
|`group()`        | *Optional*                  | `GROUP BY fields`   |


Query Result:
---------------

*These methods return database results*

|Method           | Description                                   |
|---              |---                                            |
|`row()`          | Run the query and return one item             |
|`results()`      | Run the query and return all items            |
|`first()`        | Run the query and return the first item       |
|`update()`       | Run the `UPDATE` query                        |
|`delete()`       | Run the `DELETE` query                        |
|`insert()`       | Run the `INSERT` query                        |
|`count()`        | Run a `COUNT` query and return the number     |


Database Support
---------------

*Currently only supports MySQLi connections*
