<?php
if (!defined('CC_INI_SET')) die("Access Denied");

/*
#
#	Info for API interaction can be found here:
#	http://sellonline.canadapost.ca/DevelopersResources/protocolV3/index.html
#
#	Timestamp [10/20/2010 4:27:31 PM] -- Sir William
#
*/

class Canada_Post {

	var $cpModule;
	var $cpPostageData;
	var $cpURL		= 'http://sellonline.canadapost.ca:30000/';

	function debug($error){

		$this->cpModule = $module;
		if($this->cpModule['debug']==1) { echo $error; exit; }

	}

	function Canada_Post($module, $postageData) {
		global $config;
		$this->cpModule = $module;
		$this->cpPostageData = $postageData;
		$this->cpLocale = ($config['defaultLang'] == 'fr') ? 'fr' : 'en';
	}

	function request() {
		## Generate the XML for the request
		$request_xml[] = '<eparcel>';
		$request_xml[] = '<language>'.$this->cpLocale.'</language>';
		$request_xml[] = '<ratesAndServicesRequest>';

		$request_xml[] = '<merchantCPCID>'.$this->cpModule['merchant'].'</merchantCPCID>';
		$request_xml[] = '<fromPostalCode>'.$this->cpModule['postcode'].'</fromPostalCode>';
		$request_xml[] = '<itemsPrice>'.$this->cpPostageData['subTotal'].'</itemsPrice>';

		$request_xml[] = '<lineItems>';

		$request_xml[] = '<item>';

		$request_xml[] = '<quantity>1</quantity>';
		$request_xml[] = '<weight>'.$this->cpPostageData['totalWeight'].'</weight>';
		$request_xml[] = '<length>'.$this->cpModule['length'].'</length>';
		$request_xml[] = '<width>'.$this->cpModule['width'].'</width>';
		$request_xml[] = '<height>'.$this->cpModule['height'].'</height>';
		$request_xml[] = '<description>'.$this->cpModule['description'].' - ddsasdadsd</description>';
		$request_xml[] = '</item>';

		$request_xml[] = '</lineItems>';

		$request_xml[] = '<city>'.$this->cpPostageData['basket']['city'].'</city>';
		$request_xml[] = '<provOrState>'.$this->cpPostageData['basket']['state'].'</provOrState>';
		$request_xml[] = '<country>'.$this->cpPostageData['basket']['country'].'</country>';
		$request_xml[] = '<postalCode>'.$this->cpPostageData['basket']['postcode'].'</postalCode>';

		$request_xml[] = '</ratesAndServicesRequest>';
		$request_xml[] = '</eparcel>';

		## Send the request

		$ch = curl_init($this->cpURL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("\n", $request_xml));
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$return_xml = curl_exec($ch);

		if ($this->cpModule['debug']) {
			echo "<hr /><strong>Request XML:</strong><hr />".nl2br(htmlspecialchars(implode("\n", $request_xml)));
			echo "<hr /><strong>Return XML:</strong><hr />".nl2br(htmlspecialchars($return_xml));
			exit;
		}

		return $this->process($return_xml);
	}

	function process($xml_string) {

		$this->cpModule = $module;

		$i = 0;
		$xml = new SimpleXMLElement($xml_string);

		if (is_object($xml->ratesAndServicesResponse->product)) {
			foreach ($xml->ratesAndServicesResponse->product as $products) {
				$returnData[$i]['price']= (string)$products->rate;
				$returnData[$i]['name']	= (string)$products->name;
				++$i;
			}
			return $returnData;
		}
		if(is_object($xml->error)) {
			$this->debug("Canada Post Error: ".$xml->error->statusCode." - ".$xml->error->statusMessage);
			return false;
		}
		return false;
	}
}

?>