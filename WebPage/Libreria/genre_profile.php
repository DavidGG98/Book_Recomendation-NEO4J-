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

  if(isset($_GET['genre']) ){

    $genre=$_GET['genre'];
      
  } else {
    $genre='Romance'; //Default user
  }



  $query=getGenreBooks($genre);
  $result=$client->run($query);

  //Array de libros del escritor
    $libros= array();
   foreach ($result->getRecords() as $records ) {
       array_push($libros,$records->get('b')->value('title'));  
   }
?>
<html>
<header>
    <title> Perfil de usuario </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</header>

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
                
            </ul>
        </div>

        <div id="body">

            <div>
                <?php echo $genre ?>

                <div>
                    <form method="post" action="genre_delete.php">
                        <input style="display:none" type="text" name="user" value="<?php echo $genre?>">

                        <input class="clickbutton" type="submit" value="Borrar genero" name="register">
                    </form>
                    <a class="linkbutton" href="genre_list.php"> Lista de Géneros </a>
                </div>

                <div>
                    <form method="post" action="is_genre.php">
                        <input style="display:none" type="text" name="genre" value="<?php echo $genre?>">
                       <br>
                        <label for="title"> Añadir libro</label>
                        <input list="book" name="book">
                        <datalist id="book">
                            <?php 
                            $query2= getNodes("BOOK");
                            $result2 = $client->run($query2);
                            foreach ($result2->getRecords() as $records2 ) {
                                $nombre= $records2->get('n')->value('title');
                                ?>
                            <option value="<?php echo $nombre?>"><?php echo $nombre?> </option>
                            <?php } ?>
                        </datalist>

                        <input type="submit" value="Añadir" name="register">
                    </form>
                </div>

                <div style="margin-top:20px;">

                    <table>
                        <thead>
                            <th colspan="4"> Libros </th>
                        </thead>
                        <tbody>
                            <?php
                    $c=1;
                    foreach ($libros as $b) {
                            if($c==1) { ?>
                            <tr>
                                <td> <a href="book_profile.php?book=<?php echo $b ?>"><?php echo $b ?></a> </td>

                                <td>
                                    <form method="post" action="unWrite_book.php">
                                        <input style="display:none" type="text" name="user" value="<?php echo $user?>">
                                        <input style="display:none" type="text" name="book" value="<?php echo $b?>">
                                        <input type="submit" value="X" name="register">
                                    </form>
                                </td>
                                <?php $c=2;
                                      } else if ($c==2) { ?>
                                <td> <a href="book_profile.php?book=<?php echo $b ?>"><?php echo $b ?></a> </td>

                                <td>
                                    <form method="post" action="unWrite_book.php">
                                        <input style="display:none" type="text" name="user" value="<?php echo $user?>">
                                        <input style="display:none" type="text" name="book" value="<?php echo $b?>">
                                        <input type="submit" value="X" name="register">
                                    </form>
                            </tr>

                            <?php $c=1;
                        } } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>