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

  if(isset($_GET['user']) && $_GET['user']!="" ){

    $writer=$_GET['user'];
      
  } else {
    $writer='A.A. Milne'; //Default user
  }



  $query=getWrittenBooks($writer);
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
                <?php echo $writer ?>

                <div>
                    <form method="post" action="writer_delete.php">
                        <input style="display:none" type="text" name="writer" value="<?php echo $writer?>">

                        <button class="button delete" type="submit" value="Borrar Usuario" name="register"> Borrar Escritor </button>
                    </form>
                    <a class="linkbutton" href="writer_list.php"> Lista de Escritores </a>
                </div>
                <div>
                <form method="post" action="write_book.php">
                            <label> Escribe un libro: </label>
                            <br>
                              <input style="display:none" type="text" name="writer" value="<?php echo $writer?>">
                            <label for="book"> Título: </label>
                            <input type="text" name="book" placeholder="Titulo">
                            <br>
                            
                            <input type="submit" value="Añadir" name="register">
                        </form>
                </div>
                <div style="margin-top:20px;">


                    <table>
                        <thead>
                            <th> Libros </th>
                        </thead>
                        <tbody>
                            <?php
                    foreach ($libros as $b) {
                    ?>
                            <tr>
                                <td> <a href="book_profile.php?book=<?php echo $b ?>"><?php echo $b ?></a> </td>

                                <td>
                                    <form method="post" action="unWrite_book.php">
                                        <input style="display:none" type="text" name="writer" value="<?php echo $writer?>">
                                        <input style="display:none" type="text" name="book" value="<?php echo $b?>">
                                        <input type="submit" value="X" name="register">
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>