<?php

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
    <title> Libros </title>
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
                <li> <a class="active" href="book_list.php"> Libros </a> </li>
                <li> <a href="genre_list.php"> Generos </a> </li>
                <li> <a href="writer_list.php"> Escritores </a> </li>
                <li> <a href="index.php"> Contacto </a> </li>
            </ul>
        </div>

        <div id="body">                 
                <div>
                        <form method="post" action="write_book.php">
                            <label> Añade un Libro: </label>
                            <br>
                            <label for="book"> Título: </label>
                            <input type="text" name="book" placeholder="Titulo">
                            <br>
                            <label for="writer"> Autor: </label>
                             <input list="writer" name="writer">
                                <datalist id="writer">
                               <?php 
                                $query2= getNodes("WRITER");
                                $result2 = $client->run($query2);
                                foreach ($result2->getRecords() as $records2 ) {
                                    $nombre= $records2->get('n')->value('name');
                                    ?>
                               <option value="<?php echo $nombre?>"><?php echo $nombre?> </option>
                                <?php } ?>                         
                       </datalist>
                            <input type="submit" value="Añadir" name="addBook">
                        </form>
                    

            <div>
            <table>
                <thead>
                    <th colspan="4"> Libros </th>
                </thead>
                <tbody>
 
                    <?php
        $count = 1;
        //Recogemos todos los Libros
        $query= getNodes("BOOK");
        $result = $client->run($query);
        foreach ($result->getRecords() as $records ) {
          $title= $records->get('n')->value('title');
            if($count==1) {
          ?>
                        <!-- Pasamos el nombre como argumento al pulsar el botón -->
                        
                        <tr>
                            <td><a href="book_profile.php?book=<?php echo $title ?>"><?php echo $title ?></a></td>
                            <td> 
                            <form method="post" action="book_delete.php">
                            <input style="display:none" type="text" name="book" value="<?php echo $title?>">

                            <input type="submit" value="X" name="register">
                            </form> </td>
                            <?php $count=2;
                        } else if ($count==2) { ?>
                             <td><a href="book_profile.php?book=<?php echo $title ?>"><?php echo $title ?></a></td>
                            <td> 
                            <form method="post" action="book_delete.php">
                            <input style="display:none" type="text" name="book" value="<?php echo $title?>">

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