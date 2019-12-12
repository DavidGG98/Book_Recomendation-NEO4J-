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
    if($_GET['user']=="") {
         $user='David Gonzalez';
    } else {
    $user=$_GET['user'];
    }
      
  } else {
    $user='David Gonzalez'; //Default user
  }



  $query=getBooks($user);
  $result=$client->run($query);

  //Array temporales de libros y notas para el usuario
  $b = array (); //Libros(books)
  $m = array (); //Notas (marks)

  //Creamos el array de libros y notas
  foreach ($result->getRecords() as $records) {
    array_push($b, $records->get('b')->value('title')); //Titulo del libro
    array_push($m, $records->get('r')->value('grade')); //Nota dada por el Usuarios
  }
  //Combinamos los arrays anteriores para obtener el array final
  $librosUser=array_combine($b,$m);
  //Borramos los arrays temporales
  unset($b);
  unset($m);

  //Create user comunity

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
                  <li> <a class="active" href="user_list.php"> Lectores </a> </li>
                  <li> <a href="book_list.php"> Libros </a> </li>
                  <li> <a href="genre_list.php"> Generos </a> </li>
                  <li> <a href="writer_list.php"> Escritores </a> </li>
                  <li> <a href="index.php"> Contacto </a> </li>
              </ul>
          </div>

          <div id="body">
              
              <div>
                  <?php echo $user ?>
                                  
            <div>
                <form method="post" action="user_delete.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $user?>">

                            <input class="clickbutton" type="submit" value="Borrar Usuario" name="register">
                            </form>
                <a  class="linkbutton" href="user_list.php"> Lista de usuarios </a>
            </div>
                  
                  
                  <div>
                   <form method="post" action="read_book.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $user?>">
                            <label> Añadir libro leido </label> <br>
                            <label for="title"> Titulo</label>  
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
                       <label for="grade"> Nota</label>
                            <select name="grade">
                            <?php for($i=1;$i<6;$i++) { ?>
                                <option value="<?php echo $i?> "> <?php echo $i ?></option>
                               <?php } ?>
                            </select>
                       <br>
                            <input type="submit" value="Añadir" name="register">
                        </form>
                  </div>
                  
                  
                <table>
                  <thead>
                    <th> Libros </th>
                    <th> Nota </th>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($librosUser as $b=>$m) {
                    ?>
                    <tr>
                      <td> <a href="book_profile.php?book=<?php echo $b ?>"><?php echo $b ?></a> </td>
                      <td> <?php echo $m ?> </td>
                        <td> 
                            <form method="post" action="unread_book.php">
                            <input style="display:none" type="text" name="user" value="<?php echo $user?>">
                            <input style="display:none" type="text" name="book" value="<?php echo $b?>">
                            <input type="submit" value="X" name="unRead">
                            </form> </td>
                    </tr>
                  <?php } ?>
                  </tbody>
                </table>

                  
              <table>
                <?php
                $query=getNeighbors($user);
                $result=$client->run($query);

                $userlist=array();
                foreach ($result->getRecords() as $records) {
                  array_push($userlist, $records->get('m')->value('name')); //nombre de usuario
                }
                ?>
                <thead>
                  <th> Usuarios Vecinos </th>
                </thead>
                <tbody>
                  <?php
                  foreach ($userlist as $b) {
                  ?>
                  <tr>
                    <td> <?php echo $b ?> </td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
                  
                  
              <div>
              <form action="user_neighbors.php" method="get">
              <button type="submit" name="user" value="<?php echo $user ?>"> Ver vecinos </button>
              </form>
              </div>
                  
                  
              <div>
                <form action="user_recomendation.php" method="get">
                <button type="submit" name="user" value="<?php echo $user ?>"> Ver recomendación </button>
                </form>
              </div>
                  
                  
          </div>
      </div>
  </div>
</body>

</html>
