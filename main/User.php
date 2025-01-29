<?php
class User
{
    private $user_id;
    private $id;
    private $name;
    private $dob;
    private $email;
    private $phone;
    private $flat;
    private $street;
    private $city;
    private $country;
    private $skills;
    private $qualification;
    private $role;
    private $username;
    private $password;


    public function __construct()
    {
        // fetchObject needs a no-argument constructor
    }

    //getters then setters respectively below

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDob()
    {
        return $this->dob;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getFlat()
    {
        return $this->flat;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getSkills()
    {
        return $this->skills;
    }

    public function getQualification()
    {
        return $this->qualification;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setFlat($flat)
    {
        $this->flat = $flat;
    }

    public function setStreet($street)
    {
        $this->street = $street;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

    public function setQualification($qualification)
    {
        $this->qualification = $qualification;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}
?>