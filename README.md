Backend:

- Replace Consumer Key, Secret, Token, Token Secret with your own Twitter connector information
- Install Maven Dependencies (mvn install)
- Install ElasticSearch (API is written against ElasticSearch 6.2.4 or similar). The API can be updated to reflect a newer version, but some sytax might change as-is
- https://www.linkedin.com/pulse/natural-language-extractions-using-machine-learning-ronnie-dove/ -- install Open NLP ES Plugin, this might be installed by default for newer versions of ES now.

Frontend:

- PHP Project uses PHP Composer to download dependencies (https://getcomposer.org/) into vendor directory.
- Connects to ElasticSearch using elastic.php to identify the host, if runningg on same box as ElasticSearch use localhost:9200

Future:

- Wanted to replace PHP with ReactJS. 
- React-VIS for the visualization
