<?php


namespace FintechFab\BankEmulator\Components\Helpers;


class Curl
{


	private $curl;
	public $result;
	public $code;
	public $error;

	/**
	 * содержимое страницы по url
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return string
	 */
	public function post($url, $data)
	{
		$this->init($url);
		$this->setPost($data);
		$this->exec();
		$this->fin();
	}


	private function init($url)
	{
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5);

		if (strpos($url, 'https') !== false) {
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		}


	}


	private function fin()
	{
		curl_close($this->curl);
	}

	private function exec()
	{
		$this->result = curl_exec($this->curl);
		$this->error = curl_error($this->curl);
		$info = curl_getinfo($this->curl);
		$this->code = $info['http_code'];
	}

	private function setPost($data)
	{
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
	}

	public function ok()
	{
		return $this->code == '200';
	}


} 