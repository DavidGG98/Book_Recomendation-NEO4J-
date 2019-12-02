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
    $user=$_GET['user'];
    $query=getNeighbors($user);
    $result=$client->run($query);

    $userlist=array(); //array with the neighbors users
    array_push( $userlist, $user);
    $booklist= array(); //Array with all the books
    $gradematrix = array (); //matrix with the grades of every user to everybook
    foreach ($result->getRecords() as $records) {
      array_push($userlist, $records->get('m')->value('name')); //nombre de usuario
    }
    /**For every user in userlist, we add a book in booklist if it is not already include
    * in the grades [] [] array we introduce the mark given by the user (row) to that book (column)
    * at the end, all the empy columns are placed to 0
    */
    $row=0; //Users in $userlist
    $userBooks=0;
    foreach ($userlist as $u) {
      //Get all the books for the user
      $query=getBooks($u);
      $result=$client->run($query);
      foreach ($result->getRecords() as $records) {
        $b=$records->get('b')->value('title'); //Get the book $title
        $r=$records->get('r')->value('grade'); //Get the relationship grade between the book and the user
        if (in_array($b, $booklist)) {  //Check if the book is in the list
            $col=array_search($b,$booklist); //get the position
            $gradematrix [$row][$col] = $r; //add the grade to the matrix
            //echo "$b ya existe en la posicion $col, se le añade la nota $r para el usuario $row <br>";
        } else {
          array_push($booklist,$b); //introduce the book into the array
          $col=array_search($b,$booklist); //get the position
          $gradematrix [$row][$col] = $r; //add the grade to the matrix
          //echo "$b se ha creado en la posicion $col, se le añade la nota $r para el usuario $row <br>";
        }
      }
      $row++;
      if($u==$user) {
        $userBooks=count($booklist);
        echo "numero de libros para el primer user $userBooks <br>";
      }
    }
    //Place 0 (not READ) in every gap with no array_count_values
    $rows=count($userlist);
    $columns=count($booklist);
    for ($r=0; $r<$rows;$r++) {
      $aux=$gradematrix[$r];
      for ($c=0; $c<$columns; $c++) {
        if(!array_key_exists($c, $aux)) {
          $gradematrix[$r][$c]=0;
          //echo "El usuario $r no ha leido el libro $c, por lo que se le añade un 0 <br>";
        }
      }
    }

    //Matriz de predicciones
    echo "Numero de libros=$columns <br>";
    $finalGrade=array();
    for($c=0;$c<$columns;$c++) {//Por cada producto
      if($gradematrix[0][$c]== 0) { //Si el usuario no lo ha puntuado
        $ncomp=array (); //Usuarios que compararon el producto con otro X
        $diff=array (); //Diferencia marcas de los productos
        $m=array();
        $l=array(); //Array con los objetos valorados

        for ($c2=0;$c2<$userBooks;$c2++) { //por cada producto a comparar
          if ($c2 != $c) { //No comparamos con nosotros mismos
            //comprobamos que el usuario inicial ha valorado el producto a comparar
          $n=0; //Personas que compararon = 0
            for ($r=1;$r<$rows;$r++) { //Por cada usuario
              if($gradematrix[$r][$c] != 0 && $gradematrix[$r][$c2] != 0 ) { //El usuario ha puntuado ambos productos
                //Comparamos la nota de ambos productos para ese usuario y la sumamos al total
                //Añadimos un usuario al total de usuarios que compararon
                $n++;
                array_push($diff,$gradematrix[$r][$c] - $gradematrix[$r][$c2]); //introducimos la diferencia en el array

              }
            }
            if($n!=0){
              $d=array_sum($diff)/$n;
              $d2=(array_sum($diff)/$n)+$gradematrix[0][$c2];
              //echo " comparación $c,$c2: NºUsers= $n, media dif=$d, total=$d2 <br>";
              array_push($m,(array_sum($diff)/$n)+$gradematrix[0][$c2]); //media de las diferencias + puntuacion
              array_push($ncomp, $n); //Cargamos las personas que compararon
            }
          }
        }
      //Acabamos de comparar todos los productos
      $prediction=0;
    //  echo"Comparación libro $c <br>";
      for($i=0;$i<count($m);$i++) {
        $prediction=$m[$i]*$ncomp[$i] + $prediction;
    //    echo "$prediction <br>";
      }
      $prediction=$prediction/array_sum($ncomp);
      if($prediction > "5") {
  //      echo "$prediction es mayor que 5 <br>";
  //      $prediction=5;
  //      echo " Ajuste a $prediction <br>";
        $gradematrix[0][$c]=$prediction;
      } else if ($prediction < "1") {
    //      echo "$prediction es menor que 1 <br>";
        $prediction=1;
    //      echo "ajuste a $prediction  <br>";
        $gradematrix[0][$c]=$prediction;
      } else {
      $gradematrix[0][$c]=$prediction;
    }
    }
  }



?>
<html>

<head>
    <title> Recomendation </title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css?v=<?php echo(rand()); ?>" />
</head>

<body>
  <div class="body">
    <table>
      <tr>
        <td></td>
        <td></td>
      <?php foreach ($userlist as $u) { ?>

        <td>
      <?php echo  $u ?>
    </td>
      <?php } ?>
</tr>

<?php $n=0; foreach ($booklist as $u) { ?>
      <tr>
          <td> <?php echo  $n ?> </td>
          <td> <?php echo  $u ?> </td>
          <?php for ($c=0;$c<$rows;$c++) { ?>
            <td> <?php echo $gradematrix[$c][$n] ?> </td>
          <?php } ?>

      </tr>
              <?php $n++; } ?>

  </div>
</body>
</html>
