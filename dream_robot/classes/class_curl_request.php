<?php
/**
 * Diese Klasse bietet einen Curl Request
 *
 * Nachdem Das Curl_request-Objekt erzeugt wurde, können die Standardoptionen über get_options() abgerufen werden und/oder über set_options() verändert werden um das
 * Objekt individuell für den jeweiligen Einsatzzweck anzupassen.
 * Hinweis: Diese Klasse kann nur mit UTF8-Daten arbeiten (siehe $dataset in $this->request(...)). json_encode() und Array2xml/Xml2array funktionieren sonst nicht.
 *
 * @version 24.07.2015 Niklas Schwan: Klasse erstellt.
 */
Class Curl_request
{
	/** @var bool Im Debug-Modus werden auch erfolgreiche Anfragen geloggt. */
	private $debug;

	/** Die KlassenOptionen für den Curl-Request */
	private $options = array();

	/** Curl-Objekt */
	private $curl;

	/**
	 * @param bool $debug Logdatei auch für erfolgreiche Anfragen anlegen?
	 */
	function __construct($debug = false)
	{
		$this->debug	= $debug;
		$this->curl		= curl_init();

		//Die Defaultoptionen setzen.
		$this->options = array(
			'verifypeer'	=> FALSE,	//bool		Bei einer SSL-Anfrage CURLOPT_SSL_VERIFYPEER auf "1" setzen.
			'verifyhost'	=> TRUE,	//bool		Bei einer SSL-Anfrage CURLOPT_SSL_VERIFYHOST auf "1" setzen.
			'json'			=> TRUE,	//bool		Bei einem POST(oder PATCH, etc.) -Request werden die Daten als JSON enkodiert; Die Rückgabe wird, wenn möglich, dekodiert.
			'timeout'		=> 30,		//int		Der Timeout in Sekunden nachdem die Anfrage abgebrochen wird, wenn keine Antwort erfolgt ist.
			'return_header'	=> FALSE,	//bool		Die Antwortheader ins 'header'-Element schreiben.
			'user_pwd'		=> '',		//string	Die Option CURLOPT_USERPWD auf diesen Wert setzen.
			//06.04.2016 NS: Cookie eingefügt.
			'cookie'		=> '',
			//15.06.2016 NS: XML hinzugefügt.
			'xml'			=> FALSE,	//string	Bei einem POST(oder PATCH, etc.) -Request werden die Daten als XML enkodiert; Die Rückgabe wird, wenn möglich, dekodiert.
		);

		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
	}

	/**
	 * Führt den Request aus.
	 * @version 24.07.2015 NS: Erstellt
	 *
	 * @param string	$url				Die Zieladresse beginnend mit "http" oder "https"; entsprechend wird SSL verwendet oder nicht.
	 * @param mixed		$dataset			Die Postdaten, entweder als fertiger POST-String oder als Array(für 'GET'-Anfragen nur als Array oder direkt in der URL).
	 * @param string	$request_type		z.B.: 'POST', 'GET', 'DELETE', 'OPTIONS', etc.; funktional wird alles außer 'GET' als post übertragen
	 * @param array		$header				Die zu setzenden Header als Array.
	 *
	 * @return array	'content'   string	enthält die Rückgabe.<br/>
	 *               	'code'		int		enthält den HTTP Code. Wenn dieser nicht mit einer 2 beginnt(kein Success), wird die Anfrage immer geloggt.<br/>
	 *               	'header'	string	wenn über options 'return_header' aktiviert wurde, stehen hier die Rückgabe-Header drin.
	 */
	function request($url, $dataset = array(), $request_type = 'POST', $header = array())
	{
		$logstring = "\r\nURL: " . $url . "\r\n" . $request_type . "-Daten: " . print_r($dataset, TRUE) . "\r\nHeader: " . print_r($header, TRUE) . "\r\nDR-Curl-Optionen: " . print_r($this->options, TRUE);

		//Requesttyp setzen.
		$request_type = strtoupper($request_type);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request_type);

		//Wenn JSON/XML und keine Get-Anfrage und Die Post-Daten liegen als Array vor:
		//JSON-Encode.
		if($this->options['json'] && $request_type !== 'GET' && is_array($dataset))
		{
			$dataset = json_encode($dataset);
		}
		//XML-Encode.
		elseif($this->options['xml'] && $request_type !== 'GET' && is_array($dataset))
		{
			$dataset = Array2Xml::createXML($this->options['xml'], $dataset)->saveXML();
		}

		//Header setzen
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);

		//Daten und URL einsetzen.
		if($request_type == 'GET')
		{
			if(is_array($dataset) && $dataset !== array())
			{
				$url .= (strpos($url, '?') ? '&' : '?') . http_build_query($dataset);
			}
		}
		else
		{
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataset);
		}

		//URL setzen.
		curl_setopt($this->curl, CURLOPT_URL, $url);

		//Anfrage ausführen
		$answer = curl_exec($this->curl);

		$array_answer = array();

		//Wenn RETURN_HEADER -> TRUE, Den Header aus $answer rausziehen.
		if($this->options['return_header'])
		{
			list($array_answer['header'], $answer) = explode("\r\n\r\n", $answer, 2);
		}

		//Wenn	JSON -> FALSE/Decode fehlschlägt;
		//und	XML  -> FALSE/Decode fehlschlägt;
		//Den Vanilla-Antwortstring ohne JSON-Decode einsetzen.
		if(!(
				$this->options['json'] && ($array_answer['content'] = json_decode($answer, TRUE))
			|| 	$this->options['xml'] && ($array_answer['content'] = $this->try_xml_decode($answer))
		))
		{
			$array_answer['content'] = $answer;
		}

		//Den HTTP-Code eintragen.
		$array_answer['code'] = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

		//Log schreiben.
		if(substr($array_answer['code'], 0, 1) != 2)
		{
			$this->write_curl_error('HTTP-Error: ' . $array_answer['code'] . $logstring . "\r\nAntwort: " . print_r($array_answer['content'], TRUE) . "\r\n\r\n");
		}
		elseif($this->debug)
		{
			$this->write_curl_error('HTTP-Code: ' . $array_answer['code'] . $logstring . "\r\nAntwort: " . print_r($array_answer['content'], TRUE) . "\r\n\r\n");
		}

		return $array_answer;
	}

	/**
	 * Liefert das Array mit den Anfrageoptionen.
	 * @version 18.08.2015 NS: Erstellt
	 *
	 * @return array Das Array mit den Anfrageoptionen.
	 */
	function get_options()
	{
		return $this->options;
	}

	/**
	 * Setzt die Anfrageoptionen für künftige Anfragen über dieses Objekt.
	 * @version 18.08.2015 NS: Erstellt
	 *
	 * @param array $options
	 * @return void
	 */
	function set_options($options)
	{
		foreach($options as $option => &$value)
		{
			if(array_key_exists($option, $this->options) && $value !== $this->options[$option])
			{
				$this->options[$option] = $value;

				switch ($option)
				{
					case 'verifypeer':
						curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $value);
						break;
					case 'verifyhost':
						curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, $value);
						break;
					case 'timeout':
						curl_setopt($this->curl, CURLOPT_TIMEOUT, $value);
						break;
					case 'return_header':
						curl_setopt($this->curl, CURLOPT_HEADER, $value);
						break;
					case 'user_pwd':
						curl_setopt($this->curl, CURLOPT_USERPWD, $value);
						break;
					case 'cookie':
						curl_setopt($this->curl, CURLOPT_COOKIE, $value);
						break;
					case 'xml':
						include_once(__DIR__ . '/class_array2XML.php');
						include_once(__DIR__ . '/class_XML2array.php');
						break;
				}
			}
		}
	}

	/**
	 * Hier werden die Curl-Anfragen geloggt.
	 * @version 24.07.2015 NS: Erstellt
	 *
	 * @param string	$text
	 * @return void
	 */
	function write_curl_error($text)
	{
		$filename = __DIR__ . '/../curl_log_'.date('Y-m-d').'.txt';
		$fd = fopen($filename, 'a');
		fwrite($fd, date('H:i:s') . ' | ' . $text . "\r\n");
	}

	private function try_xml_decode($xml)
	{
		try{
			return XML2Array::createArray($xml);
		} catch(Exception $e) {
			return FALSE;
		}
	}
}