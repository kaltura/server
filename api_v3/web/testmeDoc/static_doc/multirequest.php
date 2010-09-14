<h2>multiRequest</h2>

<h3>Description</h3>
<p>
	In order to reduce the number of round-trips to Kaltura server, when required to perform few API calls,
	Kaltura API offers a multiRequest service.
</p>
<p>
	Using that service, you can pass multiple requests in one REST request and you are returned with array of
	results, where each element is a result of one of your requests.
</p>
<p></p>
<p>
	The multiRequest service also allows you to send sequential requests where one request can depend on one of its preceding requests.
</p>

<h3>Example</h3>
<p>
Following is example for 2 sequential requests that are independant.
</p>
<pre>
	Request URL: api_v3/index.php?service=multirequest&action=null
	POST variables:
		1:service=baseEntry
		1:action=get
		1:version=-1
		1:entryId=0_zsadqv3e
		2:service=mixing
		2:action=getReadyMediaEntries
		2:mixId=0_zsadqv3e
		2:version=-1
		ks={ks}
</pre>
<p>
	For creating a dependant requests, you can use the following pattern as input in the variable that you want
	to have its value replaced with a result from a preceding request
</p>
<pre>
{num:result:porpertyName}
</pre>
<p>
	Where:
	<ul>
		<li><b>num</b> - is the number of the request of which you would like to collect data from</li>
		<li><b>:result:</b> - tells Kaltura API that it should replace this value with a result from another request</li>
		<li><b>properyName</b> - is the property to take from the object of the required result</li>
	</ul>
	Example for a dependant request:
</p>
<pre>
	Request URL: api_v3/index.php?service=multirequest&action=null
	POST variables:
		1:service=media
		1:action=list
		1:filter:nameLike=myentry
		2:service=media
		2:action=get
		2:entryId={1:result:objects:0:id}
		ks={ks}
</pre>
<p>
	In the above example, the first request will <b>list entries that their name is like 'myentry'</b>.<br />
	media.list request is returning an object of type <b>KalturaMediaListResponse</b> which contains an object named <b>'objects'</b> of type <b>KalturaMediaEntryArray</b><br />
</p>
<p>
	The second request is media.get which suppose to get entryId as input.
</p>
	In this example, the entryId input is dynamic, and will be fetched from the first request. 
	Since media.list response is constructed of array object within a response object, the first property we want to access is KalturaMediaEntryArray<br />
	In that array we want to take the first element (index 0), so we added ':0' to the request value. 
	Out of the first element, we want only the ID because that is the input we want for the second request, so we added ':id'<br />
</p>
	