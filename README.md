# Qbuilder
Yet another Query Builder PHP library

## Documentation

### Installation

```console
composer require develhopper/qbuilder
```

### Setup

set below variable in dotenv

```
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_NAME=[database name]
DB_USER=[database user]
DB_PASSWORD=[database password]
```

### create models

```php
<?php
namespace models;

use QB\QBuilder as Model;

class Customer extends Model{
    // table name
    protected $table="customers";
    
    // primary key
    protected $primary="customerNumber";

    // relations
    public function payments(){
        return $this->hasMany(Payment::class,false);
    }
}
```

### create migrations

```php
<?php
use QB\Migration\Migration;
use QB\Migration\Column;
use Denver\Env;

Env::setup(__DIR__."/.env");

$users = Migration::create_table('users', Column::IntegerField('id', ['primary' => true]),
    Column::StringField('username',25,['unique' => true]),
    Column::StringField('email',255,['unique' => true]),
    Column::StringField('password',255)
);

$profile = Migration::create_table('profile', Column::IntegerField('id', ['primary']),
    Column::StringField('first_name', 30),
    Column::StringField('last_name', 30),
    Column::IntegerField('user_id', ['connect' => $users->id, 'on_delete' => 'cascade', 'on_update' => 'restrict'])
);
```

### More

... read the code