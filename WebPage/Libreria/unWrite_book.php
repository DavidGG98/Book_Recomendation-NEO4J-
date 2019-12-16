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
    if(isset($_POST['writer']) and isset($_POST['book'])) {
    if($_POST['writer']!="") {
        if($_POST['book']!="") {
            $user= $_POST['writer'];   
            $book= $_POST['book'];


            $query=unWriteBook($user,$book);
         try {
            $result=$client->run($query);
            } catch (Exception $e){
                echo "ERROR";
            }


           header("Location: writer_profile.php?user=$writer");

    } else {
        echo "<p style='color:red'> ERROR, Se necesita un libro ";
    }
        
    } else {
        echo "<p style='color:red'> ERROR, Se necesita un escritor ";
    }
    
    } else {
        echo "<p style='color:red'> ERROR, se necesitan argumentos ";
    }
?>