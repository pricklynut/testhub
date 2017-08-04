TestHub
=======

TestHub is a service which allows you to create and pass tests.

Written in PHP on Symfony 3 framework.

Installation and configuration
------------------------------

Requirements:
  * linux os (in my case ubuntu 16.04)
  * php ^7.0
  * web server apache ^2.4
  * dbms postgresql ^9.5 or mysql ^5.6 (innodb fulltext indexes)

Installation steps:
  1. Clone project repository from github
  
        ```
        $ git clone https://github.com/pricklynut/testhub.git
        ```
  1. Install depending libraries via composer
        ```
        $ composer install  
        ```
  1. In `app/config/parameters.yml` set your db connection settings
  
Configuration (optional).
  * If you encounter with symfony cache permissions problem, its quite common issue, you might solve this with
  [off.documentation article](https://symfony.com/doc/current/setup/file_permissions.html)
  
  * In order to meet the requirements of my hosting (heroku), I had to change application config in production mode.
  So if you want the app in **production** mod to be accessible on your local machine, you will need to add
  two environment variables to your system (in dev mod everything must be ok). In my case, I added them to apache
  virtual host configuration:
        
    ```
    <VirtualHost *:80>
        SetEnv DATABASE_URL "postgres://username:password@127.0.0.1:5432/testhub"
        SetEnv SECRET "ThisTokenIsNotSoSecretChangeIt"
    </VirtualHost>
     ```
  * I tried the application can run with both Postgres and Mysql dbms, but Postgres is preferable.
  But if you still want to use Mysql, do next steps to change driver:
    * in `app/config/config.yml` in Doctrine section change driver to pdo_mysql: `driver: pdo_mysql`
    * in the same file in doctrine_migrations section change directory name and namespace: 
        ```
        dir_name: "%kernel.root_dir%/Migrations/Mysql"
        namespace: App\Migrations\Mysql
        ```
    * in `app/config/parameters.yml` change db setting
    * only for production, change database driver in `app/config/parameters_prod.php`
    * Mysql 5.7 by default goes with unacceptable settings, which causes errors. For example, in attempts table we have
    two timestamp columns, and it crushes with "invalid default value".
    See [mysql bugtracker](https://bugs.mysql.com/bug.php?id=80163).
    In another case, app crushes with "cannot order by value that not presented in select list"
    [doctrine issue](https://github.com/doctrine/doctrine2/issues/5622)
    To avoid that mistakes, remove "ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE" from sql_mode setting
    in `/etc/mysql/my.cnf`. I added equivalent command via migration, but it will work only for current session
    (i.e. while you restart mysql).
    To avoid problems with utf-8, you should set default character set for server in my.cnf.
    