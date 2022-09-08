# AXEL-PHP - Axel Accelerated Download Functionality For PHP

[![Codeship Status for ianoshorty/axel-php](https://codeship.com/projects/a0f58ef0-e7b1-0132-651b-4e340869c11f/status?branch=master)](https://codeship.com/projects/82613) [![Code Climate](https://codeclimate.com/github/ianoshorty/axel-php/badges/gpa.svg)](https://codeclimate.com/github/ianoshorty/axel-php) [![Latest Stable Version](https://poser.pugx.org/axel-php/axel/v/stable)](https://packagist.org/packages/axel-php/axel) [![Total Downloads](https://poser.pugx.org/axel-php/axel/downloads)](https://packagist.org/packages/axel-php/axel) [![Latest Unstable Version](https://poser.pugx.org/axel-php/axel/v/unstable)](https://packagist.org/packages/axel-php/axel) [![License](https://poser.pugx.org/axel-php/axel/license)](https://packagist.org/packages/axel-php/axel)

## General

The AXEL-PHP library wraps around the C based [Axel] library. Axel performs accelerated downloads from the command line, similar to `wget`.

AXEL-PHP offers asynchronous and synchronous downloads. See below for usage.

AXEL-PHP also comes with an optional FIFO download manager with the ability to set numbers of current downloads. Just implement `AxelDownloadManagerQueueInterface` together with your favourite queuing service.

** _PLEASE NOTE_: This library is under active development as an alpha and is subject to change at any time. **

## Example Usages

### Sync Download With Complete Callback

```php
$axel = new Axel\AxelDownload();
$axel->start('http://www.google.com', null, null, function($axel, $status, $success, $error) {
    echo 'File Downloaded';
    print_r($status);
});
```

### Start Async Download

```php
$axel = new Axel\AxelDownload();
$axel->startAsync('http://ipv4.download.thinkbroadband.com/1GB.zip', 'test.zip', '~/');
```

### Start Async Download With Progress Callbacks

```php
$axel = new Axel\AxelDownload();
$axel->startAsync('http://ipv4.download.thinkbroadband.com/1GB.zip', 'test.zip', '~/', function($axel, $status, $success, $error) {
   echo 'Progress updated';
   print_r($status);
});
```

### Setup Download / Delayed Start

```php
$axel = new Axel\AxelDownload();
$axel->addDownloadParameters([
    'address'           => 'http://www.google.com',
    'filename'          => 'test.html',
    'download-path'     => '~/',
    'callback'          => function($axel, $status, $success, $error) {
        echo 'Progress updated';
        print_r($status);
    }
]);
$axel->startAsync();
```

### Get Download Status

```php
$status = $axel->updateStatus();
```

### Cleanup

```php
$axel->clearCompleted()
```

### Axel Managed Download Queue (Synchronous)

```php
$dm = new Axel\AxelDownloadManager(new AxelDownloadManagerSyncQueue(), 'axel');
$dm->queueDownload('http://www.google.com', 'file1.html');
$dm->queueDownload('http://www.yahoo.com', 'file2.html');
$dm->processQueue();
```

### Version
0.0.13

---

## Installation

### Prerequesites

In order to install RPVR you will need:

 - PHP 5.4 or higher
 - [Axel] - A linux server with Axel installed

### Install Instructions

The preferred method of installation is via [Composer] and [Packagist].

  1. Install the axel command-line tool on the target system - `$ sudo apt-get install axel`
  2. Install the AXEL-PHP library - `$ composer require axel-php/axel`

---
## Development

Want to contribute? Great! Feel free to get in touch with me and we can collaborate, or fork / pull as you like.

### TODO

  - Possible log options / write to log file?
  - Intelligently deal with both concurrent connections and concurrent downloads
  - Check write permissions in download directory
  - Pause queue (Maybe subclass?)
  - Test queue Async

---
## License
The MIT License (MIT)

Copyright (c) 2015 Ian Outterside ([Ian Builds Apps]).

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

[LAMP]:http://laravel.com/docs/5.0/homestead
[Axel]:http://axel.alioth.debian.org
[Ian Builds Apps]:http://www.ianbuildsapps.com
[Composer]:https://getcomposer.org
[Packagist]:https://packagist.org