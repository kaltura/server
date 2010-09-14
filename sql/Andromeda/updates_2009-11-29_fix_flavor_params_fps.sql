update flavor_params set frame_rate = '0' where frame_rate = '25';

ALTER TABLE media_info CHANGE video_frame_rate video_frame_rate FLOAT; 
ALTER TABLE flavor_params CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 
ALTER TABLE flavor_params_output CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 
ALTER TABLE flavor_asset CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 

ALTER TABLE flavor_params_output ADD `audio_resolution` INTEGER default 0 AFTER `audio_sample_rate`;