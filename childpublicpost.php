<?php
/*
Plugin Name: Child Public Post
Plugin URI: http://www.cisin.com/
Description: This is the chilf plugin of For Public Post for all child sites.
Version: 1.0
Author: CIS Team	
Author URI: http://www.cisin.com
*/

function addChildPublicPost() {

//-- create server.wsdl

global $wpdb;
$SiteUrl = get_option('siteurl');

$wsdl = "<?xml version='1.0'?>
<definitions name='ServerWsdl' targetNamespace='urn:ServerWsdl' xmlns:tns='urn:ServerWsdl'  xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/' xmlns:soapenc='http://schemas.xmlsoap.org/soap/encoding/' xmlns:wsdl='http://schemas.xmlsoap.org/wsdl/' xmlns='http://schemas.xmlsoap.org/wsdl/'>";

$wsdl .= "<message name='getCallServerFunctionRequest'>
		<part name='myelement2' type='xsd:array'/>
	</message>
	<message name='getCallServerFunctionResponse'>
		<part name='Result' type='xsd:array'/>		
	</message>";

$wsdl .= "<portType name='orderPortType'>
		<operation name='CallServerFunction'>
			<input message='tns:getCallServerFunctionRequest'/>
			<output message='tns:getCallServerFunctionResponse'/>
		</operation>
	</portType>";
$wsdl .= "<binding name='orderBinding' type='tns:orderPortType'>
		<soap:binding style='rpc' transport='http://schemas.xmlsoap.org/soap/http'/>
			<operation name='CallServerFunction'>
			<soap:operation soapAction='urn:CallServerFunction'/>
				<input>
					<soap:body use='encoded' namespace='urn:CallServerFunction' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
				</input>
				<output>
					<soap:body use='encoded' namespace='urn:CallServerFunction' encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
				</output>
			</operation>
	</binding>";

$wsdl .= "<service name='orderService'>
		<port name='StockQuotePort' binding='tns:orderBinding'>
			<soap:address location='".$SiteUrl."/wp-content/plugins/childpublicpost/soapresponse/server.php' />
		</port>
	</service>
</definitions>";

	$path = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/childpublicpost/soapresponse/';
	$serverFile = $path."server.wsdl";
	if(!file_exists($serverFile))
	{
		$handle = fopen($serverFile, "a+");
		fwrite($handle, $wsdl);
		$contents = fread($handle, filesize($serverFile));
	}
	fclose($handle);     

	$table = $wpdb->prefix."posts";
	$alterTable = "ALTER TABLE  `".$table."` ADD  `forpublic` INT NOT NULL";
	$wpdb->query($alterTable);  
}

function removeChildPublicPost(){
	
	global $wpdb;
	$table = $wpdb->prefix ."posts";
	$structure = "ALTER TABLE `".$table."` DROP `forpublic`";
	$wpdb->query($structure);  
}
	/* Hook Plugin */
	register_activation_hook(__FILE__,'addChildPublicPost');
	register_deactivation_hook(__FILE__ , 'removeChildPublicPost' );

?>
