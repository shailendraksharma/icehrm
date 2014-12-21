ALTER TABLE `Attendance` ADD KEY `in_time` (`in_time`);
ALTER TABLE `Attendance` ADD KEY `out_time` (`out_time`);
ALTER TABLE `Attendance` ADD KEY `employee_in_time` (`employee`,`in_time`);
ALTER TABLE `Attendance` ADD KEY `employee_out_time` (`employee`,`out_time`);


ALTER TABLE `EmployeeTimeEntry` ADD KEY `employee_project` (`employee`,`project`);
ALTER TABLE `EmployeeTimeEntry` ADD KEY `employee_project_date_start` (`employee`,`project`,`date_start`);

