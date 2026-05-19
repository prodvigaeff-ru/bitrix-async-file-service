CREATE TABLE IF NOT EXISTS `fp_file_queue` (
    `ID`             int(11)      NOT NULL AUTO_INCREMENT,
    `FILE_ID`        int(11)      NOT NULL,
    `ORIGINAL_NAME`  varchar(255) NOT NULL DEFAULT '',
    `STATUS`         enum('pending','processing','done','error') NOT NULL DEFAULT 'pending',
    `ERROR_MESSAGE`  text,
    `CREATED_AT`     datetime     NOT NULL,
    `UPDATED_AT`     datetime     NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `idx_status`  (`STATUS`),
    KEY `idx_file_id` (`FILE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
