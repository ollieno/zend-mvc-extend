<?php

class Twm_Service_Solr extends Zend_Service_Abstract {

	/**
	 * Charset used for encoding
	 * @var string
	 */
	protected $_charset = 'UTF-8';
	/**
	 * Hostname solr server
	 * @var string
	 */
	protected $_host = 'localhost';
	/**
	 * TCP/IP port to use in requests
	 * @var int
	 */
	protected $_port = 8080;
	/**
	 * User Agent string to send in requests
	 * @var string
	 */
	protected $_userAgent;
	protected $_responseWriter = 'json';
	protected $_core;

	/**
	 * Constructor
	 *
	 * @param string $apiKey Akismet API key
	 * @param string $blog Blog URL
	 * @return void
	 */
	public function __construct($options) {
		$this->setOptions($options)
			->setUserAgent('Zend Framework/' . Zend_Version::VERSION . ' | Solr/1.4');
	}

	public function setOptions($options) {
		if (!is_array($options)) {
			throw new Exception('options should be an array');
		}

		foreach ($options as $option => $value) {
			$method_name = 'set' . ucfirst($option);
			if (method_exists($this, $method_name)) {
				$this->{$method_name}($value);
			}
		}
		return $this;
	}

	/**
	 * Retrieve charset
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->_charset;
	}

	/**
	 * Set charset
	 *
	 * @param string $charset
	 * @return Twm_Service_Solr
	 */
	public function setCharset($charset) {
		$this->_charset = $charset;
		return $this;
	}

	public function setHost($host) {
		if (!is_string($host)) {
			require_once 'Zend/Service/Exception.php';
			throw new Zend_Service_Exception('Invalid host');
		}

		$this->_host = $host;
		return $this;
	}

	public function getHost() {
		return $this->_host;
	}

	/**
	 * Retrieve TCP/IP port
	 *
	 * @return int
	 */
	public function getPort() {
		return $this->_port;
	}

	public function setCore($name) {
		$this->_core = $name;
	}

	/**
	 * Set TCP/IP port
	 *
	 * @param int $port
	 * @return Twm_Service_Solr
	 * @throws Zend_Service_Exception if non-integer value provided
	 */
	public function setPort($port) {
		if (!is_numeric($port)) {
			require_once 'Zend/Service/Exception.php';
			throw new Zend_Service_Exception('Invalid port');
		}

		$this->_port = $port;
		return $this;
	}

	/**
	 * Retrieve User Agent string
	 *
	 * @return string
	 */
	public function getUserAgent() {
		return $this->_userAgent;
	}

	/**
	 * Set User Agent
	 *
	 * Should be of form "Some user agent/version | Akismet/version"
	 *
	 * @param string $userAgent
	 * @return Twm_Service_Solr
	 * @throws Zend_Service_Exception with invalid user agent string
	 */
	public function setUserAgent($userAgent) {
		if (!is_string($userAgent)
			|| !preg_match(":^[^\n/]*/[^ ]* \| Solr/[0-9\.]*$:i", $userAgent)) {
			throw new Zend_Service_Exception('Invalid User Agent string; must be of format "Application name/version | Solr/version"');
		}

		$this->_userAgent = $userAgent;
		return $this;
	}

	public function setResponseWriter($writer) {
		$this->_responseWriter = $writer;
		return $this;
	}

	public function getResponseWriter() {
		return $this->_responseWriter;
	}

	/**
	 * Post a request
	 *
	 * @param string $host
	 * @param string $path
	 * @param array  $params
	 * @return mixed
	 */
	protected function _post($path, $data, $contentType='application/json') {

		if ($this->_core) {
			$path = "/solr/{$this->_core}/{$path}";
		} else {
			$path = "/solr/{$path}";
		}
		$uri = "http://{$this->getHost()}:{$this->getPort()}$path";
		$client = self::getHttpClient();
		$client->setUri($uri);
		$client->setConfig(array(
			'useragent' => $this->getUserAgent(),
		));

		$client->setHeaders(array(
			'Host' => $this->getHost(),
			'Content-Type' => $contentType
		));
		$client->setRawData($data, $contentType);

		$client->setMethod(Zend_Http_Client::POST);
		$response = $client->request();

		return $response;
	}

	protected function _get($path, Twm_Service_Solr_Search_Query $query) {
		if ($this->_core) {
			$path = "/solr/{$this->_core}/{$path}";
		} else {
			$path = "/solr/{$path}";
		}
		$uri = "http://{$this->getHost()}:{$this->getPort()}$path";

		$query->setWriterType($this->getResponseWriter());
		$uri .= "?" . $query->toUriParams();

		$client = self::getHttpClient();
		$client->setUri($uri);
		$client->setConfig(array(
			'useragent' => $this->getUserAgent(),
		));

		$client->setHeaders(array(
			'Host' => $this->getHost()
		));
		$client->setMethod(Zend_Http_Client::GET);
		$response = $client->request();
		return $response;
	}

	public function addDocument(Twm_Service_Solr_Document $doc) {

		$xml = new DOMDocument();

		$xAdd = $xml->createElement('add');
		$xml->appendChild($xAdd);

		$xDoc = dom_import_simplexml($doc->toXml());
		$xDoc = $xml->importNode($xDoc, true);
		$xAdd->appendChild($xDoc);

		$response = $this->_post('update', $xml->saveXML());

		$xml = simplexml_load_string($response->getBody());
		$status = $xml->xpath('//int[@name=\'status\']');
		// TODO parsing errors
		if (isset($status[0]) && $status[0] == '0') {
			return true;
		}
		throw new Exception($response);
		return false;
	}

	public function find($query) {
		if (is_string($query)) {
			$query = Twm_Service_Solr_Search_QueryParser::parse($query);
		}

		if (!$query instanceof Twm_Service_Solr_Search_Query) {
			throw new Twm_Service_Solr_Exception('Query must be a string or Twm_Search_Solr_Search_Query object');
		}

		$response = $this->_get('select', $query);

		return new Twm_Service_Solr_Search_Result($response->getBody());
	}

	public function commit() {
		$response = $this->_post('update', '<commit/>');

		$xml = simplexml_load_string($response->getBody());

		$status = $xml->xpath('//int[@name=\'status\']');
		if ($status[0] == '0') {
			return true;
		}
		return false;
	}

	public function rollback() {
		$response = $this->_post('update', '<rollback/>');

		$xml = simplexml_load_string($response->getBody());

		$status = $xml->xpath('//int[@name=\'status\']');
		if (isset($status[0]) &&  $status[0] == '0') {
			return true;
		}
		return false;
	}

	public function optimize() {
		$response = $this->_post('update', '<optimize/>');

		$xml = simplexml_load_string($response->getBody());

		$status = $xml->xpath('//int[@name=\'status\']');
		if ($status[0] == '0') {
			return true;
		}
		return false;
	}

	public function deleteById($id)
	{
		$xml = "<delete><id>{$id}</id></delete>";
		$response = $this->_post('update', $xml);
		$xml = simplexml_load_string($response->getBody());
		$status = $xml->xpath('//int[@name=\'status\']');
		if (isset($status[0]) &&  $status[0] == '0') {
			return true;
		}
		return false;
	}

	public function delete($query) {

		if (is_numeric($query)) {
			$xml = "<delete><id>{$query}</id></delete>";
		} else {
			$xml = "<delete><query>{$query}</query></delete>";
		}
		$response = $this->_post('update', $xml);

		$xml = simplexml_load_string($response->getBody());

		$status = $xml->xpath('//int[@name=\'status\']');

		if (isset($status[0]) &&  $status[0] == '0') {
			return true;
		}
		return false;
	}

}