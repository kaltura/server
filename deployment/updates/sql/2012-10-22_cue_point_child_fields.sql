ALTER TABLE  cue_point 
ADD  depth 					INT NULL DEFAULT NULL AFTER  thumb_offset ,
ADD  children_count			INT NULL DEFAULT NULL AFTER  depth ,
ADD  direct_children_count	INT NULL DEFAULT NULL AFTER  children_count;