CREATE TABLE `dashboard_metadata` (
  `dashboard_id` VARCHAR(255) NOT NULL PRIMARY KEY,
  `grafana_id` INT NOT NULL,
  `dashboard_version` INT NOT NULL,
  `dashboard_schema_version` INT DEFAULT NULL
) DEFAULT CHARACTER SET 'utf8';
