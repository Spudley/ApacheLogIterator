<?php
/**
 * Iterator class to read an Apache access log.
 *
 * @example:
 *  $log = new ApacheLogIterator($filename);
 *  foreach($log as $key => $data) {
 *      print "Record $key:\n".print_r($data,1)."\n";
 *  }
 *
 * Defaults to using the log format specified in the ApacheLogFields class.
 * If your log format differs from that one, create your own override class for ApacheLogFields
 * and pass in and instance of it to the constructor here:
 *
 * ie: $log = new ApacheLogIterator($filename, new myApacheLogFields());
 *
 * @author: Simon Champion <simon.champion@connectionservices.com>
 * @copyright Connection Services Limited (http://www.connectionservices.com), 2012
 * @version 1.0 / 17-Aug-2012
 */
class ApacheLogIterator extends SplFileObject {
    private $fields=null;   //instance of ApacheLogFields or similar class.

    public function __construct($filename, ApacheLogFields $fieldsObject=null) {
        if(!$fieldsObject) {$fieldsObject = new ApacheLogFields();}
        $this->fields=$fieldsObject;
        parent::__construct($filename);
    }

    /**
     * Override the standard SplFileObject iterator output so that it returns the Apache log parsed into an array rather than simply outputting the row.
     * @return array containing data parsed from the current apache log record. Or null if record was invalid (ie did not match the regex)
     */
    public function current() {
        return $this->parseLogRow(parent::current());
    }

	private function parseLogRow($row) {
        $matches = array();
		preg_match($this->fields->regex, $row, $matches);
		if(!isset($matches[0])) { return null; }
        $output = array();
        foreach($this->fields->fieldArray as $key=>$field) {
            if($field) {$output[$field]=trim($matches[$key]);}
        }
        if(!isset($output['scheme'])) {$output['scheme']='http';}
        $output['request'] = $this->parseURL($output['scheme'],$output['host'],$output['query']);
        unset($output['scheme'],$output['host'],$output['query']);
        return $output;

	}
    private function parseURL($protocol,$domain,$query) {
        $queryArgs = array();
        $fullURL = strtolower($protocol)."://".$domain.$query;
        $output = parse_url($fullURL);
        $output['fullURL'] = $fullURL;
        if(isset($output['query'])) {
            parse_str(urldecode($output['query']),$queryArgs);
        } else {
            $output['query'] = '';
        }
        $output['queryArgs']=$queryArgs;
        return $output;
    }
}
