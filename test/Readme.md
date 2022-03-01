## Requirement:

- PHP 7.0+ (with ext-json, ext-pdo)
- MySQL 8.0

---

## Installation

Open terminal then `cd` to root directory of project.

```bash
cd /path/to/project
```

Run the following command to import dumped data to MySQL database (replace MYSQL_USERNAME and DB_NAME)

```bash
mysql -u {MYSQL_USERNAME} -p {DB_NAME} < db_dump.sql
```

Open `config.php` then update database connection & options:

```php
return [
    'debug' => true,

    'database' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '1',
        'db_name' => 'php_test',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ]
];
```

Run composer.

```bash
composer install
```

Start server:

```bash
php -S localhost:8000 -t public
```
---

### Tasks
#### GET
```
GET /tasks
```

#### INDEX
```
GET /task?page={page}&per_page={per_page}
```

#### CREATE
```
POST /tasks/create
```

##### Fields & Validations

- name: required|string|max:150
- priority: required|string|max:1
- status: required|string|max:1

#### UPDATE
```
POST /tasks/update
Body 
- taskId
- name
- priority
- status
```

##### Fields & Validations

- taskId: required|string|max150
- name: required|string|max:150
- priority: required|string|max:1
- status: required|string|max:1

#### DELETE
```
POST /tasks/delete
Body
- taskId
```