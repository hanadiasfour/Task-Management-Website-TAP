-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2025 at 11:29 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web1210209_tap`
--

-- --------------------------------------------------------

--
-- Table structure for table `allocation`
--

CREATE TABLE `allocation` (
  `member_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `task_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `startDate` date NOT NULL,
  `contribution` double NOT NULL,
  `accept` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allocation`
--

INSERT INTO `allocation` (`member_id`, `task_id`, `role`, `startDate`, `contribution`, `accept`) VALUES
('2568368773', 'tVOtJ5CguF', 'Tester', '2025-03-05', 30, NULL),
('4568368733', '123poj75oo', 'Tester', '2025-03-05', 12, NULL),
('5523044873', 'anBfDZT14G', 'Designer', '2025-01-19', 23, 'yes'),
('5523044873', 'dc7LuVnRPF', 'Tester', '2025-01-09', 33, 'yes'),
('5523044873', 'f6jKqPjRih', 'Analyst', '2025-02-13', 50, NULL),
('5523044873', 'PbJRXNcvqb', 'Developer', '2025-01-09', 49, NULL),
('6568368769', '123poj75oo', 'Support', '2025-03-06', 13, NULL),
('6568368769', 'tl9tpoCguF', 'Designer', '2025-03-12', 12, 'yes'),
('6568368769', 'tVO23oCguF', 'Tester', '2025-03-11', 7, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `projectTitle` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `projectDescription` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `budget` int NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `clientName` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `files` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `teamLeader` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `projectTitle`, `projectDescription`, `budget`, `startDate`, `endDate`, `clientName`, `files`, `teamLeader`) VALUES
('ABCD-34567', 'AeroMax', 'Advanced aerodynamics research', 60000, '2025-07-10', '2025-07-30', 'AeroTech', 'sample.png', '0568368767'),
('ADVE-13456', 'EnviroPro', 'Environmental impact assessment', 10000, '2025-06-05', '2025-06-15', 'GreenWorld', 'ass2LV.pdf', NULL),
('AGEJ-67890', 'Aquatech', 'Water filtration project analysis', 15000, '2025-02-01', '2025-02-15', 'AquaCorp', 'parts.docx, timeTable.png', '9568372985'),
('AUIP-12345', 'Cosmos', 'Exploring the universe\'s mysteries', 50000, '2025-01-05', '2025-01-20', 'NASA', 'building.png', NULL),
('BETH-88766', 'CureAI', 'AI-based health solution', 75000, '2025-04-10', '2025-04-25', 'HealthMax', 'charts.jpg', '0298361967'),
('CRAZ-88888', 'Neurolink', 'Brain-machine interface testing', 85000, '2025-08-01', '2025-08-15', 'BrainCore', 'Project1.pdf', NULL),
('PLOD-66543', 'Deep Blue', 'Marine ecosystem study', 40000, '2025-05-01', '2025-05-20', 'MarineLabs', NULL, '6560099864'),
('REED-55344', 'Gravity', 'Let\'s see if the apple actually falls due to what they claim to be \"gravity\".', 20100, '2025-01-01', '2025-01-11', 'Newton', 'ugly.png,Response Time Graph.png', NULL),
('SSSS-55555', 'SkyClear', 'Sends a notification when the sky in the users area is 100% clear of clouds.', 1000, '2025-01-18', '2025-01-30', 'Windman', 'MYPlan.pdf', '8045519189'),
('TTTT-55555', 'TestProject', 'This project does not exist in the real world, it\'s just part of our imagination. This project was created with the purpose of testing this TAP system. Thank you.', 20000, '2025-01-08', '2025-06-18', 'Bogdan Exists', 'MyHouse.png,workDistribution.docx,Proposal.pdf', NULL),
('TTTT-66666', 'TestProject2', 'This is a second test but will actually be used in the assign leader section.', 20003, '2025-01-08', '2025-06-18', 'Bogdan2 Exists', 'MyCat.png,distribution.docx,cakeShop.pdf', '8045519189'),
('TTTT-77777', 'TestProject3', 'The Project Leader selects a project from a list. Upon selection, all tasks for the project are displayed in a table.', 20003, '2025-02-03', '2025-03-16', 'Bogdan3 Exists', 'MyTree.png', '8045519189'),
('TTTT-88888', 'TestProject4', 'The Accept Task Assignments functionality allows team members to view and confirm newly assigned tasks. It highlights new assignments, provides task details in a read-only form, and allows team member', 20003, '2025-02-03', '2025-02-21', 'Bogdan4 Exists', 'myChart.jpeg', '8045519189'),
('TTTT-99999', 'TestProject5', 'The Deadline for submitting this task is Monday, 13/01/2025. At 22:00 at the CShost, the server will be closed on Monday, 13/01/2025, at 22:00. In addition to your submission to the CShost, you must s', 20003, '2025-01-19', '2025-05-22', 'Bogdan5 Exists', 'Interface.png', '8045519189'),
('UFNK-22334', 'Helios', 'Solar energy panel optimization', 45000, '2025-03-01', '2025-03-30', 'SunPower', 'fish.png', '0568368767'),
('WORD-54321', 'AgroSense', 'Smart farming solutions', 30000, '2025-09-05', '2025-09-20', 'AgriTech', 'ass2LV.pdf, charts.jpg', '0568361967'),
('ZXQY-99999', 'Quantum Leap', 'Quantum computing simulation', 95000, '2025-10-01', '2025-10-31', 'QuantumSys', NULL, '4568399221');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `taskName` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `taskDescription` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `effort` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `priority` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `associatedProject` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `progress` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `taskName`, `taskDescription`, `startDate`, `endDate`, `effort`, `status`, `priority`, `associatedProject`, `progress`) VALUES
('123poj75oo', 'hot potato', 'a game', '2025-03-04', '2025-03-14', 2, 'In Progress', 'High', 'UFNK-22334', 20),
('1xk8rAaoQ9', 'TestTask2', 'Test authentication flows using Firebase for account creation and login.', '2025-01-09', '2025-01-12', 3, 'Pending', 'Medium', 'TTTT-66666', NULL),
('2pcJflenHb', 'Connect System', 'Connect the front end and back end together to get a comprehensive application to deploy.', '2025-01-22', '2025-01-25', 34, 'Pending', 'High', 'SSSS-55555', NULL),
('5Jr9CGLzxB', 'Test App', 'Test various tests including usability tests, performance tests, and functional tests.', '2025-01-24', '2025-01-25', 22, 'Pending', 'Low', 'SSSS-55555', NULL),
('6r2JCoLPw0', 'chatbot', 'Implement a chatbot feature with Azure AI for medical advice in Arabic.', '2025-03-10', '2025-03-12', 9, 'Completed', 'Medium', 'TTTT-77777', 100),
('anBfDZT14G', 'DesignUI', 'We need a very uer-friendly UI with descriptive icons and usable features.', '2025-01-19', '2025-01-21', 21, 'In Progress', 'Medium', 'SSSS-55555', 41),
('dc7LuVnRPF', 'sysReq', 'Create system requirements for an elderly care mobile app.', '2025-03-12', '2025-03-16', 3, 'In Progress', 'High', 'TTTT-77777', 10),
('eLLEED97hF', 'TestTask3', 'Conduct a performance test in JMeter for 500 concurrent users.', '2025-01-09', '2025-01-12', 3, 'Pending', 'High', 'TTTT-66666', NULL),
('f6jKqPjRih', 'Build Webpage', 'Build a dynamic webpage using PDO for secure database interactions.', '2025-02-12', '2025-02-13', 9, 'Pending', 'High', 'TTTT-88888', NULL),
('gK4VERPYkl', 'Proposal Report', 'Write a detailed proposal report for a software development project.', '2025-03-17', '2025-03-19', 6, 'Pending', 'Medium', 'TTTT-99999', NULL),
('KGATvAC4Zh', 'TestTask4', 'Debug SQL queries to improve data retrieval efficiency.', '2025-01-09', '2025-01-10', 4, 'Pending', 'Medium', 'TTTT-66666', NULL),
('PbJRXNcvqb', 'TestTask1', 'Design wireframes for a mobile app layout focused on user accessibility.', '2025-01-08', '2025-01-15', 2, 'Pending', 'Low', 'TTTT-66666', NULL),
('tl9tpoCguF', 'functions 3', 'there are problems in functions 1 2 and 3.', '2025-03-09', '2025-03-22', 9, 'In Progress', 'High', 'UFNK-22334', 30),
('tVO23oCguF', 'Trigger button', 'Fix the trigger button on page 3.', '2025-03-09', '2025-03-24', 2, 'In Progress', 'Low', 'UFNK-22334', 11),
('tVOtJ5CguF', 'fff', 'jhgfghui', '2025-01-01', '2025-03-19', 1, 'Pending', 'Low', 'UFNK-22334', NULL),
('tVOtpoCguF', 'click style', 'create a css for styling clicks', '2025-03-08', '2025-03-17', 7, 'Pending', 'Medium', 'UFNK-22334', NULL),
('ZULD7J3eyJ', 'BackendDev', 'Develop the backend of SkyClear including a real time database and connection of weather APIs. ', '2025-01-19', '2025-01-21', 32, 'Pending', 'High', 'SSSS-55555', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `flat` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `street` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `skills` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `qualification` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(13) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `id`, `name`, `dob`, `email`, `phone`, `flat`, `street`, `city`, `country`, `skills`, `qualification`, `role`, `username`, `password`) VALUES
('0000000001', '41499220', 'Hanadi Asfour', '2003-07-02', 'hanadiAS4@gmail.com', '0595987654', '2A', 'main', 'Sinjil', 'Palestine', 'Front-end Development', 'Bachelor\'s Degree', 'Manager', 'KittyBoss2003', '12345'),
('0298361967', '31399873', 'Jamal Brown', '2015-01-08', 'jamal@gmail.com', '7685945637', '77Y', 'Strawberry', 'Shortcake', 'Fiction', 'Front-end Development', 'High School', 'Project Leader', 'JamalBeauty', '12345'),
('0568361967', '31377873', 'Hani Pink', '2010-01-08', 'hani@gmail.com', '7685089637', '55P', 'Grablie', 'minister', 'Holand', 'Front-end Development', 'PhD', 'Project Leader', 'HaniPink', '12345'),
('0568368767', '21277873', 'Yazan Rulawi', '2000-02-03', 'yazan@gmail.com', '7685089007', '32U', 'Main', 'Vanity', 'Canada', 'Front-end Development,DevOps,Game Development', 'PhD', 'Project Leader', 'YazanRLI', '12345'),
('2032578007', '99887766', 'Test Manager', '2003-07-02', 'manger@test.com', '0900800700', '15T', 'Al-Testing', 'Testcity', 'Testland', 'DevOps,Cybersecurity,Cloud Computing', 'PhD', 'Manager', 'TestManager', 'Tcomp334'),
('2568368773', '21277834', 'Omar Khaled', '1999-09-05', 'omar.khaled@gmail.com', '7685089013', '23T', 'Ocean', 'Cairo', 'Egypt', 'Game Development,Machine Learning', 'High School', 'Team Member', 'OmarKhal', '12345'),
('3296508121', '12340987', 'Gabby Bee', '2024-12-04', 'gabby@gmail.com', '0900600700', 'R55', 'Round', 'Jupyter', 'Milky way ', 'Machine Learning,Game Development', 'High School', 'Manager', 'GabbyGirl', 'passw0rd'),
('3371500711', '99988876', 'Emily Forbes', '2024-12-09', 'emily@gmail.com', '0800200550', '990S', 'Frake', 'Georgia ', 'USA', 'Full-Stack Development,Database Management', 'Bachelor\'s Degree', 'Team Member', 'EmilyTheCool', 'try2hack'),
('4568368733', '21977873', 'Laila Khan', '2001-01-15', 'laila.khan@gmail.com', '7685089010', '11C', 'Palm', 'Ramallah', 'Palestine', 'Cloud Computing,Cybersecurity', 'Master\'s Degree', 'Team Member', 'LailaKha', '12345'),
('4568399221', '91277873', 'Ali Hamdan', '1993-03-22', 'ali.hamdan@gmail.com', '7685089011', '7G', 'Market', 'Dubai', 'UAE', 'DevOps,Cloud Computing', 'PhD', 'Project Leader', 'AliHDev', '12345'),
('5523044873', '66447788', 'Test Member', '2001-06-04', 'member@test.com', '0900800700', '15T', 'Al-Testing', 'Testcity', 'Testland', 'Front-end Development,Game Development', 'Bachelor\'s Degree', 'Team Member', 'TestMember', 'Tcomp334'),
('6336688772', '29977874', 'Emma Wilson', '1995-11-30', 'emma.wilson@gmail.com', '7685089012', '89X', 'Elm', 'London', 'UK', 'Front-end Development', 'PhD', 'Project Leader', 'EmmaProj', '12345'),
('6560099864', '21726874', 'Sophia Lee', '1996-04-25', 'sophia.lee@gmail.com', '7685089014', '56F', 'Park', 'Seoul', 'South Korea', 'Mobile App Development,Back-end Development', 'Master\'s Degree', 'Project Leader', 'SophiaLee', '12345'),
('6568368769', '21177873', 'Hussien Doe', '1985-05-18', 'hussien.doe@gmail.com', '7685089009', '42B', 'Oak', 'Chicago', 'USA', 'Full-Stack Development,Database Management,Machine Learning', 'Bachelor\'s Degree', 'Team Member', 'hussienD', '12345'),
('7598765776', '21234259', 'Amira Zidan', '2002-12-19', 'amira.zidan@gmail.com', '7685089016', '77Y', 'Horizon', 'Beirut', 'Lebanon', 'Front-end Development,Game Development', 'Bachelor\'s Degree', 'Team Member', 'AmiraZid', '12345'),
('7765049812', '87542193', 'Goarge Young', '2007-05-14', 'goarge@gmail.com', '0285857398', 'P44', 'Jupyter', 'Nablus', 'Palestine', 'DevOps,Cybersecurity,Cloud Computing', 'Master\'s Degree', 'Manager', 'gaogeMan', 'run4Ever'),
('8045519189', '88776655', 'Test Leader', '2025-01-01', 'leader@test.com', '0900800700', '15T', 'Al-Testing', 'Testcity', 'Testland', 'Back-end Development,Full-Stack Development', 'Master\'s Degree', 'Project Leader', 'TestLeader', 'Tcomp334'),
('8561234577', '21222222', 'Liam Brown', '1994-06-08', 'liam.brown@gmail.com', '7685089017', '91M', 'Meadow', 'Sydney', 'Australia', 'Full-Stack Development', 'Master\'s Degree', 'Team Member', 'LiamAUS', '12345'),
('9566982707', '21266873', 'Sarah Novak', '1998-07-12', 'sarah.novak@gmail.com', '7685089008', 'A1', 'River', 'Toronto', 'Canada', 'Full-Stack Development,Back-end Development', 'Master\'s Degree', 'Project Leader', 'SarahNova', '12345'),
('9568372985', '21279875', 'James Smith', '1988-08-14', 'james.smith@gmail.com', '7685089015', '33L', 'Valley', 'New York', 'USA', 'Cloud Architect,DevOps', 'PhD', 'Project Leader', 'JamesNYC', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allocation`
--
ALTER TABLE `allocation`
  ADD PRIMARY KEY (`member_id`,`task_id`) USING BTREE,
  ADD KEY `task` (`task_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `teamLeader` (`teamLeader`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `associated_P` (`associatedProject`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allocation`
--
ALTER TABLE `allocation`
  ADD CONSTRAINT `member` FOREIGN KEY (`member_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `task` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `teamLeader` FOREIGN KEY (`teamLeader`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `associated_P` FOREIGN KEY (`associatedProject`) REFERENCES `project` (`project_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
