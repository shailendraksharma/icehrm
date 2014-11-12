create table `Modules` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`menu` varchar(30) NOT NULL,
	`name` varchar(100) NOT NULL,
	`mod_group` varchar(30) NOT NULL,
	`mod_order` INT(11) NULL,
	`status` enum('Enabled','Disabled') default 'Enabled',
	`version` varchar(10) default '',
	`update_path` varchar(500) default '',
	primary key  (`id`),
	UNIQUE KEY `Modules_name_modgroup` (`name`,`mod_group`)
) engine=innodb default charset=utf8;

create table `Reports` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`details` text default null,
	`parameters` text default null,
	`query` text default null,
	`paramOrder` varchar(500) NOT NULL,
	`type` enum('Query','Class') default 'Query',
	primary key  (`id`),
	UNIQUE KEY `Reports_Name` (`name`)
) engine=innodb default charset=utf8;

create table `Attendance` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`employee` bigint(20) NOT NULL,
	`in_time` timestamp default '0000-00-00 00:00:00',
	`out_time` timestamp default '0000-00-00 00:00:00',
	`note` varchar(500) default null,
	CONSTRAINT `Fk_Attendance_Employee` FOREIGN KEY (`employee`) REFERENCES `Employees` (`id`),
	primary key  (`id`)
) engine=innodb default charset=utf8;


REPLACE INTO `Settings` (`name`, `value`, `description`) VALUES
('Company: Name', 'Sample Company Pvt Ltd', 'Update your company name - For updating company logo copy a file named logo.png to /app/data/ folder'),
('Email: SMTP Port', 'none', '25');

ALTER TABLE  `EmployeeLeaves` ADD  `attachment` varchar(100) NULL;

ALTER TABLE  `EmployeeDocuments` ADD  `attachment` VARCHAR( 100 ) NULL;

INSERT INTO `Reports` (`id`, `name`, `details`, `parameters`, `query`, `paramOrder`, `type`) VALUES
(1, 'Employee Details Report', 'This report list all employee details and you can filter employees by department, employment status or job title', '[\r\n[ "department", {"label":"Department","type":"select","remote-source":["CompanyStructure","id","title"],"allow-null":true}],\r\n[ "employment_status", {"label":"Employment Status","type":"select","remote-source":["EmploymentStatus","id","name"],"allow-null":true}],\r\n[ "job_title", {"label":"Job Title","type":"select","remote-source":["JobTitle","id","name"],"allow-null":true}]\r\n]', 'Select id, employee_id as ''Employee ID'',\r\nconcat(`first_name`,'' '',`middle_name`,'' '', `last_name`) as ''Name'',\r\n(SELECT name from Nationality where id = nationality) as ''Nationality'',\r\nbirthday as ''Birthday'',\r\ngender as ''Gender'',\r\nmarital_status as ''Marital Status'',\r\nssn_num as ''SSN Number'',\r\nnic_num as ''NIC Number'',\r\nother_id as ''Other IDs'',\r\ndriving_license as ''Driving License Number'',\r\n(SELECT name from EmploymentStatus where id = employment_status) as ''Employment Status'',\r\n(SELECT name from JobTitles where id = job_title) as ''Job Title'',\r\n(SELECT name from PayGrades where id = pay_grade) as ''Pay Grade'',\r\nwork_station_id as ''Work Station ID'',\r\naddress1 as ''Address 1'',\r\naddress2 as ''Address 2'',\r\ncity as ''City'',\r\ncountry as ''Country'',\r\nprovince as ''Province'',\r\npostal_code as ''Postal Code'',\r\nhome_phone as ''Home Phone'',\r\nmobile_phone as ''Mobile Phone'',\r\nwork_phone as ''Work Phone'',\r\nwork_email as ''Work Email'',\r\nprivate_email as ''Private Email'',\r\njoined_date as ''Joined Date'',\r\nconfirmation_date as ''Confirmation Date'',\r\nconfirmation_date as ''Confirmation Date'',\r\n(SELECT title from CompanyStructures where id = department) as ''Department'',\r\n(SELECT concat(`first_name`,'' '',`middle_name`,'' '', `last_name`,'' [Employee ID:'',`employee_id`,'']'') from Employees where id = supervisor) as ''Supervisor'' \r\nFROM Employees _where_', '["department","employment_status","job_title"]', 'Query'),
(2, 'Employee Leaves Report', 'This report list all employee leaves by employee, date range and leave status', '[\r\n[ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}],\r\n[ "status", {"label":"Leave Status","type":"select","source":[["NULL","All Statuses"],["Approved","Approved"],["Pending","Pending"],["Rejected","Rejected"]]}]\r\n]', 'EmployeeLeavesReport', '["employee","date_start","date_end","status"]', 'Class'),
(3, 'Employee Time Entry Report', 'This report list all employee time entries by employee, date range and project', '[\r\n[ "employee", {"label":"Employee","type":"select","allow-null":true,"remote-source":["Employee","id","first_name+last_name"]}],\r\n[ "project", {"label":"Project","type":"select","allow-null":true,"remote-source":["Project","id","name"]}],\r\n[ "date_start", {"label":"Start Date","type":"date"}],\r\n[ "date_end", {"label":"End Date","type":"date"}]\r\n]', 'EmployeeTimesheetReport', '["employee","date_start","date_end","status"]', 'Class');


