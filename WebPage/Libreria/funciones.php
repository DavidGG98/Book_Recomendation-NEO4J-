<?php


    //Returns all the nodes with he given label
    function getNodes ($label) {
        if($label=="BOOK") {
            return "MATCH (n:$label) RETURN n ORDER BY n.title";
        } else if($label=="WRITER") {
            return "MATCH (n:$label) RETURN n ORDER BY n.name";
        } else if($label=="GENRE") {
            return "MATCH (n:$label) RETURN n ORDER BY n.genre";
        } else {
            return "MATCH (n:$label) RETURN n ORDER BY n.name";
        }
    }

    //Dado un libro, devuelve la relación con sus lectores
    function getReaders ($book) {
      return "MATCH (b:BOOK {title:'$book'})<-[r]-(n:READER) RETURN n,r,b ORDER BY r.grade DESC";
    }
    //Dado un libro, devuelve su escritor
    function getWriter ($book) {
        return "MATCH (b:BOOK {title:'$book'})<-[]-(w:WRITER) return w";
    }
    //Dado un escritor, devuelve los libros escritos por esa persona
    function getWrittenBooks ($writer) {
        return "MATCH (b:BOOK)<-[]-(w:WRITER {name:'$writer'}) return b ORDER BY b.title";
    }
    // Dado un genero, devuelve todos los libros que pertenecen a dicho genero
    function getGenreBooks ($genre) {
        return "MATCH (b:BOOK)-[]->(w:GENRE {genre:'$genre'}) return b ORDER BY b.title";
    }
    //Dado un libro, devuelve los generos a los que pertenece
    function getGenre ($book) {
        return "MATCH (b:BOOK{title:'$book'})-[]->(g:GENRE) return g";
    }
    //Dado un lector, devuelve todos los libros que ha leido
    function getBooks ($user) {
      return "MATCH (n:READER {name:'$user'})-[r]-(b:BOOK) RETURN n,r,b ORDER BY r.grade DESC, b.title";
    }
    //Devuelve a los usuarios que han leido los mismos libros que el usuario
    function getNeighbors ($user) {
      return "MATCH (n:READER {name:'$user'})-[r]-(b)-[r2]-(m:READER)
      WHERE r2.grade= r.grade+1 OR r2.grade=r.grade OR r2.grade= r.grade-1
      RETURN DISTINCT m
      LIMIT 3";
    }


    //CREADORES DE NODOS
    function newReader ($user) {
        return "MERGE (n:READER {name:'$user'}) RETURN n";
    }

    function newWriter ($user) {
        return "MERGE (n:WRITER {name:'$user'}) RETURN n";
    }

    function newBook ($book) {
        return "MERGE (n:BOOK {title:'$book'}) RETURN n";
    }

    function newGenre ($user) {
        return "MERGE (n:GENRE {genre:'$user'}) RETURN n";
    }

    //CREADORES DE RELACIONES
    function readBook($user, $book, $grade) {
        return
        "MATCH (n:READER {name:'$user'}),(b:BOOK{title:'$book'})
        MERGE(n)-[r:READ]->(b)
        ON CREATE SET r.grade=$grade
        ON MATCH SET r.grade=$grade
        RETURN n,r,b";
    }

    function unReadBook($user,$book) {
        return "MATCH (n:READER {name:'$user'})-[r:READ]->(b:BOOK{title:'$book'})
        DELETE r";
    }

    function writeBook ($writer, $book) {
        return "MATCH (n:WRITER {name:'$writer'}), (b:BOOK{title:'$book'})
        MERGE (n)-[:WROTE]->(b) RETURN n,b";
    }

    function unWriteBook ($writer, $book) {
        return "MATCH (n:WRITER {name:'$writer'})-[r:WROTE]->(b:BOOK{title:'$book'})
        DELETE r";
    }

    function isGenre ($genre, $book) {
        return "MATCH (n:GENRE {genre:'$genre'}), (b:BOOK{title:'$book'})
        MERGE (b)-[:IS]->(n)";
    }
    function unDoGenre ($genre, $book) {
        return "MATCH (n:GENRE {genre:'$genre'})<-[r:IS]-(b:BOOK{title:'$book'})
        DELETE r";
    }



    function deleteNode ($id) {
        return "MATCH (n) WHERE ID(n)=$id DETACH DELETE n";
    }
    //Retunt the node with the given ID
    function getByID($ID) {
      return "MATCH (n) WHERE ID(n)=$ID RETURN n";
    }



?>
