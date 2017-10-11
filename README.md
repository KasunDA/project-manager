# Project Manager
An open source Project Management System. I use this application to keep my freelance projects information up-to-date.

![Project Manager Dashboard](http://i.imgur.com/5mo4Qmp.png)

## Requirements

1. Composer

2. PHP >= 5.5

3. Apache

4. Database (Preferably MySQL)


## Installation

1. Clone the github repository or Download ZIP and UnZip it in your web server document root (usually `/var/www/html` on Linux).

2. Run `composer install` (you can download Composer from its official [website](https://getcomposer.org/download/)).

3. Create a new database for this project.

4. Change the file `conf/database.php` with your database information.

5. Run `schema.sql` in your database.

6. Run `schema_update.sh` to update the database (If you are using windows, run `php vendor/bin/doctrine orm:schema-tool:update --force --dump-sql`).

7. Done! Now, go to http://localhost/projectfolder/login.

8. Default `Username` and `Password` is `admin`.
