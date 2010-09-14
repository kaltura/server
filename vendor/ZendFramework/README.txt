Welcome to the Zend Framework 1.9 Release! 

RELEASE INFORMATION
-------------------
Zend Framework 1.9.6 (r19198).
Released on November 23, 2009.

A detailed list of all features and bug fixes in this release may be found at:

    http://framework.zend.com/changelog/1.9.6

NEW FEATURES IN 1.9
-------------------
* Zend_Queue and Zend_Service_Amazon_Sqs, which provide the ability to
  use local and remote messaging and queue services for offloading
  asynchronous processes. (Contributed by Justin Plock and Daniel Lo)

* Zend_Queue_Adapter_PlatformJobQueue, a Zend_Queue adapter for Zend
  Platform's Job Queue. (Contributed by Zend Technologies)

* Zend_Rest_Route, Zend_Rest_Controller, and
  Zend_Controller_Plugin_PutHandler, which aid in providing RESTful
  resources via the MVC layer. (Contributed by Luke Crouch, SourceForge)

* Zend_Feed_Reader, which provides a common API to RSS and Atom feeds,
  as well as extensions to each format, caching, and a slew of other
  functionality. (Contributed by Pádraic Brady and Jurrien Stutterheim)

* Zend_Db_Adapter_Sqlsrv, a Zend_Db adapter for Microsoft's SQL Server
  driver for PHP. (Contributed by Juozas Kaziukenas and Rob Allen)

* Zend_Db_Table updates to allow using Zend_Db_Table as a concrete
  class by passing it one or more table definitions via the
  constructor. (Contributed by Ralph Schindler)

* Zend_Test_PHPUnit_Db, which provides Zend_Db support for PHPUnit's
  DBUnit support, allowing developers to do functional and integration
  testing against databases using data fixtures. (Contributed by
  Benjamin Eberlei)

* Annotation processing support for Zend_Pdf, as well as performance
  improvements. (Contributed by Alexander Veremyev)

* Zend_Dojo custom build layer support. (Contributed by Matthew Weier
  O'Phinney)

* Dojo upgraded to 1.3.2.

* Numerous Zend_Ldap improvements, including full support for CRUD
  operations, search, and manipulating tree structures. (Contributed by
  Stefan Gehrig)

* Zend_Log_Writer_Syslog, a Zend_Log writer for writing to your system
  log. (Contributed by Thomas Gelf)

* Zend_View_Helper_BaseUrl, a view helper for returning the current base
  URL to your application, as well as for constructing URLs to public
  resources. (Contributed by Robin Skoglund and Geoffrey Tran)

* Zend_Date now has support for the DateTime extension. (Contributed by
  Thomas Weidner)

* Zend_Locale has been upgraded to CLDR 1.7. (Contributed by Thomas
  Weidner)

* Zend_Translate now has plurals support for the Gettext, Csv, and Array
  adapters. (Contributed by Thomas Weidner)

* PHP 5.3 compatibility, including support for new features in the
  mysqli extension. All components are fully tested on both PHP 5.2.x
  and PHP 5.3.0.

In addition, a large number of smaller improvements were made throughout
the framework, and around 700 issues have been resolved or closed since
the release of 1.8.0!

A detailed list of all features and bug fixes in this release may be found at:

    http://framework.zend.com/changelog/1.9.6

IMPORTANT CHANGES
-----------------
Zend_Http_Client:
A change was made in Zend_Http_Client to correct ZF-5744 (Multiple file uploads
using the same $formname in setFileUpload). Instead of returning an associative
array of element name => upload information pairs, it now returns an array of
arrays, with the element name as part of the upload information. This allows
multiple file uploads using the same element name.

Zend_Config_Xml:
One deciding factor for many when choosing which Zend_Config format to use for
their application config had to do with support for constants. Our application
recommendations include defining two constants, APPLICATION_ENV and
APPLICATION_PATH, and many developers have found it useful that in INI and PHP
configurations, these constants are expanded during parsing. Zend_Config_Xml
now supports this via an XML namespace as follows:

    <config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
        <production>
            <includePath><zf:const
                zf:name="APPLICATION_PATH"/>/library</includePath>
        </production>
    </config>

On the PHP side, nothing changes.

Zend_Translate_Adapter_Ini:
Prior to PHP 5.3, parse_ini_file() and parse_ini_string() handled non-ASCII
characters in INI option values without an issue. However, starting in PHP 5.3,
such options will now be silently dropped in the array returned. If you are
upgrading to PHP 5.3 and using Zend_Translate_Adapter_Ini, this could cause
potential issues for you. If you use UTF-8 or Latin-1 characters in your INI
option keys (which are the message IDs for translation), you should either
modify these to use only ASCII characters, or choose a different translation
adapter.

Zend_Service_Amazon:
Zend_Service_Amazon has been updated to comply with the latest Amazon
ECommerce APIs -- which, as of 15 August 2009, will require an API key
for authentication. As a result, if you now use Zend_Service_Amazon, you
will need to pass your API key to the Zend_Service_Amazon constructor:
    
    $amazon = new Zend_Service_Amazon($appId, $countryCode, $apiKey);

Otherwise, usage of this component remains the same.

SYSTEM REQUIREMENTS
-------------------

Zend Framework requires PHP 5.2.4 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
------------

Please see INSTALL.txt.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
