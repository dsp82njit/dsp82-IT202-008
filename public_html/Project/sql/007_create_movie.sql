CREATE TABLE IF NOT EXISTS `MOVIE2` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(20) NOT NULL,
    `year` INT,
    `stars` TEXT,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
    
)