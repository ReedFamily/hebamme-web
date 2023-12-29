CREATE TABLE `gallery_images`(
    `gallery_id` BIGINT(20),
    `images_id` BIGINT(20),
    FOREIGN KEY (gallery_id) REFERENCES gallery(id),
    FOREIGN KEY (images_id) REFERENCES images(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

