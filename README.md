# BasePHP Package: Database
Database and Query Builder

Usage:
---------------

`use \Base\Support\Facades\DB;`

*Examples:*

```php

// get the test more recent users
DB::select(['id','name'])->table('users')->where(['status'=>'enabled'])->limit(10)->order('id DESC')->results();

// get just a single item (user) from the db table by id
DB::table('users')->where('id',32212)->row();


```

### Query Builder Methods:

*These methods are stackable*

|Method           |
|---              |
|`select()`       |
|`table()`        |
|`where()`        |
|`in()`           |
|`not()`          |
|`limit()`        |
|`offset()`       |
|`order()`        |
|`group()`        |


### Query Result Methods:

*These methods return database results*

|Method           |
|---              |
|`row()`          |
|`results()`      |
|`first()`        |
|`update()`       |
|`delete()`       |
|`insert()`       |
|`count()`        |
