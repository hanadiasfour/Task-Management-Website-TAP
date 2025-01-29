<?php
require_once "User.php";
require_once "db.php";

class UserGateway
{

    private static String $baseSQL = "SELECT * FROM user";


    function __construct() {}


    
    function getProjectLeaders()
    {
        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE role = 'Project Leader';";

        $results = executeQuery($pdo, $sql, []);

        $leaders = [];

        // add users to leaders list
        while ($data = $results->fetchObject("User")) {
            $leaders[] = $data;
        }

        closeConnection($pdo);

        return $leaders;
    }


    function getTeamMembers()
    {
        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE role = 'Team Member';";

        $results = executeQuery($pdo, $sql, []);

        $members = [];

        // add users to leaders list
        while ($data = $results->fetchObject("User")) {
            $members[] = $data;
        }

        closeConnection($pdo);

        return $members;
    }


    function getUserByUsername($username)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE username = :username;";

        $results = executeQuery($pdo, $sql, [":username" => $username]);

        $data = $results->fetchObject("User");

        closeConnection($pdo);

        return $data;
    }

    function getUserByIUser_id($user_id)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE user_id = :user_id;";

        $results = executeQuery($pdo, $sql, [":user_id" => $user_id]);

        $data = $results->fetchObject("User");

        closeConnection($pdo);

        return $data;
    }


    function getUserById($id)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE id = :id;";

        $results = executeQuery($pdo, $sql, [":id" => $id]);

        $data = $results->fetchObject("User");

        closeConnection($pdo);

        return $data;
    }

    
    function getUserByEmail($email)
    {

        $pdo = getConnection();

        $sql = self::$baseSQL . " WHERE email = :email;";

        $results = executeQuery($pdo, $sql, [":email" => $email]);

        $data = $results->fetchObject("User");

        closeConnection($pdo);

        return $data;
    }

    public function InsertTicket($params)
    {
        $pdo = getConnection();

        try {
            // start a transaction
            $pdo->beginTransaction();

            $sql = "INSERT INTO `ticket` (`ticket_id`, `customer_id`, `assigned_staff`, `submitted_date`, `status`, `description`, `ticket_img`, `assigned_date`, `emergency_level`, `location`, `contact_email`) 
            VALUES (:ticket_id, :customer_id, NULL, NOW(), :status, :description, " . (isset($params[":image"]) ? ":image" : "NULL") . ", NULL, :emergency_level, :location, :contact_email)";

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

    function insertUser($params)
    {
        $success = false; // to track if the insertion was successful

        $pdo = getConnection();


        do {
            try {
                // open new transaction
                $pdo->beginTransaction();

                // generate a random 10-digit number
                $uniqueId = sprintf('%010d', mt_rand(0, 9999999999));

                $new_params = array_merge([":user_id" => $uniqueId], $params);

                $sql = "INSERT INTO `user` (`user_id`, `id`, `name`, `dob`, `email`, `phone`, `flat`, `street`, `city`, `country`, `skills`, `qualification`, `role`, `username`, `password`) 
                VALUES (:user_id, :id, :name, :dob, :email, :phone, :flat, :street, :city, :country, :skills, :qualification, :role, :username, :password)";
    
                executeQuery($pdo, $sql, $new_params);//execute the query

                $pdo->commit();//commit transaction

                closeConnection($pdo);//close

                $success = true;// Success

                return $uniqueId;//return what id was used for user


                

            } catch (Exception $e) {
                // Rollback transaction on failure
                $pdo->rollBack();
            }
        } while (!$success);


        closeConnection($pdo);//close

    }

}
?>