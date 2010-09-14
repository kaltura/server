# fix the search mechanism for partner search & kaltura network
# --------------------------------------------------------------
# fix for the partner policy 
update partner set appear_in_search=appear_in_search*2;

# use this to test how many entries are relevant for the update
select count(1) from entry where DISPLAY_IN_SEARCH>0;
select id,DISPLAY_IN_SEARCH as dis, media_type as mt ,search_text from entry where DISPLAY_IN_SEARCH>0 limit 100;
select id,DISPLAY_IN_SEARCH as dis, media_type as mt ,search_text from entry where DISPLAY_IN_SEARCH>0 order by int_id desc limit 100 ;


# fix the characters for the partner search mechanism
# first - make sure that all the ' single quotes are converted to _
update entry set search_text=replace( search_text , "'" , "_" ) where DISPLAY_IN_SEARCH>0 and search_text like "%|%"  and search_text like "\'%\'%" ;
# add the _<partner_id>_| to entries with no |
update entry set search_text=concat( "_" , partner_id , "_|" , search_text) where DISPLAY_IN_SEARCH>0 and search_text NOT like "%|%" ;
# second - add the _KAL_NET_ as the first string in the search_text (if entries are set with DISPLAY_IN_SEARCH>0 they are part of the kal-net)
update entry set search_text=concat("_KAL_NET_ " , search_text) where DISPLAY_IN_SEARCH>0 and search_text like "%|%" ;


update entry set search_text=concat( "_PAR_ONLY_ _" , partner_id , "_|" , search_text) where DISPLAY_IN_SEARCH>0 and search_text NOT like "%|%" and parnter_id=395;
update entry set search_text=replace( search_text , "'" , "_" ) where DISPLAY_IN_SEARCH>0 and search_text like "%|%"  and search_text like "\'%\'%" and parnter_id=395;