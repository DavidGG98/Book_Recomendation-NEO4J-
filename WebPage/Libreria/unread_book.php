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

  if(($_POST['user']!="") and ($_POST['book']!="")) {

    $user= $_POST['user'];
    $book= $_POST['book'];


    $query=unReadBook($user,$book);
 try {
    $result=$client->run($query);
    } catch (Exception $e){
        echo "ERROR";
    }
    if (isset($_POST['unRead']))
        header("Location: user_profile.php?user=$user");
    else {
        header("Location: book_profile.php?book=$book");
    }
} else {

 echo error;

}

?>
