<?php

  include ('funciones.php');
  //Create the conection
  require_once ('vendor/autoload.php');
  use GraphAware\Neo4j\Client\ClientBuilder;
  $client = ClientBuilder::create()
      ->addConnection('default', 'http://neo4j:david98@localhost:7474')
      ->build();

    if (isset($_POST['writer']) AND isset($_POST['book'])) {
        $user= $_POST['writer'];  
        if ($_POST['book']!="") {
         
        $book= $_POST['book'];
        
        $query=newBook($book);
        $result=$client->run($query);
        
        $query2=writeBook($user,$book);
        $result=$client->run($query2);
        }
        header("Location: book_list.php?user=$book");
       
    } else {
        echo "ERROR";
    }
?>