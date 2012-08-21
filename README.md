# ApacheLogIterator

### Version 1.0.1

A small PHP class intended to simplify the processing of Apache log files within a PHP program.

## Requirements

ApacheLogIterator has been tested under PHP 5.3. It is written with the intention of also working under 5.2, but this has not yet been tested.

This class has no external dependencies.

## Functionality

ApacheLogIterator is a parser that aims to make the reading of your Apache log file as simple and as efficient as possible.

It extends the SPLFileObject. Normal usage of the SPLFileObject would be to open a file and use a foreach() loop to load each line from the file in turn. The ApacheLogIterator class extends this, such that instead of simply returning the raw data as read from the file, it processes it into a structured array.

Because it's an iterator, you can loop through it using foreach() without having to load the whole file into memory. This is particularly useful because Apache log files can be very large. Only the current record is in memory at any given time.

In addition, ApacheLogIterator comes with a small helper class called ApacheLogFields. This defines the structure of the log records. If your Apache is configured to produce logs in a different format to the ones described in the code, you may override the ApacheLogFiles class to define your own log record format as required.

Filtering the output is trivial: Since it is an Iterator class, you can filter the output using PHP's built-in [FilterIterator](http://php.net/manual/en/class.filteriterator.php).

## Example

    $logFile = "/path/to/apache/log/file";
    $logIterator = new ApacheLogIterator($logFile);
    foreach ($logIterator as $logRecord) {
        print_r($logRecord); //do whatever you want to here with the output array.
    }

The output array looks something like this:

    array (
        'originalLogEntry' => '*** the full log record, in case you need it ***',
        'localServer' => 'yourservername',
        'remoteIP' => '192.168.1.1',
        'datetime' => '21/Jul/2012:15:10:54 +0100',
        'method' => 'GET',
        'status' => '200',
        'bytes' => '32611',
        'referrer' => 'http://www.referralurl.com/',
        'userAgent' => 'Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0.1',
        'request' => array (
            'scheme' => 'http',
            'host' => 'mydomain.com',
            'path' => '/query.php',
            'query' => 'q=te57+1ng&submit=Search',
            'fullURL' => 'http://mydomain.com:/query.php?q=te57+1ng&submit=Search',
            'queryArgs' => array (
                'q' => 'te57 1ng',
                'submit' => 'Search',
            ),
        ),
    )

## Copyright and License

This class was written by Simon Champion, and is copyright [Connection Services Limited](http://www.connectionservices.com/).

It is released under the General Public License version 3 (GPLv3); see COPYING.txt for full license text. Please contact us if you require alternative licensing arrangements.
