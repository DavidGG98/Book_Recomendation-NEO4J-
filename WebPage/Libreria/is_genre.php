<?php

  include ('funciones.php');
  //Create the conection
  require_once ('vendor/autoload.php');
  use GraphAware\Neo4j\Client\ClientBuilder;
  $client = ClientBuilder::create()
      ->addConnection('default', 'http://neo4j:david98@localhost:7474')
      ->build();

    $message="";
    //Guardamos el usuario que está haciendo la petición
    $genre= $_POST['genre'];   
    $book= $_POST['book'];


    $query=isGenre($genre,$book);
    try {
    $result=$client->run($query);
    } catch (Exception $e){
        echo "ERROR";
    }

    header("Location: genre_profile.php?genre=$genre");
  
?>