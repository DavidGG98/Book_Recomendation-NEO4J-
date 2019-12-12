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
        if ( $_POST['username']=="") {
            $message="No se puede crear un Lector sin nombre";
        } else {
        $user= $_POST['username'];
        $query=newWriter($user);
        
        $result=$client->run($query);
        
        $summary = $result->summarize();
        $stats = $summary->updateStatistics();
        $affected = $stats->containsUpdates();
        if ($affected) {
            $message="Escritor añadido";
        } else {
            $message="El Escritor ya existe";
        }
        }
    }
?>
<html>
<head>
    <title> Escritores </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</head>
<body>

        <div id="main">

        <div id="topbar"> Recomiendame un libro </div>
        
        <div id="lateralmenu">
            <ul class="menu">
                <li> <a href="index.php"> Home </a> </li>
                <li> <a href="user_list.php"> Lectores </a> </li>
                <li> <a href="book_list.php"> Libros </a> </li>
                <li> <a href="genre_list.php"> Generos </a> </li>
                <li> <a class="active" href="writer_list.php"> Escritores </a> </li>
                <li> <a href="index.php"> Contacto </a> </li>
            </ul>
        </div>

        <div id="body">                 
                <div>
                        <form method="post" action="writer_list.php">
                            <label for="uname"> Nuevo escritor </label>
                            <input type="text" name="username" placeholder="New writer  name">

                            <input type="submit" value="Añadir" name="register">
                        </form>
                    
                    <div style="color: red">
                        <?php echo $message; ?>
                    </div>
            <div>
            <table>
                <thead>
                    <th colspan="4"> Escritores </th>
                </thead>
                <tbody>
 
                    <?php
        $count = 1;
        //Recogemos todos los Usuarios
        $query= getNodes("WRITER");
        $result = $client->run($query);
        foreach ($result->getRecords() as $records ) {
          $nombre= $records->get('n')->value('name');
            if($count==1) {
          ?>
                        <!-- Pasamos el nombre como argumento al pulsar el botón -->
                        
                        <tr>
                            <td><a href="writer_profile.php?user=<?php echo $nombre ?>"><?php echo $nombre ?></a></td>
                            <td> 
                            <form method="post" action="writer_delete.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $nombre?>">

                            <input type="submit" value="X" name="register">
                            </form> </td>
                            <?php $count=2;
                        } else if ($count==2) { ?>
                             <td><a href="writer_profile.php?user=<?php echo $nombre ?>"><?php echo $nombre ?></a></td>
                            <td> 
                            <form method="post" action="writer_delete.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $nombre?>">

                            <input type="submit" value="X" name="register">
                            </form> </td>
                            </tr>
                       <?php $count=1;
                            }

                        } ?>
                    
                </tbody>
            </table>
            </div>
            </div>
        </div>
    </div>

</body>

</html>