
Package contents
=================
 - The Kaltura client library base (KalturaClientBase, KalturaObjectBase...)
 - Auto generated core APIs (KalturaClient...)
 - Required JAR files
 - Project files
 - Library test code and data files (KalturaClientTester/*)
 - Reference application (DemoApplication/*)

Running the test code
======================
1. Import the projects into Eclipse - 
	a. right click in the Package Explorer
	b. Import...
	c. Android->Existing Android Code Into Workspace
	d. Select the root dir containing all 3 android projects (KalturaClient, KalturaClientTester and DemoApplication)
	e. Make sure all 3 projects are selected, click ok
	f. Wait until the projects are automatically compiled (initially some errors will appear, 
		until the KalturaClient is compiled, they should go away automatically)
2. Edit KalturaClientTester/src/com.kaltura.client.test/KalturaTestConfig and fill out your Kaltura account information
3. Right click on KalturaClientTester/src/com.kaltura.client.test/KalturaTestSuite
4. Run As->Android JUnit Test


Running the demo application
=============================
1. Import the projects into Eclipse (see above)
2. Edit com.kaltura.activity.Splash/src/com.kaltura.activity/Settings.java
3. Search for etEmail.setText and etPassword.setText
4. Set the default user / password to the credentials of you Kaltura KMC account
5. Hit the play button
