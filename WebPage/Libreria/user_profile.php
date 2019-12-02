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

  <div class="body">
  <div class="inside">
    <?php echo $user ?>

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
</div>
<div class="inside">

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
</div>
<div class="space"> </div>
<form action="user_neighbors.php" method="get">
<button type="submit" name="user" value="<?php echo $user ?>"> Ver vecinos </button>
</form>
</div>
<div>
  <form action="user_recomendation.php" method="get">
  <button type="submit" name="user" value="<?php echo $user ?>"> Ver recomendación </button>
  </form>
</div>


  </body>

</html>
