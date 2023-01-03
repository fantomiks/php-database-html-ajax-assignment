USE `test`;

DROP TABLE IF EXISTS `test`.`user`;

CREATE TABLE `test`.`user` ( `user_id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `access_count` INT NOT NULL , `modify_dt` TIMESTAMP NOT NULL , PRIMARY KEY (`user_id`)) ENGINE = InnoDB;

INSERT INTO `user` (`user_id`, `name`, `access_count`, `modify_dt`) VALUES (NULL, 'John Doe', 1, '2022-12-31'), (NULL, 'Peter Novak', 3, '2022-12-31'), (NULL, 'Luka Doncic', 2, '2022-12-31'), (NULL, 'LeBron James', 1, '2022-12-31'), (NULL, 'Goran Dragic', 10, '2022-12-31');
