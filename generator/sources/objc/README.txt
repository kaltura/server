Package contents
=================
 - The Kaltura client library base (KalturaClientBase, KalturaXmlParsers)
 - Auto generated core APIs (KalturaClient)
 - Auto generated plugin APIs (KalturaPlugins/*)
 - The 'ASIHttpRequest' open source library (http://allseeing-i.com/ASIHTTPRequest/)
 - Project files
 - Library test code and data files (KalturaClientTester/*)
 - Reference iPhone / iPad applications

Adding the KalturaClient to an Xcode 4 project
=============================================
1. Open the target project in XCode
2. Locate KalturaClient.xcodeproj in Finder
3. Drag KalturaClient.xcodeproj to the project in XCode
	IMPORTANT: this doesn't work correctly if the KalturaClient project is open in XCode
		Make sure to close all Xcode projects before dragging (specifically, don't drag
		the KalturaClient project from XCode - drag it from Finder)
	KalturaClient.xcodeproj should now appears under your project and be expandable
4. Click on your project and select Build Settings->Search Paths
5. Add the following paths to 'Header Search Paths'
	../KalturaClient/KalturaClient
	../KalturaClient/KalturaClient/ASIHTTPRequest
	../KalturaClient/KalturaClient/KalturaPlugins
	NOTE: these paths apply when your project is saved under the same folder as KalturaClient
		if it's not, adjust the paths appropriately
6. Click on your project and select Build Phases
7. Locate libKalturaClient.a under KalturaClient.xcodeproj->Products
8. Drag libKalturaClient.a to 'Link Binary With Libraries'
9. Add the following libraries to 'Link Binary With Libraries'
	libz.dylib
	libxml2.dylib
	CFNetwork.framework
	MobileCoreServices.framework
	SystemConfiguration.framework

Running the library test code
==============================
1. Open XCode
2. Open the KalturaClientTester project (File->Open)
3. Edit KalturaClientTester.m and fill out your partner id and admin secret (optionally, change the user id)
4. Choose the KalturaClientTester > iPhone simulator scheme
5. Build the project (Product->Build)
6. Hit play
7. Click the Go button in the test app

Note: The library was tested under iPhone Simulator V4.3, built under xCode V4.1


Running the demo application
==============================
1. Open XCode
2. Open the Kaltura project under DemoApplication (File->Open)
3. Open Shared/Kaltura-Info.plist
4. Type your KMC credentials under the UserEmail / UserPassword fields
5. Choose the Kaltura > iPhone simulator scheme
6. Hit play

Running the demo application with DRM support
==============================================

1. Open Xcode 
2. Open the Kaltura project under DemoApplication (File->Open)
3. Choose the "Kaltura widevine" > Device (iPhone/iPad) scheme
4. Hit play
