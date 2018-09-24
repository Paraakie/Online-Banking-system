Unzip and open MVCexample directory using terminal.

Execute: docker-compose up (MAKE SURE YOU EXECUTE IT IN THIS DIRECTORY!)

Sit back and relax for a while. Once its up and running (you will see output from mysql server), use your browser to open localhost:8000/account/
You should see log of HTTP requests in the terminal window (where you ran docker-compose up command).

You can also open localhost:8888 to access PhpMyAdmin if you wish to use it to inspect the database (host: mysql, user: root, pass: root).


You can use CodeSniffer to check your code against PSR-1 and PSR-2 by executing 'docker exec -ti php /web/vendor/bin/phpcs /web/src --standard=PSR12'
It is also able to fix some problems automatically - check the manual for CodeSniffer.

You can run phpDocumentor to create HTML docs for your code by executing 'docker exec -ti php /web/vendor/bin/phpdoc -d /web/src/ -t /web/docs/'
The above commands must be executed in ANOTHER terminal window (while docker-compose up is running).

To stop docker containers, you can press ctrl-c in the terminal window where docker-compose up is running. Then execute 'docker-compose down' to remove the containers.