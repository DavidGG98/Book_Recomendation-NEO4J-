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

  if(isset($_GET['book']) ){

    $book=$_GET['book'];
      
  } else {
    $book='1984'; //Default user
  }



  $query=getReaders($book);
  $result=$client->run($query);

  //Array de lectores y notas
  $r = array (); //Libros(books)
  $m = array (); //Notas (marks)

  //Creamos el array de libros y notas
  foreach ($result->getRecords() as $records) {
    array_push($r, $records->get('n')->value('name')); //Titulo del libro
    array_push($m, $records->get('r')->value('grade')); //Nota dada por el Usuarios
  }
  //Combinamos los arrays anteriores para obtener el array final
  $lectores=array_combine($r,$m);
  //Borramos los arrays temporales
  unset($r);
  unset($m);

    $query=getWriter($book);
    $result=$client->run($query);
    $writer=$result->getRecord()->get('w')->value('name');

    $genres= array();

    $query=getGenre($book);
    $result=$client->run($query);
    foreach ($result->getRecords() as $records) {
        array_push($genres, $records->get('g')->value('genre'));
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
                  <li> <a class="active" href="book_list.php"> Libros </a> </li>
                  <li> <a href="genre_list.php"> Generos </a> </li>
                  <li> <a href="writer_list.php"> Escritores </a> </li>
                  <li> <a href="index.php"> Contacto </a> </li>
              </ul>
          </div>

          <div id="body">

              <div>
                  <div>
                      Titulo:<?php echo $book ?> <br>
                      Autor:<a href="writer_profile.php?user=<?php echo $writer ?>"><?php echo $writer ?> </a>
                  </div>
                  <div>
                <form method="post" action="book_delete.php">
                            <input style="display:none" type="text" name="book" value="<?php echo $nombre?>">

                            <input type="submit" value="Borrar este libro" name="register">
                            </form>
                <a  class="linkbutton" href="book_list.php"> Lista de Libros </a>
            </div>
                  
                  
                  <div>
                   <form method="post" action="read_book.php">
                            <input style="display:none" type="text" name="book" value="<?php echo $book?>">
                            <label> Añadir Lector </label> <br>
                            <label for="title"> Nombre</label>  
                       <input list="reader" name="user">
                       <datalist id="reader">
                           <?php 
                            $query2= getNodes("READER");
                            $result2 = $client->run($query2);
                            foreach ($result2->getRecords() as $records2 ) {
                                $nombre= $records2->get('n')->value('name');
                                ?>
                           <option value="<?php echo $nombre?>"><?php echo $nombre?> </option>
                            <?php } ?>                         
                       </datalist>
                       <label for="grade"> Nota</label>
                            <select name="grade">
                            <?php for($i=1;$i<6;$i++) { ?>
                                <option value="<?php echo $i?> "> <?php echo $i ?></option>
                               <?php } ?>
                            </select>
                       <br>
                            <input type="submit" value="Añadir" name="addReader">
                        </form>
                  </div>
                  
                  
                <table>
                  <thead>
                    <th> Libros </th>
                    <th> Nota </th>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($lectores as $r=>$m) {
                    ?>
                    <tr>
                      <td><a href="user_profile.php?user=<?php echo $r ?>"><?php echo $r ?></a></td>
                      <td> <?php echo $m ?> </td>
                        <td> 
                            <form method="post" action="unread_book.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $r?>">
                            <input style="display:none" type="text" name="book" value="<?php echo $book?>">
                            <input type="submit" value="X" name="register">
                            </form> </td>
                    </tr>
                  <?php } ?>
                  </tbody>
                </table>
                  
                  
                <table>
                  <th> Generos </th>
                    <?php
                    foreach ($genres as $g) {
                    ?>
                    <tr><td><?php echo $g?> </td></tr>
                    <?php } ?>
                  </table>
                  
              
                  
                  
          </div>
      </div>
  </div>
</body>

</html>
