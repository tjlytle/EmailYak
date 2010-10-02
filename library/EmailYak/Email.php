<?php
class EmailYak_Email
{
	//writable properties
	protected $_to;
	protected $_from;
	protected $_received;
	protected $_subject;
	protected $_htmlBody;
	protected $_textBody;
	
	
	//read-only properties
	protected $_emailId;
	protected $_status;
	protected $_message;
	
	//special header proerty
	protected $_headers;
	
	public function __construct($data)
	{
		if(is_string($data)){
			$data = json_decode($data);
		}
		foreach($data as $field => $value){
			switch($field){
				case 'EmailID':
					$field = '_emailId';
					break;
				case 'HtmlBody':
					$field = '_htmlBody';
					break;
				case 'TextBody':
					$field = '_textBody';
					break;
				case 'Headers':
					$this->setHeaders($value);
					continue 2;
					break;
				default:
					$field = '_' . strtolower($field);
					break;
			}
			$this->$field = $value;
		}
		
	}
	
	public function __get($name)
	{
		$method = 'get' . ucfirst($name);
		if(method_exists($this, $method)){
			return $this->$method();
		}
		
		$property = '_' . $name;
		if(property_exists($this, $property)){
			return $this->$property;
		}
		
		throw new Exception("Property '$name' does not exsist");
	}
	
	public function __set($name, $value)
	{
		$method = 'set' . ucfirst($name);
		if(method_exists($this, $method)){
			$this->$method($value);
			return;
		}
		
		$property = '_' . $name;
		if(property_exists($this, $property)){
			$this->$property = $value;
			return;
		}
		
		throw new Exception("Property '$name' does not exsist");
	}

	public function getHeaders()
	{
		foreach($this->_headers as $field => $value){
			if(count($value) == 1){
				$headers[$field] = $value[0]; 
			} else {
				$headers[$field] = $value;
			}
		}
		
		return $headers;
	}
	
	public function setHeaders($headers)
	{
		foreach($headers as $header){
			$this->setHeader($header);
		}
	}
	
	public function setHeader($header, $value = null)
	{
		if(is_object($header)){
			$field = $header->Name;
			$value = $header->Value;
		}
		
		$this->_headers[$field][] = $value;
	}
	
	protected function setStatus()
	{
		throw new Exception("Property 'status' is read-only");
	}

	protected function setMessage()
	{
		throw new Exception("Property 'status' is read-only");
	}

	protected function setEmailId()
	{
		throw new Exception("Property 'status' is read-only");
	}
}