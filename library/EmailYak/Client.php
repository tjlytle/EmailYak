<?php
require_once 'Zend/Rest/Client.php';
class EmailYak_Client extends Zend_Rest_Client
{
	
	protected $_key = null;
	protected $_format = 'json';
	protected $_version = 'v1';
	
	const API_HOST = 'https://api.emailyak.com';
	
	protected $_errors = array(
		402 => "Invalid JSON/XML. Malformed JSON/XML syntax.",
		403 => "Permission denied. Account does not have ownership of domain. ",
		420 => "Internal Error. There was an error in the system. ",
		421 => "Input Parameter Error. ",
		423 => "API key does not exist. ",
		424 => "Account disabled.",
		426 => "Domain has been disabled. ",
		427 => "The domain is not registered with Email Yak. ",
		428 => "The requested record is not found. ",
		430 => "Account not allowed access to requested version of API. ",
		431 => "Invalid Response Format. In the url, specify ../json/.. or ../xml/.. ",
		432 => "Invalid Request Format. Needs to be JSON or XML.",
		503 => "Service is Temporarily Down. Please stand by.",
	);
	
	public function __construct($key)
	{
		$this->_key = $key;
		$this->setUri(self::API_HOST);
	}
	
	public function getEmail($emailId, $headers)
	{
		$params = array();
		
		if(is_array($emailId)){
			$params['EmailID'] = implode(',', $emailId);
			$path = 'get/email/list/';
		} else {
			$params['EmailID'] = $emailId;
			$path = 'get/email/';
		}
		
		$params['GetHeaders'] = $headers;
		$response = $this->get($path, $params);
		$email = json_decode($response->getBody());
		
		if(isset($email->Emails)){
			return $email->Emails;
		}
		
		return $email;
	}
	
	public function getNewEmail($start = null, $end = null, $domain = null, $headers = null)
	{
		return $this->getEmails($start, $end, $domain, $headers, true);
	}
	
	public function getEmails($start = null, $end = null, $domain = null, $headers = null, $new = false)
	{
		$params = array();
		if(isset($start)){
			$params['Start'] = $start;	
		}
		
		if(isset($end)){
			$params['End'] = $end;
		}
		
		$params['Domain'] = $domain;
		$params['GetHeaders'] = $headers;

		if($new){
			$path = '/get/new/email/';
		} else {
			$path = 'get/all/email/';
		}
		
		$response = $this->get($path, $params);
		return json_decode($response->getBody());	
	}

	public function getAllEmail($start = null, $end = null, $domain = null, $headers = null)
	{
		return $this->getEmails($start, $end, $domain, $headers, false);
	}
	
	public function sendEmail($to, $from, $subject, $textBody, $htmlBody = null, array $headers = array())
	{
		$post['To'] = $to;
		$post['From'] = $from;
		$post['Subject'] = $subject;
		$post['TextBody'] = $textBody;
		$post['HtmlBody'] = $htmlBody;
		if(count($headers) > 0){
			foreach($headers as $name => $value)
			{
				if(!is_array($value)){
					$value = array($value);
				}
				
				foreach($value as $line){
					$header['Name'] = $name;
					$header['Value'] = $line;
					$post['Headers'][] = $header;
				}
			}
		}
		
		return $this->post('send/email/', $post);
	}

	public function deleteEmail($emailId)
	{
		$post['EmailID'] = $emailId;
		return $this->post('delete/email/', $post);
	}
	
	public function registerAddress($address, $callback = null)
	{
		$post['Address'] = $address;
		if(!is_null($callback)){
			$post['CallbackURL'] = $callback;
		}
		
		return $this->post('register/address/', $post);
	}

	public function registerDomain($domain, $callback = null)
	{
		$post['Domain'] = $domain;
		if(!is_null($callback)){
			$post['CallbackURL'] = $callback;
		}
		
		return $this->post('register/domain/', $post);
	}
	
	public function get($path, $params)
	{
		$response = $this->restGet($this->_assemblePath($path), $params);
		
		if($response->getStatus() != 200){
			$this->_badResponse($response);
		}
		
		return $response;
	}
	
	public function post($path, $params)
	{
		$response = $this->restPost($this->_assemblePath($path), json_encode($params));

		if($response->getStatus() != 200){
			$this->_badResponse($response);
		}

		return $response->getBody();
	}
	
	protected function _badResponse($response)
	{
		$message = 'Unknown status code.';
		$code = $response->getStatus();
		if(isset($this->_errors[$code])){
			$message = $this->_errors[$code];
		}
		require_once 'EmailYak/Exception.php';
		throw new EmailYak_Exception($message, $code);		

	}
	
	//only way to set the content-type since _prepareRest is final and clears the http client's parameters
	protected function _performPost($method, $data = null)
	{
		if (is_string($data)) {
			self::getHttpClient()->setHeaders('Content-Type', 'application/json');
		}
		return parent::_performPost($method, $data);
    }
	
	protected function _assemblePath($path)
	{
		return '/' . $this->_version . '/' . $this->_key . '/' . $this->_format . '/' . $path;
	}
}