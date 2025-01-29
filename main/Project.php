<?php
class Project
{
    private $project_id;
    private $projectTitle;
    private $projectDescription;
    private $startDate;
    private $endDate;
    private $budget;
    private $clientName;
    private $files; // array to store uploaded file paths
    private $teamLeader;

    public function __construct()
    {
        // no-argument constructor for fetching objects
    }

    // getters
    public function getProject_id()
    {
        return $this->project_id;
    }

    public function getTitle()
    {
        return $this->projectTitle;
    }

    public function getDescription()
    {
        return $this->projectDescription;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getBudget()
    {
        return $this->budget;
    }

    public function getClient()
    {
        return $this->clientName;
    }

    public function getFiles()
    {
        return $this->files;
    }
    public function getTeamLeader()
    {
        return $this->teamLeader;
    }

    // setters
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
    }

    public function setTitle($title)
    {
        $this->projectTitle = $title;
    }

    public function setDescription($description)
    {
        $this->projectDescription = $description;
    }

    public function setStartDate($start_date)
    {
        $this->startDate = $start_date;
    }

    public function setEndDate($end_date)
    {
        $this->endDate = $end_date;
    }

    public function setBudget($budget)
    {
        $this->budget = $budget;
    }

    public function setClient($client)
    {
        $this->clientName = $client;
    }

    public function setFiles($files)
    {
        $this->files = $files;
    }

    public function setTeamLeader($teamLeader)
    {
        $this->teamLeader = $teamLeader;
    }


    // to display project details in table format
    public function displayProjectRow()
    {

        $row = "<tr>";
        $row .= "<td><a class='table_link' href='assignProject.php?id=" . $this->project_id . "'>" . $this->project_id . "</a></td>";
        $row .= "<td>" . $this->projectTitle . "</td>";
        $row .= "<td>" . $this->startDate . "</td>";
        $row .= "<td>" . $this->endDate . "</td>";
        $row .= "<td><a href='assignProject.php?id=".$this->project_id."'><img src='../images/assignLeader.png' alt='Assign Leader' class='clickable_img' width='50'></a></td>";
        $row .= "</tr>";

        return $row;
    }

    // to display file information
    public function displayFileList()
    {
        if (empty($this->files)) {
            return "No files attached.";
        }

        $fileArray = explode(',', $this->files);
        $fileList = "<ul>";

        //creating a list item for each file
        foreach ($fileArray as $file) {

            $file = trim($file);
            $fileList .= "<li><a href='../uploads/" . $file . "' target='_blank'>" . $file . "</a></li>";
        }

        $fileList .= "</ul>";

        return $fileList;
    }
}
?>