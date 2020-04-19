Please run these commands on the command line :
- composer install
- symfony server:start

The application can be accessed through this link http://127.0.0.1:8000/lunch

By default the current date is now. But we can change by passing the value of the current date through "date" parameter in query string. Like this one http://127.0.0.1:8000/lunch?date=2019-04-03

To test the code please use this command on the command line:
- php bin/phpunit tests/Util/LunchTest.php

