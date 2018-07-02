
## Installation

```shell

$ git clone https://github.com/boykioyb/cms-laravel-admin.git

$ cd cms-laravel-admin

$ composer install 

```

Then create a database with name `cms_admin` in your mysql. You can find database dump in `storage/mysql_dump/cms_admin.sql`,  import it:
```sql

mysql> create database `cms_admin`;

mysql> use `cms_admin`;

mysql> source storage/mysql_dump/cms_admin.sql

```

Back to terminal and start the web server:

```shell

$ php artisan serve

```

Finally open `http://localhost:8000/` in your browser.

## License

MIT
