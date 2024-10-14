-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 07:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `C_ID` int(11) NOT NULL,
  `C_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`C_ID`, `C_Name`) VALUES
(20, 'Administration'),
(16, 'Consulting'),
(11, 'Content Writing'),
(4, 'Customer Service'),
(5, 'Data Analysis'),
(15, 'Education'),
(10, 'Engineering'),
(7, 'Finance'),
(8, 'Graphic Design'),
(13, 'Healthcare'),
(6, 'Human Resources'),
(12, 'IT Support'),
(14, 'Legal'),
(2, 'Marketing'),
(19, 'Operations'),
(18, 'Product Management'),
(9, 'Project Management'),
(17, 'Research'),
(3, 'Sales'),
(1, 'Software Development');

-- --------------------------------------------------------

--
-- Table structure for table `employer`
--

CREATE TABLE `employer` (
  `E_ID` int(11) NOT NULL,
  `E_Name` varchar(100) NOT NULL,
  `E_Email` varchar(256) NOT NULL,
  `E_PhoneNo` varchar(11) DEFAULT NULL,
  `About_Company` text DEFAULT NULL,
  `Location` varchar(256) NOT NULL,
  `E_profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer`
--

INSERT INTO `employer` (`E_ID`, `E_Name`, `E_Email`, `E_PhoneNo`, `About_Company`, `Location`, `E_profile_image`) VALUES
(2, 'Alice Chu', 'alice.chu@example.com', '01151539601', 'Our company prides itself on a dynamic and collaborative environment that encourages professional growth and development. With a customer-first approach, we aim to offer exceptional services that simplify business operations and foster lasting relationships. Whether it\'s resolving technical issues, streamlining administrative tasks, or nurturing talent within an organization, our team is dedicated to delivering excellence in everything we do.\r\n\r\nJoin Alice Chu and be part of a supportive and forward-thinking company that values integrity, teamwork, and innovation.', 'Sabah, Kota Kinabalu', 'uploads/profile_images/WhatsApp Image 2021-02-12 at 12.04.57 AM.jpeg'),
(3, 'Tech Innovations Sdn Bhd', 'contact@techinnovations.my', '012-3456789', 'Leading company in technology solutions.', 'Kuala Lumpur', NULL),
(4, 'Green Energy Solutions Sdn Bhd', 'info@greenenergy.my', '013-9876543', 'Experts in renewable energy and sustainability.', 'Selangor', NULL),
(5, 'Creative Minds Agency', 'hello@creativeminds.my', '014-1234567', 'Full-service marketing and advertising agency.', 'Penang', NULL),
(6, 'HealthCare Plus', 'support@healthcareplus.my', '015-2345678', 'Providing top-notch healthcare services.', 'Johor Bahru', NULL),
(7, 'Finance Gurus', 'info@financegurus.my', '016-3456789', 'Your partner in financial growth.', 'Malacca', NULL),
(8, 'Education First Sdn Bhd', 'contact@educationfirst.my', '017-4567890', 'Passionate about quality education.', 'Ipoh', NULL),
(9, 'BuildRight Construction', 'info@buildright.my', '018-5678901', 'Constructing dreams with excellence.', 'Kota Kinabalu', NULL),
(10, 'Travel Adventures Sdn Bhd', 'support@traveladventures.my', '019-6789012', 'Creating unforgettable travel experiences.', 'Kuching', NULL),
(11, 'Culinary Delights', 'hello@culinarydelights.my', '012-7890123', 'Your go-to for gourmet catering services.', 'Shah Alam', NULL),
(12, 'Digital Marketing Agency', 'info@digitalmarketing.my', '013-8901234', 'Specializing in digital marketing strategies.', 'Cyberjaya', NULL),
(13, 'Automotive Solutions', 'contact@automotivesolutions.my', '014-9012345', 'Leading automotive service and repairs.', 'Sibu', NULL),
(14, 'Retail Wonders Sdn Bhd', 'support@retailwonders.my', '015-0123456', 'Innovative retail solutions for businesses.', 'Butterworth', NULL),
(15, 'Fintech Innovations', 'hello@fintechinnovations.my', '016-1234567', 'Transforming the finance sector with technology.', 'Seremban', NULL),
(16, 'Real Estate Experts', 'info@realestateexperts.my', '017-2345678', 'Helping you find your dream home.', 'Kota Bharu', NULL),
(17, 'Logistics and Supply Co.', 'contact@logisticsupply.my', '018-3456789', 'Streamlining your supply chain.', 'Petaling Jaya', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_application`
--

