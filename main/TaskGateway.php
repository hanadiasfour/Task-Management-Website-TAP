<?php
require_once "Task.php";
require_once "Allocation.php";
require_once "db.php";

class TaskGateway
{

    private static String $baseSQL = "SELECT * FROM task";


    function __construct() {}


    function getAllTasks()
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . ";";

        $results = executeQuery($pdo, $sql, []);

        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }

    

    function getLeaderProjectTasks($leader_id)
    {

        $pdo = getConnection();

        $sql = "SELECT t.* FROM task t JOIN project p ON t.associatedProject = p.project_id WHERE p.teamLeader = :teamLeader;";

        $results = executeQuery($pdo, $sql, ["teamLeader"=> $leader_id]);
        
        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }



    function getTaskByID($task_id)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE task_id = :task_id;";

        $results = executeQuery($pdo, $sql, [":task_id" => $task_id]);

        $data = $results->fetchObject("Task");

        closeConnection($pdo);

        return $data;
    }

    function getProjectNameByTaskID($task_id)
    {

        $pdo = getConnection();

        $sql = "SELECT p.projectTitle FROM task t JOIN project p ON t.associatedProject = p.project_id WHERE t.task_id = :task_id;";

        $results = executeQuery($pdo, $sql, [":task_id" => $task_id]);

        $data = $results->fetchColumn(); // fetchColumn to get the first column of the first row

        closeConnection($pdo);

        return $data;
    }



    public function assignProject($project_id, $leader_id)
    {

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

            return true; //success

        } catch (Exception $e) {
            // rollback the transaction when catching an error
            $pdo->rollBack();
            closeConnection($pdo);

            return false; //failure
        }
    }


    public function insertTask($params)
    {
        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "INSERT INTO `task` (`task_id`, `taskName`, `taskDescription`, `startDate`, `endDate`, `effort`, `status`, `priority`, `associatedProject`)
            VALUES (:task_id, :taskName, :taskDescription, :startDate, :endDate, :effort, :status, :priority, :associatedProject )";

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

    public function getUnassignedTasks($project_id)
    {

        $pdo = getConnection();

        $sql = "SELECT t.* FROM task t LEFT JOIN allocation a ON t.task_id = a.task_id WHERE a.task_id IS NULL AND t.associatedProject = :project_id;";

        $results = executeQuery($pdo, $sql, [":project_id" => $project_id]);

        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }


    public function getAssignedTasks($project_id)
    {

        $pdo = getConnection();

        $sql = "SELECT DISTINCT t.* FROM task t INNER JOIN allocation a ON t.task_id = a.task_id WHERE t.associatedProject = :project_id;";

        $results = executeQuery($pdo, $sql, [":project_id" => $project_id]);

        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }



    public function assignTask($params)
    {

        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "INSERT INTO `allocation`(`member_id`, `task_id`, `role`, `startDate`, `contribution`)
            VALUES (:member_id, :task_id, :role, :startDate, :contribution)";

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


    public function getCurrentTaskContribution($task_id)
    {
        $pdo = getConnection();

        $sql = "SELECT SUM(contribution) AS total FROM allocation WHERE task_id = :task_id;";

        $results = executeQuery($pdo, $sql, [":task_id" => $task_id]);

        // get the result column
        $tot = $results->fetchColumn();

        //  return 0 when null (no contribution so far)
        $tot = $tot === null ? 0 : $tot;

        closeConnection($pdo);

        return $tot;
    }


    public function getUnacceptedMemberTasks($member_id)
    {

        $pdo = getConnection();

        $sql = "SELECT t.* FROM task t  JOIN allocation a ON t.task_id = a.task_id  WHERE a.member_id = :member_id AND a.accept IS NULL;";

        $results = executeQuery($pdo, $sql, [":member_id" => $member_id]);

        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }

    public function getAcceptedMemberTasks($member_id)
    {

        $pdo = getConnection();

        $sql = "SELECT t.* FROM task t  JOIN allocation a ON t.task_id = a.task_id  WHERE a.member_id = :member_id AND a.accept IS NOT NULL;";

        $results = executeQuery($pdo, $sql, [":member_id" => $member_id]);

        $tasks = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }



    function getRoleByMemberAndTask($member_id, $task_id)
    {
        $pdo = getConnection();

        $sql = "SELECT role FROM allocation WHERE member_id = :member_id AND task_id = :task_id;";

        $results = executeQuery($pdo, $sql, [":member_id" => $member_id, ":task_id" => $task_id]);

        $data = $results->fetchColumn();

        closeConnection($pdo);

        return $data; // the role or false if no match
    }

    public function updateStatus($params)
    {

        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "UPDATE `task` SET `status`=:status WHERE `task_id`= :task_id";

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

    public function deleteAllocation($params)
    {
        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "DELETE FROM `allocation` WHERE `task_id` = :task_id AND `member_id` = :member_id";

            executeQuery($pdo, $sql, $params);

            // commit the transaction
            $pdo->commit();

            closeConnection($pdo);

            return true; // success
        } catch (Exception $e) {
            // Roll back the transaction in case of an error
            $pdo->rollBack();
            closeConnection($pdo);

            return false; // failure
        }
    }

    public function acceptTask($params)
    {

        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "UPDATE `allocation` SET `accept`='yes' WHERE `task_id` = :task_id AND `member_id` = :member_id";

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

    public function getMemberFilteredTasks($task_id, $taskName, $projectTitle, $status, $member_id)
    {

        // base SQL query
        $sql = "SELECT t.* FROM allocation a
                JOIN task t ON a.task_id = t.task_id
                JOIN project p ON t.associatedProject = p.project_id
                WHERE a.member_id = :member_id AND a.accept IS NOT NULL";

        // arrays to hold the script associated variables
        $params = [":member_id" => $member_id];

        // cumulating filter conditions
        if (!is_null($task_id)) {
            $sql .= " AND t.task_id LIKE :task_id";
            $params[":task_id"] = "%" . $task_id . "%";
        }
        if (!is_null($taskName)) {
            $sql .= " AND t.taskName LIKE :taskName";
            $params[":taskName"] = "%" . $taskName . "%";
        }
        if (!is_null($status)) {
            $sql .= " AND t.status = :status";
            $params[":status"] = $status;
        }
        if (!is_null($projectTitle)) {
            $sql .= " AND p.projectTitle LIKE :projectTitle";
            $params[":projectTitle"] = "%" . $projectTitle . "%";
        }

        $pdo = getConnection();

        // execute query
        $results = executeQuery($pdo, $sql, $params);

        // fill tasks to a list
        $tasks = [];
        while ($data = $results->fetchObject("Task")) {
            $tasks[] = $data;
        }

        closeConnection($pdo);

        return $tasks;
    }


    public function updateProgress($params){

        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "UPDATE `task` SET `progress`=:progress , `status`=:status  WHERE `task_id` = :task_id;";

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

    // 0 -> manager, 1-> leader, 2->member
    public function getFilteredTasks($mode, $user_id, $priority = null, $status = null, $startDate = null, $endDate = null, $projectTitle = null)
{
    $sql= "";// to hold sql
    $params = [":user_id" => $user_id];// holds vars when needed

    switch($mode){
        case 0:// manager filtering
            $sql = "SELECT t.* FROM task t 
            JOIN project p ON t.associatedProject = p.project_id
            WHERE TRUE";
            $params = [];// no user id needed
            break;

        case 1:// leader filtering
            $sql = "SELECT t.* FROM task t
            JOIN project p ON t.associatedProject = p.project_id
            WHERE p.teamLeader = :user_id";
            break;

        case 2://member filtering
            $sql = "SELECT t.* FROM allocation a
            JOIN task t ON a.task_id = t.task_id
            JOIN project p ON t.associatedProject = p.project_id
            WHERE a.member_id = :user_id AND a.accept IS NOT NULL";
            break;
    }
    

    // dynamically add conditions based on filters
    if (!is_null($priority)) {
        $sql .= " AND t.priority = :priority";
        $params[":priority"] = $priority;
    }
    if (!is_null($status)) {
        $sql .= " AND t.status = :status";
        $params[":status"] = $status;
    }
    if (!is_null($startDate) && !is_null($endDate)) {
        //  both startDate and endDate of the task fall within the range
        $sql .= " AND t.startDate >= :startDate AND t.endDate <= :endDate";
        $params[":startDate"] = $startDate;
        $params[":endDate"] = $endDate;

    } elseif (!is_null($startDate)) {
        // task starts on or after the specified startDate
        $sql .= " AND t.startDate >= :startDate";
        $params[":startDate"] = $startDate;

    } elseif (!is_null($endDate)) {
        //  task ends on or before the specified endDate
        $sql .= " AND t.endDate <= :endDate";
        $params[":endDate"] = $endDate;
    }

    if (!is_null($projectTitle)) {
        $sql .= " AND p.projectTitle LIKE :projectTitle";
        $params[":projectTitle"] = "%" . $projectTitle . "%";
    }

    $pdo = getConnection();

    // Execute query
    $results = executeQuery($pdo, $sql, $params);

    // fill in the tasks array
    $tasks = [];
    while ($data = $results->fetchObject("Task")) {
        $tasks[] = $data;
    }

    closeConnection($pdo);

    return $tasks;
}

public function getAllocation($task_id){


    $pdo = getConnection();

        $sql = "SELECT a.*, u.name, t.endDate
        FROM allocation a 
        JOIN user u ON a.member_id = u.user_id 
        JOIN task t ON a.task_id = t.task_id 
        WHERE a.task_id = :task_id;";

        $results = executeQuery($pdo, $sql, [":task_id"=>$task_id]);

        $allocations = [];

        // add task objects to a list
        while ($data = $results->fetchObject("Allocation")) {
            $allocations[] = $data;
        }

        closeConnection($pdo);

        return $allocations;


}

}

?>