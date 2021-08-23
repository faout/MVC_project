DROP TABLE IF EXISTS `accounts`;
DROP TABLE IF EXISTS `waterbottles`;
DROP TABLE IF EXISTS `comments`;

CREATE TABLE `accounts`
(
    `login` VARCHAR(20) NOT NULL,
    `pseudo` VARCHAR(30),
    `password` VARCHAR(100) NOT NULL, -- bCrypt hash aren't longer than 71 chars
    `status` VARCHAR(12) NOT NULL, -- 'connected' or 'disconnected'
    `creationDate` DATETIME NOT NULL,
    `lastConnectionDate` DATETIME DEFAULT NULL,
    PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `waterbottles`
(
    `id` INT(100) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) DEFAULT NULL,
    `price` FLOAT DEFAULT NULL,
    `picture` VARCHAR(255) DEFAULT NULL,
    `composition` VARCHAR(255) DEFAULT NULL,
    `category` VARCHAR(255) DEFAULT NULL,
    `creator` VARCHAR(20) NOT NULL,
    `creationDate` DATETIME NOT NULL,
    `lastUpdateDate` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `comments`
(
    `writer` VARCHAR(20) NOT NULL,
    `waterbottle` INT(100) NOT NULL,
    `writingDate` DATETIME NOT NULL,
    `comment` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `accounts` (`pseudo`,`login`,`password`,`status`,`creationDate`) VALUES
('Pascal Vanier','vanier','$2y$10$.9EOAhIybulDc9yT8cTn3uoyNZFUWRA1VDFsR1GLJyWWyWVlARdBW', 'disconnected', NOW());
INSERT INTO `accounts` (`pseudo`,`login`,`password`,`status`,`creationDate`) VALUES
('Jean-Marc Lecarpentier','lecarpentier','$2y$10$12/qOtaPGi1vcnGa2KPIb.yzR3d.7y0/5I6t6L9UDtQkJQecXa/bG', 'disconnected', NOW());
