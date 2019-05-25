# Scheduled Event - Bulk Upload   

## Summary 

### Task  

There is a requirement to support the simultaneous creation of multiple scheduled events of various types in the DB,
rather than using UI-based tools to create them one by one.

### Decision

Use the CSV bulk upload infrastructure to support addition of multiple scheduled events and their related objects:
* Template entries
* Association of the event to a scheduled resource.

### Status

Decided. We are open to new alternatives if proposed.

  
## Detail

### Assumptions 

For this feature, we decided to utilize the existing CSV Bulk Upload mechanism for the following reasons:
 * OOTB support for the creation of large numbers of DB objects, including queue management.
 
 * Straightforward input format that would be easy to maintain and expand as needed.

### Constraints 

* Management of the event organizer and entry ownership must be enforced in the CSV format.

* The only accepted recurrence field format is a string of structure:  
                FREQ=,INTERVAL=,BYMONTHDAY=, BYMONTH=, BYDAY=, BYSETPOS=, COUNT=

### Implications  

Hitherto the creation of the scheduled events and their related objects was managed through UI-based client-side 
application. The addition of a server-side engine managing the creation of the same objects and their
associations to each other in the same way takes away from the genericness of the offering.

Example: if the end-user needs to associate an event with a template entry that is neither a KalturaLiveStreamEntry
nor a KalturaMediaEntry with mediaType=video, the offered tool would be useless to the end-user.

### Known limitations and potential issues

* Currently, only the creation of new events and associated objects is supported. There is no plan at this time to 
support event updates or deletions.

* A new event can only be of type 1 (RECORD) or 2 (LIVE_STREAM)

* An event's associated template entry can only be of type KalturaLiveStreamEntry or KalturaMediaEntry of mediaType=VIDEO

* The decision of the type of template entry to associate with the schedule event is up to the end uesr. It is 
possible to create a live scheduled event and associate it with the VOD entry. The code does not protec the end user
from this mistake and does not prevent it.

* New event ownerId will be set to 'batchUser' and this value cannot be overridden from the CSV

