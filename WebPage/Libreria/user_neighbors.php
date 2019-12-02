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
//Match the users user_neighbors
$user=$_GET['user'];
  $query=getNeighbors($user);
  $result=$client->run($query);

  $userlist=array();
  foreach ($result->getRecords() as $records) {
    array_push($userlist, $records->get('m')->value('name')); //nombre de usuario
  }

?>
<html>

<head>
    <title> Pruebas </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</head>

<body>
  <div class="body">
    <div class="inside">
    <!-- Tabla con 3 columnas -->
    <table>
      <?php //We get all the users
        foreach ($userlist as $u){
          echo $u;
        $query=getBooks($u);
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
        // We get all the books for every user in the $userlist array
        //Print al the books for that user
        ?>
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
              <td> <?php echo $b ?> </td>
              <td> <?php echo $m ?> </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php } ?>
</table>
</body>
</html>
