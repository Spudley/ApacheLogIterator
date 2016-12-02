<?php
/**
 * Unit tests for ApacheLogIterator class
 * Since this is an iterator class, we don't need to test all the methods individually;
 * we can simply use a foreach() loop to check the whole thing in one go.
 *
 * @author: Simon Champion <simon@simonchampion.net>
 * @copyright Simon Champion and Connection Services Limited (http://www.connectionservices.com), 2016
 * @version 2.0 / 01-Dec-2016
 */

namespace Spudley/ApacheLogIterator;

class ApacheLogIteratorTest extends \PHPUnit_Framework_TestCase {
    private $tempfile = '';

    private $logEntries = array(
        'Jul 19 00:59:29 sdcweb1 mydomain.com: 192.168.1.1 - - [19/Jul/2012:00:59:28 +0100] "GET /query.php?q=te57+1ng&submit=Search HTTP/1.1" 200 7727 "http://www.referralurl.com/" "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1"',
        'Jul 21 15:10:54 sdcweb1 mydomain.com: 192.168.1.2 - - [21/Jul/2012:15:10:54 +0100] "GET /index.php HTTP/1.1" 200 32611 "http://www.referralurl2.com/" "Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0.1"',
        'Jul 21 15:10:54 sdcweb1 mydomain.com: 192.168.1.3 - - [21/Jul/2012:15:10:54 +0100] "GET /test.php?q=Decode%22This%2521 HTTP/1.1" 200 32611 "http://www.referralurl2.com/" "Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0.1"',
    );

    /**
     * The class we're testing extends SPLFileObject, so it has to read its data from a file.
     * So in order to unit test it, we have to create a temp file containing the test data.
     */
    protected function setUp() {
        $log = implode("\n",$this->logEntries);
        $this->tempfile = tempnam("/tmp", "ApacheLogIteratorTest");
        file_put_contents($this->tempfile, $log);
    }
    protected function tearDown() {
        if($this->tempfile) {unlink($this->tempfile);}
        $this->tempfile = '';
    }

    public function testIterator() {
        $expected = array(
              array (
                'originalLogEntry' => $this->logEntries[0],
                'localServer' => 'sdcweb1',
                'remoteIP' => '192.168.1.1',
                'datetime' => '19/Jul/2012:00:59:28 +0100',
                'method' => 'GET',
                'status' => '200',
                'bytes' => '7727',
                'referrer' => 'http://www.referralurl.com/',
                'userAgent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1',
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
              ),
              array (
                'originalLogEntry' => $this->logEntries[1],
                'localServer' => 'sdcweb1',
                'remoteIP' => '192.168.1.2',
                'datetime' => '21/Jul/2012:15:10:54 +0100',
                'method' => 'GET',
                'status' => '200',
                'bytes' => '32611',
                'referrer' => 'http://www.referralurl2.com/',
                'userAgent' => 'Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0.1',
                'request' => array (
                    'scheme' => 'http',
                    'host' => 'mydomain.com',
                    'path' => '/index.php',
                    'fullURL' => 'http://mydomain.com:/index.php',
                    'query' => '',
                    'queryArgs' => array (),
                ),
            ),
              array (
                'originalLogEntry' => $this->logEntries[2],
                'localServer' => 'sdcweb1',
                'remoteIP' => '192.168.1.3',
                'datetime' => '21/Jul/2012:15:10:54 +0100',
                'method' => 'GET',
                'status' => '200',
                'bytes' => '32611',
                'referrer' => 'http://www.referralurl2.com/',
                'userAgent' => 'Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0.1',
                'request' => array (
                    'scheme' => 'http',
                    'host' => 'mydomain.com',
                    'path' => '/test.php',
                    'fullURL' => 'http://mydomain.com:/test.php?q=Decode%22This%2521',
                    'query' => 'q=Decode%22This%2521',
                    'queryArgs' => array (
                        'q' => 'Decode"This%21',    //Validate that we don't do urldecode, but not double decoding (Ticket #1)
                    ),
                ),
            ),
        );
        $logIterator = new ApacheLogIterator($this->tempfile);
        $output = array();
        foreach ($logIterator as $logRecord) {
            $output[] = $logRecord;
        }
        $this->assertEquals($output,$expected,'Check that the records loaded match the expected data.');
    }
}
