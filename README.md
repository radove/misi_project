Backend:

- Replace Consumer Key, Secret, Token, Token Secret with your own Twitter connector information
- Install Maven Dependencies (mvn install)
- Install ElasticSearch (API is written against ElasticSearch 6.2.4 or similar)
- https://www.linkedin.com/pulse/natural-language-extractions-using-machine-learning-ronnie-dove/ -- install Open NLP ES Plugin

Frontend:

- PHP Project uses PHP Composer to download dependencies (https://getcomposer.org/) into vendor directory.
- Connects to ElasticSearch using elastic.php to identify the host, if runningg on same box as ElasticSearch use localhost:9200
