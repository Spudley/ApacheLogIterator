<?php
namespace Spudley/ApacheLogIterator;

/**
 * A config class for ApacheLogIterator.
 * This allows the main iterator class to be functional yet configurable.
 * If we have a log file in a different format, we simply need to override this class and pass an instance of the
 * override class into ApacheLogIterator's constructor. An override class must provide the $regex and $fieldArray
 * variables, in the formats shown here. $fieldArray must include at a minimum 'host' and 'query' elements so that
 * it can parse the URL properly.
 *
 * Example Apache log file record, as parsed by this class:
 *    Jul 19 00:59:29 sdcweb1 mydomain.com: 192.168.1.2 - - [19/Jul/2012:00:59:28 +0100] "GET /query.php?q=te57+1ng&submit=Search HTTP/1.1" 200 7727 "http://www.referralurl.com/" "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1"
 *
 * If your Apache log files differ in format from this, write your own class that extends or replaces this class and
 * implements suitable regex and fieldArray properties, and pass an instance of it into ApacheLogIterator's constructor.
 *
 * @author: Simon Champion <simon@simonchampion.net>
 * @author: Radu Topala <radu.topala@trisoft.ro>
 * @copyright Simon Champion and Connection Services Limited (http://www.connectionservices.com), 2016
 * @version 2.0 / 01-Dec-2016
 */
class ApacheLogFields {
    /**
     * @var string Regular expression to extract relevant data from an Apache log record.
     */
    public $regex = '/^\w\w\w \d\d? \d\d:\d\d:\d\d\s(\S+)\s(\S+)\s(\d+\.\d+\.\d+\.\d+)\s-\s-\s\[([^\[]+)\] "(\S+) (.*?) (\S+)\/\S+" (\d+) (\d+) "(.*?)" "(.*?)"\s*$/';
    //                 [date/time without year...]  (box)  (dom)  (ip address........)          (datetime)  (GET) (URL) (proto)[/ver](sts)(bytes)(referer)(UA)

    /**
     * @var array Defines the index position of each field in the regex matches array. (
     */
    public $fieldArray = array(
        'originalLogEntry', //element zero is the full returned string.
        'localServer',
        'host',
        'remoteIP',
        'datetime',
        'method',
        'query',
        'scheme',
        'status',
        'bytes',
        'referrer',
        'userAgent',
    );
}
