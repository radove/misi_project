<?php
require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$logger = ClientBuilder::defaultLogger('/var/www/html/root/cyber/elasticsearch.log');

$hosts = [
    'localhost:9200'         // IP + Port
];

$client = ClientBuilder::create()           // Instantiate a new ClientBuilder
                    ->setHosts($hosts)      // Set the hosts
                    ->setLogger($logger)        // Set the logger with a default logger
                    ->build();              // Build the client object

return $client;


