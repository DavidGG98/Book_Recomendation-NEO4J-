# Book_Recomendation(NEO4J)
 Book recomendarion system in neo4j database and html web 
 
 Herramientas necesarias:
 
 Neo4J para la base de datos
 XAMPP para hospedar el servidor web
 
 #Cargar base de datos#
 1.- Creamos una nueva base de datos
 2.- Añadimos un nuevo grafo
 3.- Iniciamos el grafo
 4.- Hacemos click en MANAGE
 5.- Hacemos click en OPEN FOLDER
 6.- Copiamos los archivos CSV de la carpeta CSV del GITHUB en la carpeta imports de la base de datos
 7.- Abrimos el browser del grafo
 8.- Introducimos los siguientes comandos
 
 LOAD CSV WITH HEADERS FROM 'file:///BOOKS_AND_WRITERS.csv' AS line FIELDTERMINATOR ';'
UNWIND split(line.Genre, '|') AS genres
MERGE(w:WRITER {name:line.Writer})
MERGE(b:BOOK {title:line.Title, pages:line.Pages, rating:line.Rating, rating_count:line.Rating_Count, review_count:line.Review_Count} )
MERGE(g:GENRE {genre: genres})
MERGE(w)-[:WROTE]->(b)
MERGE (b)-[:IS]->(g)

LOAD CSV WITH HEADERS FROM 'file:///USERS.csv' AS line FIELDTERMINATOR ';'
MERGE(n:READER {name:line.USERS1})
MERGE(m:READER {name:line.USERS2})
MERGE(b1:BOOK {title:line.BOOKS1})
MERGE(b2:BOOK {title:line.BOOKS2})
MERGE(n)-[r1:READ]->(b1)
ON CREATE SET r1.grade=line.GRADE1
ON MATCH SET r1.grade=line.GRADE1
MERGE(m)-[r2:READ]->(b2)
ON CREATE SET r2.grade=line.GRADE2
ON MATCH SET r2.grade=line.GRADE2
 
 Despues de esto ya estará creada la base de datos.
 
 #Servidor Web# 
 
 1.- Descargamos XAMPP
 2.- Abrimos la carpeta XAMPP
 3.- Abrir el controlador xampp_control.exe
 4.- Iniciar el servidor apache (Apache -> start)
 5.- Introducir la carpeta libreria dentro de htdocs
 6.- Introducir en el navegador http://localhost/libreria/
 
 ##NOTAS##
 Para que la aplicación funcione correctamente, el servidor de neo4j y apache deben estar encendidos.