CREATE TABLE `job_application` (
  `A_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `L_ID` int(11) NOT NULL,
  `R_ID` int(11) NOT NULL,
  `E_ID` int(11) NOT NULL,
  `Application_Date` date NOT NULL DEFAULT curdate(),
  `Status` enum('accepted','rejected','pending') NOT NULL DEFAULT 'pending',
  `Reviewed_At` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_application`
--

INSERT INTO `job_application` (`A_ID`, `S_ID`, `L_ID`, `R_ID`, `E_ID`, `Application_Date`, `Status`, `Reviewed_At`) VALUES
(1, 1, 1, 1, 2, '2024-09-27', 'rejected', '2024-09-26 16:33:47'),
(5, 3, 1, 2, 2, '2024-09-27', 'accepted', '2024-09-26 16:59:00'),
(6, 3, 1, 2, 2, '2024-09-29', 'accepted', '2024-09-29 05:06:43'),
(7, 3, 2, 2, 2, '2024-09-29', 'accepted', '2024-09-29 05:44:04'),
(8, 3, 1, 2, 2, '2024-09-29', 'accepted', '2024-09-29 07:35:31'),
(9, 3, 2, 2, 2, '2024-09-29', 'accepted', '2024-09-29 14:26:44'),
(10, 3, 1, 2, 2, '2024-09-29', 'accepted', '2024-09-29 14:26:36'),
(11, 3, 2, 2, 2, '2024-09-29', 'accepted', NULL),
(12, 3, 2, 2, 2, '2024-09-29', 'accepted', NULL),
(13, 3, 17, 2, 2, '2024-09-29', 'accepted', '2024-09-29 14:44:09'),
(14, 3, 1, 2, 2, '2024-09-29', 'accepted', NULL),
(15, 3, 1, 2, 2, '2024-09-30', 'accepted', NULL),
(16, 3, 1, 2, 2, '2024-09-30', 'accepted', NULL),
(17, 3, 18, 2, 2, '2024-09-30', 'accepted', '2024-09-30 11:49:54'),
(18, 1, 18, 1, 2, '2024-10-01', 'rejected', '2024-10-01 03:35:27'),
(19, 3, 1, 2, 2, '2024-10-02', 'rejected', '2024-10-02 09:21:49'),
(30, 3, 1, 2, 2, '2024-10-04', 'rejected', '2024-10-04 05:34:11'),
(31, 3, 2, 2, 2, '2024-10-04', 'rejected', '2024-10-04 05:34:14'),
(32, 3, 1, 2, 2, '2024-10-04', 'rejected', '2024-10-04 06:45:32'),
(33, 3, 18, 2, 2, '2024-10-04', 'rejected', '2024-10-04 06:45:35'),
(34, 44, 1, 8, 2, '2024-10-04', 'accepted', '2024-10-04 07:06:59'),
(35, 44, 2, 8, 2, '2024-10-04', 'rejected', '2024-10-04 07:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `job_listing`
--

CREATE TABLE `job_listing` (
  `L_ID` int(11) NOT NULL,
  `E_ID` int(11) NOT NULL,
  `C_ID` int(11) NOT NULL,
  `Job_Name` varchar(100) NOT NULL,
  `Job_Type` text DEFAULT NULL,
  `Salary` text DEFAULT NULL,
  `Job_Responsibilities` text DEFAULT NULL,
  `Requirement` text DEFAULT NULL,
  `Add_On` text DEFAULT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `view_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_listing`
--

INSERT INTO `job_listing` (`L_ID`, `E_ID`, `C_ID`, `Job_Name`, `Job_Type`, `Salary`, `Job_Responsibilities`, `Requirement`, `Add_On`, `status`, `Created_at`, `view_count`) VALUES
(1, 2, 12, 'Customer Service IT', 'part_time', '30 - 40/hour', '- Provide technical support and assistance to customers via phone, email, or chat\r\n- Troubleshoot software, hardware, and network issues\r\n- Guide customers through problem-solving processes for technical difficulties\r\n- Document customer interactions, issues, and resolutions in the support system\r\n- Collaborate with other IT teams to resolve complex technical issues\r\n- Ensure customer satisfaction through timely and effective solutions\r\n- Stay up-to-date with product updates and new technologies to assist customers better', '- Bachelor’s degree in IT, Computer Science, or a related field (or equivalent experience)\r\n- Previous experience in customer service or IT support roles\r\n- Strong troubleshooting and problem-solving skills\r\n- Excellent verbal and written communication skills\r\n- Familiarity with operating systems (Windows, macOS, Linux) and network protocols\r\n- Ability to work in a fast-paced environment and handle multiple support tickets\r\n- Patience and a customer-focused attitude', '- Knowledge of ticketing systems (e.g., Zendesk, Jira) is a plus\r\n- Work Location: Remote or On-site (depending on company policy)\r\n- Benefits: Pro-rated benefits, training, and development opportunities', 'available', '2024-09-26 15:33:58', 37),
(2, 2, 20, 'Administrative Assistant', 'full_time', '3000 - 3500', '- Manage day-to-day office operations (e.g., scheduling, correspondence)\r\n- Answer phone calls and handle email inquiries\r\n- Organize and maintain files, records, and office supplies\r\n- Schedule appointments, meetings, and coordinate calendars\r\n- Prepare and edit documents such as reports, memos, and presentations\r\n- Greet visitors and assist with general office tasks\r\n- Support management and other staff with administrative tasks\r\n- Handle travel arrangements and expense reports', '- High school diploma or equivalent (bachelor’s degree preferred)\r\n- Proven experience as an administrative assistant or similar role\r\n- Proficiency in Microsoft Office Suite (Word, Excel, PowerPoint, Outlook)\r\n- Strong organizational and time management skills\r\n- Excellent verbal and written communication abilities\r\n- Ability to multitask and prioritize workloads\r\n- Strong attention to detail and problem-solving skills', 'Benefits: Health insurance, paid time off, retirement plan options\r\nOpportunities: Professional development and growth within the company', 'available', '2024-09-26 19:49:53', 19),
(3, 3, 16, 'Business Consultant', 'Full Time', '5000-6000', 'Advise companies on business practices.', 'Bachelor\'s in Business Management', 'Experience in consulting preferred.', 'available', '2024-09-26 19:49:53', 2),
(4, 4, 11, 'Content Writer', 'Part Time', '1500-2000', 'Write and edit content for websites.', 'Proven content writing experience.', 'Familiarity with SEO.', 'available', '2024-09-26 19:49:53', 0),
(5, 5, 4, 'Customer Service Representative', 'Full Time', '2500-3000', 'Assist customers with queries and issues.', 'Good communication skills.', 'Experience in customer service is a plus.', 'available', '2024-09-26 19:49:53', 0),
(6, 6, 5, 'Data Analyst', 'Full Time', '4000-4500', 'Analyze datasets to inform business decisions.', 'Degree in Statistics or related field.', 'Proficiency with data analysis tools.', 'available', '2024-09-26 19:49:53', 0),
(7, 7, 15, 'High School Teacher', 'Full Time', '3500-4000', 'Teach students in various subjects.', 'Degree in Education.', 'Classroom teaching experience preferred.', 'available', '2024-09-26 19:49:53', 0),
(8, 8, 10, 'Mechanical Engineer', 'Full Time', '6000-7000', 'Design and develop mechanical systems.', 'Degree in Mechanical Engineering.', 'Experience in mechanical design software.', 'available', '2024-09-26 19:49:53', 0),
(9, 9, 7, 'Financial Analyst', 'Full Time', '5000-6000', 'Provide financial insights for business growth.', 'Degree in Finance.', 'Strong analytical skills.', 'available', '2024-09-26 19:49:53', 0),
(10, 10, 8, 'Graphic Designer', 'Part Time', '2000-2500', 'Design marketing materials and logos.', 'Degree in Graphic Design or related.', 'Proficiency in Adobe Creative Suite.', 'available', '2024-09-26 19:49:53', 0),
(11, 11, 13, 'Nurse', 'Full Time', '4500-5000', 'Provide healthcare services to patients.', 'Degree in Nursing.', 'Registered Nurse license.', 'available', '2024-09-26 19:49:53', 0),
(12, 12, 6, 'HR Manager', 'Full Time', '5000-5500', 'Oversee recruitment and employee relations.', 'Degree in Human Resources.', 'HR management experience.', 'available', '2024-09-26 19:49:53', 0),
(13, 13, 12, 'IT Support Specialist', 'Part Time', '2500-3000', 'Provide technical support to users.', 'Degree in IT or related.', 'Experience in IT support preferred.', 'available', '2024-09-26 19:49:53', 0),
(14, 14, 14, 'Legal Advisor', 'Full Time', '6000-7000', 'Provide legal advice to clients.', 'Degree in Law.', 'Experience in corporate law.', 'available', '2024-09-26 19:49:53', 0),
(15, 15, 2, 'Marketing Executive', 'Full Time', '3500-4000', 'Plan and execute marketing campaigns.', 'Degree in Marketing or related.', 'Experience in digital marketing preferred.', 'available', '2024-09-26 19:49:53', 0),
(16, 16, 3, 'Sales Executive', 'Full Time', '4000-5000', 'Sell products and services to clients.', 'Degree in Sales or related field.', 'Proven sales experience.', 'available', '2024-09-26 19:49:53', 0),
(17, 2, 15, 'Lecturer', 'part_time', '20 - 25/hour', '- Deliver lectures on assigned subjects to students\r\n- Develop lesson plans and instructional materials\r\n- Assess student progress through assignments, exams, and class participation\r\n- Provide academic support and guidance to students\r\n- Stay updated with the latest developments in the subject area\r\n- Maintain accurate records of student attendance and performance', '- Educational Qualification: Master’s degree or higher in the relevant subject area\r\n- Experience: Prior teaching experience at a university or college level preferred\r\n\r\nSkills:\r\n- Strong communication and presentation skills\r\n- Ability to engage and motivate students\r\n- Proficiency in using digital tools for teaching (e.g., online learning platforms)\r\n', 'Contract Length: Semester-based or academic year-based', 'unavailable', '2024-09-29 14:25:50', 3),
(18, 2, 6, 'HR (Human Resources)', 'part_time', '20 - 25/hour', '1. Recruitment & Onboarding:\r\n- Assist in sourcing, screening, and interviewing potential candidates.\r\n- Help with onboarding new hires and conducting orientation sessions.\r\n\r\n2. Employee Records Management:\r\n- Maintain and update employee records and HR databases.\r\n- Ensure proper documentation and compliance with company policies.\r\n\r\n3. Support HR Policies:\r\n- Assist in the implementation and communication of HR policies and procedures.\r\n- Address employee queries regarding benefits, payroll, and policies.\r\n\r\n4. Training & Development:\r\n- Help coordinate training programs and workshops.\r\n- Monitor and track employee development progress.\r\n\r\n5. Performance Management:\r\n- Assist in performance review processes and feedback collection.\r\n- Collaborate with managers on employee evaluations.\r\n\r\n6. General HR Support:\r\n- Provide support in organizing employee engagement activities.\r\n- Manage day-to-day HR administrative tasks.', '1. Educational Background:\r\n- Pursuing or completed a degree in Human Resources, Business Administration, or related fields.\r\n\r\n2. Skills:\r\n- Strong communication and interpersonal skills.\r\n- Organizational and multitasking abilities.\r\n- Basic knowledge of HR practices and labor laws.\r\n- Familiarity with HR software/tools (e.g., MS Office, HRIS systems).\r\n\r\n3. Experience:\r\n- Previous experience or internship in HR is a plus but not mandatory.\r\n- Comfortable working with confidential information.\r\n\r\n4. Availability:\r\n- Part-time position, availability for approximately 15-20 hours per week.\r\n- Flexible schedule to accommodate other commitments.', 'Contract Duration: 6-month part-time contract, with the possibility of extension.\r\nGrowth Opportunities: Potential for full-time employment based on performance.\r\nWork Environment: Collaborative team with opportunities for learning and professional development.', 'available', '2024-09-30 11:40:47', 14),
(21, 2, 20, 'defrgt', 'full_time', '2400 - 3000', 'swdef', 'adssfdhfsgrjasf', 'sadfgn', 'unavailable', '2024-10-04 05:32:22', 0),
(22, 2, 2, 'Social Media Coordinator', 'part_time', '30 - 40/hour', '- Create, schedule, and publish engaging content across social media platforms\r\n- Monitor social media engagement (likes, comments, shares) and respond to followers\r\n- Develop social media strategies and campaigns to boost visibility and reach\r\n- Analyze social media metrics and performance reports\r\n- Collaborate with marketing teams to align social media with company goals\r\n', '- Experience managing social media accounts (Instagram, Twitter, LinkedIn, etc.)\r\n- Creative content creation skills (writing, graphics, or video)\r\n- Basic knowledge of social media analytics tools (e.g., Hootsuite, Buffer)\r\n- Strong communication and organizational skills\r\n- Familiarity with trends and best practices in social media marketing\r\n', '- Location: Remote\r\n- Work Hours: Flexible, part-time, may involve weekends\r\n- Salary: Competitive hourly rate\r\n- Growth: Opportunities for full-time roles in digital marketing\r\n\r\n', 'available', '2024-10-04 07:05:50', 0);

-- --------------------------------------------------------

--
-- Table structure for table `job_seeker`
--

CREATE TABLE `job_seeker` (
  `S_ID` int(11) NOT NULL,
  `S_Name` varchar(100) NOT NULL,
  `Gender` char(1) DEFAULT NULL CHECK (`Gender` in ('M','F')),
  `Age` int(11) NOT NULL,
  `S_PhoneNo` varchar(11) DEFAULT NULL,
  `S_Email` varchar(256) NOT NULL,
  `Address` varchar(256) NOT NULL,
  `AboutMe` text DEFAULT NULL,
  `S_profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_seeker`
--

INSERT INTO `job_seeker` (`S_ID`, `S_Name`, `Gender`, `Age`, `S_PhoneNo`, `S_Email`, `Address`, `AboutMe`, `S_profile_image`) VALUES
(1, 'vincent chu', 'M', 20, '01151539608', 'vincent.chu@example.com', 'beverly hills phase 2 block J3-3', 'i am hardworking', 'WhatsApp Image 2021-02-12 at 12.04.57 AM.jpeg'),
(3, 'John Doe', 'M', 28, '1234567890', 'john.doe@example.com', 'beverly hills phase 2 block J3-3', 'i work very slow', 'works.jpg'),
(44, 'ling tan', 'F', 20, '0123456789', 'ling@example.com', 'sabah', 'I am a dedicated and results-oriented professional with experience in customer service, IT support, and administrative roles. My strengths lie in providing excellent customer assistance, troubleshooting technical issues, and streamlining office operations. I enjoy problem-solving and am passionate about leveraging my skills to help businesses grow. I am now seeking part-time opportunities where I can contribute my expertise while continuing to expand my knowledge in new areas.\r\n\r\n', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resume`
--

CREATE TABLE `resume` (
  `R_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `Detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume`
--

INSERT INTO `resume` (`R_ID`, `S_ID`, `Detail`) VALUES
(1, 1, 'uploads/fileUpload/Your paragraph text.pdf'),
(2, 3, 'uploads/fileUpload/Your paragraph text.pdf'),
(8, 44, 'Career Objective:\r\nSeeking a part-time position in customer service, IT support, or administration where I can utilize my communication, technical, and organizational skills to support company operations and improve customer satisfaction.\r\n\r\nWork Experience:\r\nCustomer Service Representative\r\nABC Tech Solutions, Kuala Lumpur (Jan 2023 – Present)\r\n\r\n- Assisted customers with technical inquiries, resolved software and network issues.\r\n- Achieved 90% customer satisfaction through effective problem-solving and clear communication.\r\n\r\nAdministrative Assistant\r\nXYZ Company, Kuala Lumpur (Jun 2021 – Dec 2022)\r\n\r\n- Managed scheduling, coordinated meetings, and prepared reports.\r\n- Streamlined administrative processes, improving office efficiency by 15%.\r\n\r\nEducation:\r\nDiploma in Information Technology University of Malaya, Kuala Lumpur (2020 – 2023)\r\n\r\nSkills:\r\n- IT troubleshooting and customer support\r\n- Administrative and organizational abilities\r\n- Excellent written and verbal communication\r\n- Proficiency in MS Office, CRM software, and social media tools\r\n\r\nLanguages:\r\n- English (Fluent)\r\n- Malay (Fluent)\r\n- Mandarin (Intermediate)\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `S_ID` int(11) NOT NULL,
  `L_ID` int(11) NOT NULL,
  `Saved_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`S_ID`, `L_ID`, `Saved_At`) VALUES
(3, 1, '2024-09-29 05:42:09'),
(3, 2, '2024-09-29 05:44:29'),
(3, 17, '2024-09-29 14:42:08'),
(3, 18, '2024-09-30 11:49:22'),
(44, 18, '2024-10-04 07:01:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `U_ID` int(11) NOT NULL,
  `U_name` varchar(100) NOT NULL,
  `U_Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `U_profile_image` varchar(255) DEFAULT NULL,
  `Role` enum('job_seeker','employer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`U_ID`, `U_name`, `U_Email`, `Password`, `U_profile_image`, `Role`) VALUES
(1, 'vincent chu', 'vincent.chu@example.com', '$2y$10$ui.pIo8Tdq6a9dWWqkc8M.HOYUBsX9zOzmE66nkTLjluPwFgLE/MG', NULL, 'job_seeker'),
(2, 'alice chu', 'alice.chu@example.com', '$2y$10$fBEGPu7RlTIX.v6t7UvmLuXKp85MymIboCBM5QY2asUtHDipZiV5q', NULL, 'employer'),
(3, 'John Doe', 'john.doe@example.com', '$2y$10$C78fc0xhYTV.7B7lBjCZteMNJ/lYZHVzhsGGcnGdm7idcKKP7AD2G', NULL, 'job_seeker'),
(44, 'ling', 'ling@example.com', '$2y$10$8iRIAchEWdDIHzvJVro8I./hB4edXx.pDsnbbD.vb1sW.D9xJvm9q', NULL, 'job_seeker');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`C_ID`),
  ADD UNIQUE KEY `C_Name` (`C_Name`);

--
-- Indexes for table `employer`
--
ALTER TABLE `employer`
  ADD PRIMARY KEY (`E_ID`),
  ADD UNIQUE KEY `E_Email` (`E_Email`),
  ADD UNIQUE KEY `E_PhoneNo` (`E_PhoneNo`);

--
-- Indexes for table `job_application`
--
ALTER TABLE `job_application`
  ADD PRIMARY KEY (`A_ID`),
  ADD KEY `S_ID` (`S_ID`),
  ADD KEY `L_ID` (`L_ID`),
  ADD KEY `R_ID` (`R_ID`),
  ADD KEY `E_ID` (`E_ID`);

--
-- Indexes for table `job_listing`
--
ALTER TABLE `job_listing`
  ADD PRIMARY KEY (`L_ID`),
  ADD KEY `E_ID` (`E_ID`),
  ADD KEY `C_ID` (`C_ID`);

--
-- Indexes for table `job_seeker`
--
ALTER TABLE `job_seeker`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `S_PhoneNo` (`S_PhoneNo`),
  ADD UNIQUE KEY `S_Email` (`S_Email`);

--
-- Indexes for table `resume`
--
ALTER TABLE `resume`
  ADD PRIMARY KEY (`R_ID`),
  ADD KEY `S_ID` (`S_ID`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`S_ID`,`L_ID`),
  ADD KEY `L_ID` (`L_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`U_ID`),
  ADD UNIQUE KEY `U_Email` (`U_Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `C_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employer`
--
ALTER TABLE `employer`
  MODIFY `E_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `job_application`
--
ALTER TABLE `job_application`
  MODIFY `A_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `job_listing`
--
ALTER TABLE `job_listing`
  MODIFY `L_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `job_seeker`
--
ALTER TABLE `job_seeker`
  MODIFY `S_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `resume`
--
ALTER TABLE `resume`
  MODIFY `R_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `U_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `job_application`
--
ALTER TABLE `job_application`
  ADD CONSTRAINT `job_application_ibfk_1` FOREIGN KEY (`S_ID`) REFERENCES `job_seeker` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_application_ibfk_2` FOREIGN KEY (`L_ID`) REFERENCES `job_listing` (`L_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_application_ibfk_3` FOREIGN KEY (`R_ID`) REFERENCES `resume` (`R_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_application_ibfk_4` FOREIGN KEY (`E_ID`) REFERENCES `employer` (`E_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_listing`
--
ALTER TABLE `job_listing`
  ADD CONSTRAINT `job_listing_ibfk_1` FOREIGN KEY (`E_ID`) REFERENCES `employer` (`E_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_listing_ibfk_2` FOREIGN KEY (`C_ID`) REFERENCES `category` (`C_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resume`
--
ALTER TABLE `resume`
  ADD CONSTRAINT `resume_ibfk_1` FOREIGN KEY (`S_ID`) REFERENCES `job_seeker` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`S_ID`) REFERENCES `job_seeker` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`L_ID`) REFERENCES `job_listing` (`L_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
