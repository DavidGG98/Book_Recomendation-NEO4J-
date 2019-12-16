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
        $user='David Gonzalez'; //Default user
    } else {
    $user=$_GET['user'];
 }
  } else {
    $user='David Gonzalez'; //Default user
  }
    
    //NUMERO DE MEJORES CANDIDATOS A COMPARAR
    $MAXUSERS=5;
/*
    $userbooks=array ();
    $query=getBooks($user);
    $result=$client->run($query);



    //ArrayTemp
    $b=array();
    $m=Array();
    foreach ($result->getRecords() as $records) {
        array_push($b,$records->get('b')->value('title')); //Get the book $title
        array_push($m, $records->get('r')->value('grade')); //Get the grade

    }
    $userbooks=array_combine($b,$m); //Array with user books and marks
    //Free the variable
    unset($b);
    unset($m);
*/

    $query=getNeighbors($user);
    $result=$client->run($query);

    $userlist=array(); //array with the neighbors users
    array_push( $userlist, $user); //We add the actual user as first user
    $booklist= array(); //Array with all the books
    $gradematrix = array (); //matrix with the grades of every user to everybook
    foreach ($result->getRecords() as $records) {
      array_push($userlist, $records->get('m')->value('name')); //nombre de usuario
    }

   
    
    /**For every user in userlist, we add a book in booklist if it is not already include
    * in the grades [] [] array we introduce the mark given by the user (row) to that book (column)
    * at the end, all the empy columns are placed to 0
    */
    $row=0; //candidates in $userlist
    $n=0; //Books for the user

    $query=getBooks($user);
    $result=$client->run($query); //Cargamos los libros del usuario
    foreach ($result->getRecords() as $records) {
        $b=$records->get('b')->value('title'); //Get the book $title
        $r=$records->get('r')->value('grade'); //Get the relationship grade between the
        array_push($booklist,$b);
        $col=array_search($b,$booklist); //get the position
        $gradematrix [$row][$col] = $r; //add the grade to the matrix
    }
    
    /*
     *  CREAMOS UNA MATRIZ UNICAMENTE CON LOS  LIBROS DEL USUARIO PARA DETERMINAR CUAL 
     *  ES LA COMUNIDAD DEL USUARIO
     */

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
        } 
      }
      $row++;
      if($u==$user) {
        $n=count($booklist);
        echo "numero de libros para el primer user $n <br>";
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




    /*
     * AHORA TENEMOS LA MATRIZ DE NOTAS, AHORA BUSCAMOS LOS MEJORES CANDIDATOS
     */
   
    $coeficiente = array();
    for($i=1;$i<$rows;$i++) { //Empezamos en el user 1(user=0 es el de la rec)
        $sum=0;
        for ($j=0;$j<$n;$j++) {
            $a=$gradematrix[$i][$j]; //Cojemos la nota para el libro j del candidato
            $b=$gradematrix[0][$j]; //Cojemos la nota del libro j del usuario
            if ($a==$b) {
                $sum+=2; //Sumamos 2
            } else if  ($a!=0 and ($a-$b== 1 or $a-$b== (-1))) {
                $sum+=1; //sumamos 1
            }  //no sumamos nada
        }
        //Al acabar con el usuario obtenemos la meedia y la guardamos en la lista de medias
        array_push($coeficiente,($sum/$n));
    }
    $top = array();
    $count=1;
    $i=0;
    foreach ($coeficiente as $c) {
        //echo "Coeficiente usuario $count= $c <br>";
        
        if($i<$MAXUSERS) {
            $top[$i]=array();
            $top[$i][0]=$count;
            $top[$i][1]=$c;
            $i++;
        } else {
            $min= array();
            $min[0]= 0;
            $min[1]=$top[0][1];
            
            for ($j=1;$j<$MAXUSERS;$j++) {
                if ($top [$j][1]<$min[1]) {
                    $min[0]= $j;
                    $min[1]=$top[$j][1];                    
                }
            }
            if ($c>$min[1]) {
                $top[$min[0]][0]=$count;
                $top[$min[0]][1]=$c;
            }
            $i++;
        }
        $count++;
       
    }

    unset($i);
    unset($count);
    unset($coeficiente);

    foreach ($top as $t) {
        echo "<br> TOPUSER ",$t[0], " coef=", $t[1];
    }

    /*
     * Hemos obtenido los mejores X candidatos siendo $top[x] el numero de candidato y
     * $top [x] [y] el coeficiente para dicho candidato.
     * Ahora pasamos a realizar la recomendación unicamente con esos candidatos
     */

    /*
     * Ahora pasamos a completar la matriz de notas con los libros de los usuarios del top
     */
    $row=0;
    unset($gradematrix); //Limpiamos la matriz y pasamos a crear una nueva
    
    //Seguimos los pasos anteriores, solo que ahora unicamente añadimos los usuarios del top.

    $query=getBooks($user);
    $result=$client->run($query); //Cargamos los libros del usuario
    foreach ($result->getRecords() as $records) {
        $b=$records->get('b')->value('title'); //Get the book $title
        $r=$records->get('r')->value('grade'); //Get the relationship grade between the
        $col=array_search($b,$booklist); //get the position
        $gradematrix [$row][$col] = $r; //add the grade to the matrix
       
    }
    $row++;
    foreach ($top as $u) { //Para cada usuario del top
      //Get all the books for the user
        echo "<br> Cargamos las notas de ",  $userlist[$u[0]], "<br>";
      $query=getBooks($userlist[$u[0]]);
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
    }
    //Place 0 (not READ) in every gap with no array_count_values
    $rows=$MAXUSERS+1;
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

    /*
     * UNA VEZ HEMOS REALIZADO LA MATRIZ DE NOTAS PODEMOS PASAR A PREDECIR
     */

    //Matriz de predicciones
    echo "Libros usuario:$n";
    echo "Numero de libros=$columns <br>";
    $finalGrade=array();
    for($c=0;$c<$columns;$c++) {//Por cada producto
      if($gradematrix[0][$c]== 0) { //Si el usuario no lo ha puntuado
        $ncomp=array (); //Usuarios que compararon el producto con otro X
        $diff=array (); //Diferencia marcas de los productos
        $m=array();
        $l=array(); //Array con los objetos valorados

        for ($c2=0;$c2<$n;$c2++) { //por cada producto a comparar
            
          if ($c2 != $c) { //No comparamos con nosotros mismos

            //comprobamos que el usuario inicial ha valorado el producto a comparar
            $num=0; //Personas que compararon = 0
            $r=1;
            foreach ($top as $t) { //Por cada usuario
              if($gradematrix[$r][$c] != 0 && $gradematrix[$r][$c2] != 0 ) { //El usuario ha puntuado ambos productos
                
                //Comparamos la nota de ambos productos para ese usuario y la sumamos al total
                //Añadimos un usuario al total de usuarios que compararon
                $num++;
                array_push($diff,$gradematrix[$r][$c] - $gradematrix[$r][$c2]); //introducimos la diferencia en el array

              } 
                $r++;
            }
            if($num!=0){
              $d=array_sum($diff)/$num;
              $d2=(array_sum($diff)/$num)+$gradematrix[0][$c2];
              //echo " comparación $c,$c2: NºUsers= $n, media dif=$d, total=$d2 <br>";
              array_push($m,(array_sum($diff)/$n)+$gradematrix[0][$c2]); //media de las diferencias + puntuacion
              array_push($ncomp, $num); //Cargamos las personas que compararon
            
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
        //$prediction=1;
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
          <td> <?php echo $user ?></td>
      <?php foreach ($top as $u) { ?>

        <td>
      <?php echo  $userlist[$u[0]] ?>
    </td>
      <?php } ?>
</tr>

<?php $n=0; foreach ($booklist as $u) { ?>
      <tr>
          <td> <?php echo  $n ?> </td>
          <td> <?php echo  $u ?> </td>
          <?php for ($c=0;$c<($MAXUSERS+1);$c++) { ?>
            <td> <?php echo $gradematrix[$c][$n] ?> </td>
          <?php } ?>

      </tr>
              <?php $n++; } ?>
      </table>
  </div>
</body>
</html>