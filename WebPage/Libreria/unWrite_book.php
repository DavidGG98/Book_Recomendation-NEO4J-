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
if (isset($_POST['register'])) {
    $user= $_POST['user'];   
    $book= $_POST['book'];


    //$query=unWriteBook($user,$book);
 try {
    //$result=$client->run($query);
    } catch (Exception $e){
        echo "ERROR";
    }
}

   header("Location: user_profile.php?user=$user")
?>