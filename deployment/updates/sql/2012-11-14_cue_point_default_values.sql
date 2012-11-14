
UPDATE	cue_point
SET		depth			= 0
WHERE	depth IS NULL;

UPDATE	cue_point
SET		children_count		= 0
WHERE	children_count IS NULL;

UPDATE	cue_point
SET		direct_children_count	= 0
WHERE	direct_children_count IS NULL;

		
ALTER TABLE	cue_point
CHANGE	depth					depth					INT( 11 ) NOT NULL DEFAULT  '0',
CHANGE	children_count			children_count			INT( 11 ) NOT NULL DEFAULT  '0',
CHANGE	direct_children_count	direct_children_count	INT( 11 ) NOT NULL DEFAULT  '0';
