<?php
    // define the database variables as static
    define("DBHOST","localhost");
    define("DBNAME","tap");
    define("DBUSER","root");
    define("DBPASS",'');
    define("DBCONNSTRING", "mysql:host=".DBHOST.";dbname=".DBNAME);


  // this opens the connection to the database by creating a pdo object
    function getConnection(){

        try{
            
            $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // error handling
            return $pdo;

        }catch(PDOException $e){//catches exceptions thrown by pdo
            die($e->getMessage());
        }
    }


    function executeQuery(PDO &$pdo, String $sql, $parameters=array()){

        // if the parameter values were sent not as an array
        if(!is_array($parameters)){
            $parameters = array($parameters);
        }

        // create empty statement to hold results
        $statement = null;

        // when there are parameters we prepare the statement and bind the values to the sql query
        if(count($parameters)>0){

            $statement = $pdo->prepare($sql);
            $successful_execution = $statement->execute($parameters);

            //when the execution was not successful throw an error
            if(!$successful_execution){
                throw new PDOException;
            }

        }else{// when the query doesn't need preparing (no parameters to bind)

            $statement = $pdo->query($sql);

            //when the execution was not successful throw an error
            if(!$statement){
                throw new PDOException;
            }

        }
        return $statement; // return the results of executing the query

    }


    function closeConnection(&$pdo) {
        $pdo = null;
    }


?>