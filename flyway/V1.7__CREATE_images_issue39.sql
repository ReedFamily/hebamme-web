CREATE TABLE `images` (
    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `height` INT DEFAULT 0,
    `width` INT DEFAULT 0,
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;