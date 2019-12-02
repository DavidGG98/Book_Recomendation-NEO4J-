<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado

    //Nodos [READE, WRITER, BOOK, GENRE];
    require_once ('vendor/autoload.php');
    include ('funciones.php');

    use GraphAware\Neo4j\Client\ClientBuilder;

    $client = ClientBuilder::create()
        ->addConnection('default', 'http://neo4j:david98@localhost:7474')
        ->build();

    /**
    *Crea una query
    *$query = "MATCH (n:WRITER {name:'William Shakespeare' }) RETURN n.name as name";
    *manda la query y la guarda en $result
    *$result = $client->run($query);
    *record = $result->getRecord();
    *echo $record->value('name');
    */

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
    <table>
      <thead>
        <th> Lectores </th>
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
          ?>
          <!-- Pasamos el nombre como argumento al pulsar el botón -->
        <tr>
        <td><button type="submit" name="user" value="<?php echo $nombre ?>"><?php echo $nombre ?> </button></td>
        </tr>

        <?php } ?>
      </form>
        </tbody>
        </table>
  </div>
  <div class="inside">
    <table>
      <thead>
        <th> Libros </th>
      </thead>
    <tbody>

      <?php
        $count = 1;
        //Recogemos todos los Usuarios
        $query= getNodes("BOOK");
        $result = $client->run($query);
        foreach ($result->getRecords() as $records ) {

          ?>
        <tr>
        <td><?php  echo  $records->get('n')->value('title'); ?></td>
        </tr>

        <?php } ?>
    </tbody>
    </table>
  </div>
  <div class="inside">
    <table>
      <thead>
        <th> Generos </th>
      </thead>
    <tbody>

      <?php
        $count = 1;
        //Recogemos todos los Usuarios
        $query= getNodes("GENRE");
        $result = $client->run($query);
        foreach ($result->getRecords() as $records ) {

          ?>
        <tr>
        <td><?php  echo  $records->get('n')->value('genre'); ?></td>
        </tr>

        <?php } ?>
    </tbody>

    </table>
  </div>
</div>


</body>

</html>
