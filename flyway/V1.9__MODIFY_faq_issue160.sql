ALTER TABLE `faq`
ADD COLUMN `created` timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE `faq`
ADD COLUMN `changed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();