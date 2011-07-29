
UPDATE syndication_feed 
SET display_in_search = -1 
WHERE display_in_search = 0;

UPDATE entry 
SET display_in_search = -1
WHERE display_in_search = 0
AND id IN(SELECT playlist_id FROM syndication_feed WHERE display_in_search = -1);