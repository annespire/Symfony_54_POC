# Before you even checkout the code...
1. Make sure you add the --recursive flag to your checkout so that the boxoffice submodule comes down as well
2. If you missed that, just do a `git submodule update --init` to pull it down
3. You will probably want to switch to the boxoffice z-symfony-test-001 branch for now
4. You will want to use the develop branch for this repository

# Steps to install / get running
1. obtain a .env.local file from a coworker and install next to the .env file in the root folder
2. copy the boxoffice/app/config.php file from your normal boxoffice source code and place it into src/boxoffice/app/
3. copy the entire boxoffice/assets folder from your normal boxoffice source code into the public folder
4. you will need to get a copy of your htt_demo and ept_super database set up in the database server brought up in Docker.  The process, as a whole, is left to the reader. Remember to read the docker-compose.yml file and remember that .env will be used when database is first initialized.
5. `docker-compose up -d`
6. `docker exec -it symfony_test_sym_1 /bin/bash`
7. `composer install`
8. `symfony server:start`

# In PHPStorm
1. Set language level to minimally be 8.0
2. Set up a Docker Compose based CLI Interpreter, PHPStorm will pretty much walk you through it. Set your path mappings to:
   1. <Project root>→/var/www/html; <Project root>/docker/php.ini→/usr/local/etc/php/php.ini
3. Make sure you are listening for incoming debugging requests
4. Set a breakpoint on line 8, or any line of your choosing, in public/index.php or maybe in src/boxoffice/app/controllers/LoginController.php

# In a browser
1. http://localhost

# As you develop and / or pull down new code
1. if necessary, control C from the `symfony server:start` process
2. if you modify yaml files, you'll probably need to `composer dump-autoload`
3. execute `php bin/console cache:clear`
