<?php
/**
 * HTTP class using sockets. (No cURL)
 * @author noother
 */
class GWF_HTTPSock
{
	private $socket = false;
	private $ip     = false;
	private $host;
	private $cookies = array();

	private $settings = array(
		'auto-follow'         => true,
		'accept-cookies'      => true,
		'unstable-connection' => false
	);
	
	private $lastHeader = false;
	
	public function __construct($host)
	{
		$this->host = $host;
	}
	
	public function GET($path)
	{
		return $this->_getResponse('GET', $path);
	}
	
	public function POST($path, $post, $urlencoded=false)
	{
		$header = "Content-Type: application/x-www-form-urlencoded\r\n";
		
		$post_string = '';
		foreach($post as $name => $value)
		{
			$post_string.= ($urlencoded?$name:urlencode($name)).'='.($urlencoded?$value:urlencode($value)).'&';
		}
		$post_string = substr($post_string, 0, -1);
		
		$header.= "Content-Length: ".strlen($post_string)."\r\n\r\n";
		$header.= $post_string;
		
		return $this->_getResponse('POST', $path, $header);
	}
	
	public function set($setting, $value)
	{
		if(!isset($this->settings[$setting]))
		{
			return false;
		}
		$this->settings[$setting] = $value;
	}
	
	public function setCookie($name, $value, $urlencoded=false)
	{
		$this->cookies[$name] = $urlencoded ? $value : urlencode($value);
	}
	
	public function setCookies($array, $urlencoded=false)
	{
		foreach($array as $name => $value)
		{
			$this->setCookie($name, $value, $urlencoded);
		}
	}
	
	public function clearCookies()
	{
		$this->cookies = array();
	}
	
	
	public function getHeader()
	{
		return $this->lastHeader;
	}
	
	
	private function _connect()
	{
		if($this->ip === false)
		{
			$this->ip = gethostbyname($this->host);
		}
		
		# Close old connection
		if($this->socket)
		{
			fclose($this->socket);
		}
		
		$this->socket = @fsockopen($this->ip, 80);
		
		if(!$this->socket)
		{
			if($this->settings['unstable-connection'])
			{
				usleep(500000);
				return $this->_connect();
			}
			else
			{
				trigger_error('Could not connect to '.$this->host.' ('.$this->ip.') on port 80');
				return false;
			}
		}
		return true;
	}
	
	private function _getResponse($method, $path, $additional_headers=null)
	{
		if($this->socket === false || feof($this->socket))
		{
			if(!$this->_connect())
			{
				return false;
			}
		}
		
		$header = $method.' '.$path." HTTP/1.1\r\n";
		$header.= "Host: ".$this->host."\r\n";
		
		if(!empty($this->cookies))
		{
			$cookie_string = 'Cookie: ';
			foreach($this->cookies as $name => $value)
			{
				$cookie_string.= $name.'='.$value.';';
			}
			$cookie_string = substr($cookie_string, 0, -1);
			$header.= $cookie_string."\r\n";
		}
		
		$header.= "Connection: Keep-Alive\r\n";
		
		if(isset($additional_headers))
		{
			$header.= $additional_headers;
		}
		
		$header.= "\r\n";
		fputs($this->socket, $header);
		
		list($header, $content_length) = $this->_getHeader();
		$body = $this->_getBody(
			$content_length,
			isset($header['Transfer-Encoding']) && $header['Transfer-Encoding'] == 'chunked' ? true : false
		);
		$this->lastHeader = $header;
		
		if($this->settings['auto-follow'] && isset($header['Location']))
		{
			$parts = parse_url($header['Location']);
			if($parts['host'] != $this->host)
			{
				$this->host = $parts['host'];
				$this->clearCookies();
				
				$new_ip = gethostbyname($parts['host']);
				if($new_ip != $this->ip)
				{
					fclose($this->socket);
					$this->socket = fsockopen($new_ip, 80);
				}
			}
			return $this->_getResponse($method, $parts['path'], $additional_headers);
		}
		
		if($this->settings['accept-cookies'] && !empty($header['cookies']))
		{
			$this->setCookies($header['cookies'], true);
		}
		
		return $body;
	}
	
	private function _getHeader()
	{
		$header = array();
		$header['cookies'] = array();
		
		for($c=0; '' != $line = trim(fgets($this->socket)); $c++)
		{
			if(!$c)
			{
				preg_match('#^HTTP/1\.1 (\d+?) ([\w ]+?)$#', $line, $arr);
				$header['status_code'] = (int)$arr[1];
				$header['status']      = $arr[2];
			}
			else
			{
				$tmp = explode(': ', $line, 2);
				if($tmp[0] == 'Set-Cookie')
				{
					preg_match('#^(.+?)=(.+?)(:?;|$)#', $tmp[1], $arr);
					$header['cookies'][$arr[1]] = $arr[2];
				}
				else
				{
					$header[$tmp[0]] = $tmp[1];
				}
			}
		}
		
		if(isset($header['Transfer-Encoding']) && $header['Transfer-Encoding'] == 'chunked')
		{
			$line = trim(fgets($this->socket));
			preg_match('#^([0-9a-f]+)#', $line, $arr);
			$content_length = hexdec($arr[1]);
		}
		else
		{
			$content_length = $header['Content-Length'];
		}
		
		return array($header, $content_length);
	}
	
	private function _getBody($content_length, $chunked)
	{
		$body = '';
		$received = 0;
		while($received < $content_length)
		{
			$part = fread($this->socket, $content_length-$received);
			$received+= strlen($part);
			$body.= $part;
		}
		
		if($chunked)
		{
			fgets($this->socket); // get the \r\n
			
			$next_length = hexdec(trim(fgets($this->socket)));
			if($next_length)
			{
				return $body.$this->_getBody($next_length, true);
			}
			else
			{
				do
				{
					$line = fgets($this->socket);
				}
				while($line !== "\r\n");
				
				return $body;
			}
		}
		else
		{
			return $body;
		}
	}
}
?>