<?php
require_once "Project.php";
require_once "db.php";

class ProjectGateway
{

    private static String $baseSQL = "SELECT * FROM project";


    function __construct() {}


    function getProjectByID($project_id)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE project_id = :project_id;";

        $results = executeQuery($pdo, $sql, [":project_id" => $project_id]);

        $data = $results->fetchObject("Project");

        closeConnection($pdo);

        return $data;
    }



    public function assignProject($project_id, $leader_id){

        $pdo = getConnection();

        try {
            //start a transaction
            $pdo->beginTransaction();
    
            //perform the update
            $sql = "UPDATE project SET teamLeader = :leader_id WHERE project_id = :project_id";
            executeQuery($pdo, $sql, [":leader_id" => $leader_id, ":project_id" => $project_id]);
    
            // no exception happened if it got to this line of code
            // so we commit the transaction
            $pdo->commit();
    
            closeConnection($pdo);
    
            return true;//success
    
        } catch (Exception $e) {
            // rollback the transaction when catching an error
            $pdo->rollBack();
            closeConnection($pdo);

            return false;//failure
        }
    }



    function getUnassignedProjects()
    {
        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE teamLeader IS NULL ORDER BY startDate ASC;";

        $results = executeQuery($pdo, $sql, []);

        $projects = [];

        // add Project objects to a list
        while ($data = $results->fetchObject("Project")) {
            $projects[] = $data;
        }

        closeConnection($pdo);

        return $projects;
    }


    public function InsertProject($params)
    {
        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "INSERT INTO `project` (`project_id`, `projectTitle`, `projectDescription`, `budget`, `startDate`, `endDate`, `clientName`, `files`) 
            VALUES (:project_id, :projectTitle, :projectDescription, :budget, :startDate, :endDate, :clientName, :files )";

            // execute the query
            executeQuery($pdo, $sql, $params);

            // commit the transaction
            $pdo->commit();

            closeConnection($pdo);

            return true; // success

        } catch (Exception $e) {
            // rollback the transaction in case of an error
            $pdo->rollBack();
            closeConnection($pdo);

            return false; // failure
        }
    }

    public function getProjectByLeader($teamLeader){

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE teamLeader = :teamLeader AND endDate >= CURDATE();";

        $results = executeQuery($pdo, $sql, [":teamLeader" => $teamLeader]);

        $projects = [];

        // add Project objects to a list
        while ($data = $results->fetchObject("Project")) {
            $projects[] = $data;
        }

        closeConnection($pdo);

        return $projects;

    }





}


?>