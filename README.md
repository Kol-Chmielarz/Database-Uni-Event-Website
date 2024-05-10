you need to get a server ssh root@'ip of the server'

you need to set up apache, php and mariadb in the server
place the all of the contents into the /var/www/html file of the server


LAMPAPI: Contains all the API php funtions that retrive the information from the database and passes it as a json file to ceod.js.
js file: contains js files, code.js its the connection of the php and html files and md5.js is the hash for the passwords.
the rest are the .hml and .css files that are the frontend pages for the user to see and interact.

create the database users with:
CREATE DATABASE users;

Finally set up the database with the following instructions:

CREATE TABLE `admin` (
  `aid` int(11) NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `events` (
  `eid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(190) NOT NULL,
  `time` datetime NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `user` (
  `pid` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(190) NOT NULL,
  `access` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `event_member` (
  `eid` int(11) NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `private_events` (
  `eid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `public_events` (
  `eid` int(11) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `students` (
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `rso` (
  `rid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rso_events` (
  `eid` int(11) NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rso_members` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `rid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `super_admin` (
  `spid` int(11) NOT NULL,
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `comments` (
  `cid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `comments` text NOT NULL,
  `rating` double NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;





CREATE TABLE `universities` (
  `uid` int(11) NOT NULL,
  `spid` int(11) NOT NULL,
  `name` varchar(190) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `description` text,
  `student_count` int(11) NOT NULL,
  `picture` varchar(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `location` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `pid` (`pid`);


ALTER TABLE `comments`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `pid` (`pid`);


ALTER TABLE `events`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `location` (`location`);




ALTER TABLE `event_member`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `pid` (`pid`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`pid`),
  ADD UNIQUE KEY `username` (`username`,`email`);


ALTER TABLE `private_events`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `aid` (`aid`);


ALTER TABLE `public_events`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `aid` (`aid`);


ALTER TABLE `rso`
  ADD PRIMARY KEY (`rid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `aid` (`aid`);


ALTER TABLE `rso_events`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `sid` (`rid`);


ALTER TABLE `rso_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rid` (`rid`),
  ADD KEY `pid` (`pid`);


ALTER TABLE `students`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`spid`,`pid`),
  ADD KEY `pid` (`pid`);

ALTER TABLE `universities`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD KEY `universities_ibfk_1` (`spid`),
  ADD KEY `location` (`location`);


ALTER TABLE `admin`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;


ALTER TABLE `comments`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE comments
MODIFY COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `events`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `user`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;


ALTER TABLE `rso`
  MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;


ALTER TABLE `rso_events`
  MODIFY `eid` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `rso_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;


ALTER TABLE `super_admin`
  MODIFY `spid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `universities`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

DELIMITER $$
CREATE TRIGGER `RSOStatusUpdateA` AFTER INSERT ON `rso_members` FOR EACH ROW BEGIN 
    IF ((SELECT COUNT(*) FROM rso_members M WHERE M.rid = NEW.rid) >  4) 
    THEN 
    UPDATE rso 
    SET rso.status = 'active'    
    WHERE rso.rid = NEW.rid;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `RSOStatusUpdateP` AFTER INSERT ON `rso_members` FOR EACH ROW BEGIN 
    IF ((SELECT COUNT(*) FROM rso_members M WHERE M.rid = NEW.rid) < 5) 
    THEN 
    UPDATE rso 
    SET rso.status = 'inactive'    
    WHERE rso.rid = NEW.rid;
    END IF;
END
$$
DELIMITER ;
