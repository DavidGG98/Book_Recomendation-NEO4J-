<?php


    //Returns all the nodes with he given label
    function getNodes ($label) {
      return "MATCH (n:$label) RETURN n";
    }

    function getReaders ($book) {
      return "MATCH (b:BOOK {title:'$book'})<-[r]-(n) RETURN n,r,b ORDER BY r.grade DESC";
    }

    function getBooks ($user) {
      return "MATCH (n:READER {name:'$user'})-[r]-(b:BOOK) RETURN n,r,b ORDER BY r.grade DESC";
    }

    function getNeighbors ($user) {
      return "MATCH (n:READER {name:'$user'})-[r]-(b)-[r2]-(m:READER)
      WHERE r2.grade= r.grade+1 OR r2.grade=r.grade OR r2.grade= r.grade-1
      RETURN DISTINCT m
      LIMIT 3";
    }


    // NO SE PUEDEN REUTILIZAR
    //function getRelations($user, $label) {
      //return "MATCH"
    //}

    //Retunt the node with the given ID
    function getByID($ID) {
      return "MATCH (n) WHERE ID(n)=$ID RETURN n";
    }

    function getrelbyid($ID) {
      return "MATCH (n)-[r]-(m) WHERE ID(n)=$ID RETURN n,r,m";
    }

    //CREATORS (TODO-> Modificadores en el mismo método?)
    function newReader($name) {
      return "MERGE (n:READER {name:'$name'})";
    }

    function newWriter($name) {
      return "MERGE (n:WRITER {name:'$name'})";
    }

    function newBook($title) {
      return "MERGE (n:BOOK {title:'$title'})";
    }

    //
?>
