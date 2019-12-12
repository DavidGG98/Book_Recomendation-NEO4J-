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

  if(isset($_GET['user']) ){

    $user=$_GET['user'];
      
  } else {
    $user='A.A. Milne'; //Default user
  }



  $query=getWrittenBooks($user);
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
                <li> <a href="index.php"> Contacto </a> </li>
            </ul>
        </div>

        <div id="body">

            <div>
                <?php echo $user ?>

                <div>
                    <form method="post" action="writer_delete.php">
                        <input style="display:none" type="text" name="user" value="<?php echo $user?>">

                        <input class="clickbutton" type="submit" value="Borrar Usuario" name="register">
                    </form>
                    <a class="linkbutton" href="writer_list.php"> Lista de Escritores </a>
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
                                        <input style="display:none" type="text" name="user" value="<?php echo $user?>">
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