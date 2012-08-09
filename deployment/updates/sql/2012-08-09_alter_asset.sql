ALTER TABLE  flavor_asset 
ADD  bitrate 			INT 			NOT NULL 	DEFAULT  '0' AFTER  height ,
ADD  frame_rate 		FLOAT 			NOT NULL 	DEFAULT  '0' AFTER  bitrate ,
ADD  video_codec_id 	VARCHAR( 127 ) 	NULL 		DEFAULT NULL AFTER  container_format;
