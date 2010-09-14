ALTER TABLE  `media_info` CHANGE  `container_format`  `container_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE  `video_format`  `video_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE  `audio_format`  `audio_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;