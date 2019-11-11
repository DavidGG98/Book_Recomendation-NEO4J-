<?php

    require_once ('vendor/autoload.php');

    use GraphAware\Neo4j\Client\ClientBuilder; 

    $client = ClientBuilder::create()
        ->addConnection('default', 'http://neo4j:david98@localhost:7474')
        ->build();
    
    //Crea una query
    $query = "MATCH (n:WRITER {name:'William Shakespeare' }) RETURN n.name as name";
    //manda la query y la guarda en $result
    $result = $client->run($query);
    $record = $result->getRecord();
    echo $record->value('name');
          
 ?>



<html>

<head>
    <title> Pruebas </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css" />
</head>

<body>
    <div>
        Hola que haces
    </div>
    <div>
        <?php 
            echo $record->value('name');
        ?>
    </div>
    Hola mundo
</body>

</html>
