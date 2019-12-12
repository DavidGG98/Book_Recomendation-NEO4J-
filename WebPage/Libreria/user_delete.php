<?php

  include ('funciones.php');
  //Create the conection
  require_once ('vendor/autoload.php');
  use GraphAware\Neo4j\Client\ClientBuilder;
  $client = ClientBuilder::create()
      ->addConnection('default', 'http://neo4j:david98@localhost:7474')
      ->build();

    $message="";
    //Comprobar si se ha mandado registrar un nuevo usuario
if(isset($_POST['register'])) {
    $user= $_POST['user'];   
    //Encontramos el ID del nodo perteneciente a este usuario
    $query="MATCH (n:READER {name:'$user' }) RETURN n.name, ID(n)";
    $result=$client->run($query);
    $record=$result->firstRecord();
 
    //Borramos el nodo con el ID seleccionado
    $query=deleteNode($record->get('ID(n)'));

    $result=$client->run($query);
    $summary = $result->summarize();
    $stats = $summary->updateStatistics();
    $affected = $stats->containsUpdates();
    if ($affected) {
            $message="Nodo añadido";
        } else {
            $message="No se ha realziado ningun cambio";
        }
        header("Location: user_list.php");
} else {
    echo "<p style= 'color: red'> No se ha enviado ningun argumento </p>";
}

?>