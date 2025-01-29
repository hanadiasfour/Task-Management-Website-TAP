<?php
class Task
{
    private $task_id;
    private $taskName;
    private $taskDescription;
    private $associatedProject; // foreign key reference to the Project
    private $startDate;
    private $endDate;
    private $effort;
    private $status; // Pending, In Progress, Completed
    private $priority; // Low, Medium, High
    private $progress;

    public function __construct()
    {
        // no-argument constructor for fetching objects
    }

    // Getters
    public function getTaskId()
    {
        return $this->task_id;
    }

    public function getName()
    {
        return $this->taskName;
    }

    public function getDescription()
    {
        return $this->taskDescription;
    }

    public function getAssociatedProject ()
    {
        return $this->associatedProject ;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getEffort()
    {
        return $this->effort;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    // Setters
    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;
    }

    public function setName($name)
    {
        $this->taskName = $name;
    }

    public function setDescription($description)
    {
        $this->taskDescription = $description;
    }

    public function setAssociatedProject ($associatedProject )
    {
        $this->associatedProject  = $associatedProject ;
    }

    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    public function setEffort($effort)
    {
        $this->effort = $effort;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    // Display task details in table row format
    public function displayTaskRow()
    {
        $row = "<tr>";
        $row .= "<td><a class='table_link' href='assignTask.php?id=" . $this->task_id . "'>" . $this->task_id . "</a></td>";
        $row .= "<td>" . $this->taskName . "</td>";
        $row .= "<td>" . $this->startDate . "</td>";
        $row .= "<td>" . $this->status . "</td>";
        $row .= "<td>" . $this->priority . "</td>";
        $row .= "<td><a href='assignTask.php?id=" . $this->task_id . "'><img src='../images/assignMember.png' alt='Assign Task' class='clickable_img' width='50'></a></td>";
        $row .= "</tr>";

        return $row;
    }


    public function displayTaskRowMember($projectName, $mode = 1)
    {
        $row = "<tr>";
        $row .= "<td><a class='table_link' href='".(($mode==2)?"editProgress.php":"confirmTask.php")."?id=" . $this->task_id . "'>" . $this->task_id . "</a></td>";
        $row .= "<td>" . $this->taskName . "</td>";
        $row .= "<td>" . $projectName . "</td>";
        

        if($mode==1){
            $row .= "<td>" . $this->startDate . "</td>";
            $row .= "<td><a href='confirmTask.php?id=" . $this->task_id . "'><img src='../images/confirmTask.png' alt='Confirm Task' class='clickable_img' width='50'></a></td>";
        }else{
            $row .= "<td>" . $this->status . "</td>";
            $row .= "<td><a href='editProgress.php?id=" . $this->task_id . "'><img src='../images/editProgress.png' alt='Edit Task Progress' class='clickable_img' width='50'></a></td>";


    }
        $row .= "</tr>";

        return $row;
    }


    // Display task details for a given project in list format
    public function displayTaskDetails($projectName)
    {


        // deciding what classes to add to the rows
        $p_class = '';
        switch ($this->priority) {
            case "Low":
                $p_class = "low-priority";
                break;
            case "Medium":
                $p_class = "medium-priority";
                break;
            case "High":
                $p_class = "high-priority";
                break;
        }

        $s_class = '';
        switch ($this->status) {
            case "Pending":
                $s_class = "pending";
                break;
            case "In Progress":
                $s_class = "in-progress";
                break;
            case "Completed":
                $s_class = "completed";
                break;
        }

        $details = "<tr>";
        $details .= "<td><strong><a class='table_link' href='viewTask.php?id=" . $this->task_id . "'>" . $this->task_id . "</a></strong></td>";
        $details .= "<td>" . $this->taskName . "</td>";        
        $details .= "<td>" . $projectName . "</td>";
        $details .= "<td class='".$s_class."'>" . $this->status . "</td>";
        $details .= "<td class='".$p_class."'>" . $this->priority . "</td>";
        $details .= "<td>" . $this->startDate . "</td>";
        $details .= "<td>" . $this->endDate . "</td>";
        $details .= "<td>" . ($this->progress ?? 0) . " %</td>";
        $details .= "</tr>";
        
        return $details;
    }
}
?>