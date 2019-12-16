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
    <title> Pruebas </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</head>

<body>
    <div id="main">

        <div id="topbar"> Recomiendame un libro </div>
        
        <div id="lateralmenu">
            <ul class="menu">
                <li> <a class="active" href="index.php"> Home </a> </li>
                <li> <a href="user_list.php"> Lectores </a> </li>
                <li> <a href="book_list.php"> Libros </a> </li>
                <li> <a href="genre_list.php"> Generos </a> </li>
                <li> <a href="writer_list.php"> Escritores </a> </li>
                <li> <a href="index.php"> Contacto </a> </li>
            </ul>
        </div>

        <div id="body">
            <div style="margin: 50px; font-size: 20">
            <p>Este sitio web esta diseñado para probar el sistema de recomendación de libros diseñado por David Gonzalez Gacís (NIF: 71464871-F) para la asignatura de Sistemas de Información de Gestion y Business Intelligence en la 
                <a style="text-decoration:underline; color: blue" href="https://www.unileon.es/"> Universidad de León </a>
                </p>
            <br>
            <p>El sitio permite la interacción completa con la base de datos, pudiendo crear desde 0 libros, generos, autores y lectores. La base de datos de prueba contiene 50 Lectores, 200 libros y alrededor de 180 autores.
            Para conseguir una recomendación se debe avanzar a la pestaña de Lectores (menu de la izquierda) y seleccionar un lector de la lista. Una vez en el perfil del lector, se mostrará la información relativa al lector, como todos los libros leidos por el mismo y las notas otorgadas, asi como otros lectores que conforman su comunidad (que han leido los mismos libros que él). Pulsando en el botón de recomendación, se pondrá en funcionamiento el algoritmo y se mostrarán los 5 libros más recomendados para el usuario en cuestión, basandose en los libros leidos y las puntuaciones dadas.
                </p>
            </div>
        </div>
    </div>
    </body>

</html>