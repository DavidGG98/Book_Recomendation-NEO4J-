<?php
  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

  include ('funciones.php');
  //Create the conection
  require_once ('vendor/autoload.php');
  use GraphAware\Neo4j\Client\ClientBuilder;
  $client = ClientBuilder::create()
      ->addConnection('default', 'http://neo4j:david98@localhost:7474')
      ->build();

  

?>
<html>

<head>
    <title> Pruebas </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</head>

<body>
        <div id="main">

        <div id="topbar"> Recomiendame un libro </div>
        
        <div id="lateralmenu">
            <ul class="menu">
                <li> <a href="index.php"> Home </a> </li>
                <li> <a class="active" href="user_list.php"> Lectores </a> </li>
                <li> <a href="book_list.php"> Libros </a> </li>
                <li> <a href="genre_list.php"> Generos </a> </li>
                <li> <a href="writer_list.php"> Escritores </a> </li>
                <li> <a href="index.php"> Contacto </a> </li>
            </ul>
        </div>

        <div id="body">
            <div>
                <a href="" , class="linkbutton"> Nuevo usuario </a>
                <a href="" , class="linkbutton"> Borrar usuario </a>
            </div>
            <div> </div>
            <div>
            <table>
                <thead>
                    <th colspan="2"> Lectores </th>
                </thead>
                <tbody>
                    <form action="user_profile.php" method="get">
                        <?php
        $count = 1;
        //Recogemos todos los Usuarios
        $query= getNodes("READER");
        $result = $client->run($query);
        foreach ($result->getRecords() as $records ) {
          $nombre= $records->get('n')->value('name');
            if($count==1) {
          ?>
                        <!-- Pasamos el nombre como argumento al pulsar el botón -->
                        
                        <tr>
                            <td><button type="submit" name="user" value="<?php echo $nombre ?>"><?php echo $nombre ?> </button></td>
                            <?php $count=2;
                        } else if ($count==2) { ?>
                            <td><button type="submit" name="user" value="<?php echo $nombre ?>"><?php echo $nombre ?> </button>
                            </td>
                            </tr>
                       <?php $count=1;
                            }

                        } ?>
                    </form>
                </tbody>
            </table>
            
            </div>
        </div>
    </div>

</body>

</html>
