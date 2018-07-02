laravel-admin.org
=================

Source code of official [http://laravel-admin.org](http://laravel-admin.org) website.

## Installation

```shell

$ git clone http://gitlab.namviet-corp.vn/hoatq/linkgo_cms.git

$ cd linkgo_cms

$ composer install 

```

Then create a database with name `linkgo_cms_admin` in your mysql. You can find database dump in `storage/mysql_dump/linkgo_cms_admin.sql`,  import it:
```sql

mysql> create database `linkgo_cms_admin`;

mysql> use `linkgo_cms_admin`;

mysql> source storage/mysql_dump/linkgo_cms_admin.sql

```

Back to terminal and start the web server:

```shell

$ php artisan serve

```

Finally open `http://localhost:8000/` in your browser.

## License

MIT
