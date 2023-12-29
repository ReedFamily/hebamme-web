CREATE TABLE `gallery` (
  `id` bigint(20) NOT NULL,
  `name` varchar(65) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gallery_name` (`name`);

ALTER TABLE `gallery`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;