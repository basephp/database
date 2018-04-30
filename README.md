# BasePHP Package: Database
Database and Query Builder for BasePHP

Installation:
---------------

**(1)** Install using composer

`php composer.phar require basephp/database dev-master`

**(2)** Use the `DB` class:**

`use \Base\Support\Facades\DB;`

**(3)** Add database to your `.env` file

```
DB_USER=admin
DB_PASS=password
DB_HOST=127.0.0.1
DB_NAME=database
```

**(4)** Add the `example_config.php` to your `config/` directory (rename it to `db.php`)

And now you should have full access to your database and all query builder methods below.


Usage Examples:
---------------

```php

// get the test more recent users
$users = DB::select(['id','name'])->table('users')->where(['status'=>'enabled'])->limit(10)->order('id DESC')->results();

// get just a single item (user) from the db table by id
$user = DB::table('users')->where('id',32212)->row();

// Delete the item that matches an ID
DB::table('users')->where('id',7455)->delete();

// Update
DB::table('users')
    ->set([
        'name' => 'John',
        'email' => 'johnsmith@email.com'
    ])
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

### Query Builder Methods:

*These methods are stackable*

|Method           | Description         |
|---              |---                  |
|`select()`       | `SELECT fields`     |
|`table()`        | `FROM table`        |
|`where()`        | `WHERE`             |
|`in()`           | `IN (values)`       |
|`not()`          | `NOT IN (values)`   |
|`limit()`        | `LIMIT number`      |
|`offset()`       | Adding an offset    |
|`order()`        | `ORDER BY fields`   |
|`group()`        | `GROUP BY fields`   |
|`set()`          | `SET fields`        |


### Query Result Methods:

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
