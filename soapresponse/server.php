<?php require_once($_SERVER['DOCUMENT_ROOT'].'/wp-blog-header.php');
class SOAPFunctionsManager
{

	function CallServerFunction($arg)
	{
		try
		{
			global $wpdb;
			$delete = "DELETE FROM `".$wpdb->prefix."posts` WHERE `forpublic` = 1";
			$wpdb->query($delete);

			$options["connection_timeout"] = 25;
			$options['trace'] 	= 1;
			$options['cache_wsdl'] 	= 0;
			
 			$url        = $arg[0].'wp-content/plugins/forpublicpost/soaprequest/client.php?wsdl';
			$client     = new SoapClient($url, $options);
			$response   = $client->CallClientFunction();
			$countResponse = count($response);
			for($i = 0; $i < $countResponse; $i++)
			{
				// Create post object
				$new_post = array();
				$new_post['post_title']   = $response[$i]->post_title;
				$new_post['post_content'] = $response[$i]->post_content;
				$new_post['post_status']  = $response[$i]->post_status;
				$new_post['post_author']  = $response[$i]->post_author;
				// Insert the post into the database
				$post_id[] = wp_insert_post($new_post);
				$update = "UPDATE `".$wpdb->prefix."posts` SET `forpublic` = '1' WHERE `ID` = ".$post_id;
				$wpdb->query($update);

			}
			
		}
		catch(SoapFault $e)
		{  
			echo "<pre>";
			print_r($e); 
		}
		return $post_id;
	}
}

if(!extension_loaded("soap")){ dl("php_soap.dll");}
ini_set("soap.wsdl_cache_enabled","0");
$server = new SoapServer("server.wsdl");
$server->setClass("SOAPFunctionsManager");
$server->handle();

?>
