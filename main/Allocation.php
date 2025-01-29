<?php
class Allocation
{
    private $member_id;
    private $task_id;
    private $name;
    private $role;
    private $startDate;
    private $endDate;
    private $contribution;
    private $accept;


    public function __construct()
    {
        // fetchObject needs a no-argument constructor
    }

    //getters then setters respectively below

    public function getTask_id()
    {
        return $this->task_id;
    }

    public function getMember_id()
    {
        return $this->member_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRole()
    {
        return $this->role;
    }
    
    public function getAccept()
    {
        return $this->accept;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
    public function getContribution()
    {
        return $this->contribution;
    }


    public function setTask_id($id)
    {
        return $this->task_id = $id;
    }
    public function setMember_id($id)
    {
        return $this->member_id = $id;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function setRole($role)
    {
        return $this->role = $role;
    }
    
    public function setAccept($accept)
    {
        return $this->accept =$accept;
    }

    public function setStartDate($date)
    {
        return $this->startDate = $date;
    }

    public function setEndDate($date)
    {
        return $this->endDate = $date;
    }

    public function setContribution($contribution)
    {
        return $this->contribution = $contribution;
    }
    

    public function displayRow()
    {
        $row = "<tr>";
        $row .= "<td><img class='member-photo' src='../images/profile.jpg' alt='Picture' width='50'></td>";
        $row .= "<td>" . $this->member_id . "</td>";
        $row .= "<td>" . $this->name . "</td>";
        $row .= "<td>" . $this->startDate . "</td>";
        $row .= "<td" . ((is_null($this->accept))?('>'. $this->endDate):" class='in-progress'>In Progress") . "</td>";
        $row .= "<td>" . $this->contribution . "%</td>";
        $row .= "</tr>";
        return $row;
    }
}
?>