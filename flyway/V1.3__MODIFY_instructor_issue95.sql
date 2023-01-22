ALTER TABLE `instructor`
ADD COLUMN `team_member` BOOLEAN DEFAULT false;

UPDATE `instructor` SET `team_member` = true WHERE last_name = 'Reed' OR last_name = 'Altenbeck';