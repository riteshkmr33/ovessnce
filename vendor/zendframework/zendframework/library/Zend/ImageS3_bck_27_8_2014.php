<?php
/*
 * 
 * Form Amazon image upload 
 * Added on 1-may-2014 
 * 
 */
 
namespace Zend;

use Aws\S3\S3Client;

class ImageS3
{
	private $bucket = 'ovessence';
	private $accessKey = 'AKIAIZUDXC5GQL3SFESA';
	private $secretKey = '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';
	private $tempFolder;
	public $uploader;
	public $allowedExt = array('png', 'jpg', 'jpeg', 'gif');
	
	public function __construct($bucket='')
	{
		
		$this->tempFolder = './public/temp/';
		($bucket != "")?$this->bucket = $bucket:"";
		$this->client = S3Client::factory(array('key'=> $this->accessKey,'secret' => $this->secretKey));
		
	}
	
	// Function to upload files on S3
	public function uploadFiles($file,$folder="",array $paths, $sizes = array(), $perm='public-read')
	{
		
		$data = array();
		$fileNameParts = explode(".",$file['name']);
		$ext = end($fileNameParts);
		$fileName = time().".".$ext;
		
		if (in_array($ext,$this->allowedExt)) {
			
			if (count($paths)>0) {
				
				foreach ($paths as $key=>$value) {
					$filePath = $this->tempFolder.$fileName;
					
					// Resize image
					 
					$hostname = $_SERVER['SERVER_NAME'];
					
					if ($hostname == 'ovessence.com') {
						exec("/usr/bin/convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
					} else if ($hostname == 'dev.clavax.us') {
						exec("/usr/local/bin/convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
					} else {
						exec("convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
					}
					
					// Upload file to S3 server
					$result = $this->client->putObject(array(
									'Bucket' => $this->bucket,
									'Key'    => $folder."/".$key."/".$fileName,
									//'SourceFile' => $filePath,
									//'SourceFile' => $file['tmp_name'],
									'SourceFile' => $filePath,
									'ACL'    => $perm
								));
						
					$data[$key] = $result['ObjectURL'];
					 
					// Delete temperary file			
					if (is_file($filePath)) {
						@unlink($filePath);
					}
					
				}
			} else {
				
				if (count($sizes) > 0) {
					
					foreach ($sizes as $key=>$value) {
						$filePath = $this->tempFolder.$fileName;
						
						// Resize image
						 
						$hostname = $_SERVER['SERVER_NAME'];
						
						if ($hostname == 'ovessence.com') {
							exec("/usr/bin/convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
						} else if ($hostname == 'dev.clavax.us') {
							exec("/usr/local/bin/convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
						} else {
							exec("convert " . $file['tmp_name'] . " -resize " . $value . "%  $filePath");
						}
						
						// Upload file to S3 server
						$result = $this->client->putObject(array(
										'Bucket' => $this->bucket,
										'Key'    => $key."/".$fileName,
										//'SourceFile' => $filePath,
										//'SourceFile' => $file['tmp_name'],
										'SourceFile' => $filePath,
										'ACL'    => $perm
									));
							
						$data[$key] = $result['ObjectURL'];
						
						// Delete temperary file			
						if (is_file($filePath)) {
							@unlink($filePath);
						}
						
					}
					
				} else {
					// Upload file to S3 server
					$result = $this->client->putObject(array(
										'Bucket' => $this->bucket,
										'Key'    => $folder."/".$fileName,
										'SourceFile' => $file['tmp_name'],
										'ACL'    => $perm
									));
									
					$data['Original'] = $result['ObjectURL'];
					
					// Delete temperary file			
					if (is_file($file['tmp_name'])) {
						@unlink($file['tmp_name']);
					}
				}

			}
			
			return $data;
		}
		
		return false;
		
	}
	
	public function uploadFile($file, $sizes = array(), $perm='public-read')
	{
		if (count($sizes) > 0) {
			$fileParts = explode("/",$file);
			$fileNameParts = explode(".",end($fileParts));
			$ext = end($fileNameParts);
			$fileName = time().".".$ext;
			$data = array();
			$hostname = $_SERVER['SERVER_NAME'];
		
			foreach ($sizes as $folder => $size) {
				
				if ($hostname == 'ovessence.com') {
					exec("/usr/bin/convert " . $file . " -resize " . $value . "%  $filePath");
				} else if ($hostname == 'dev.clavax.us') {
					exec("/usr/local/bin/convert " . $file . " -resize " . $value . "%  $filePath");
				} else {
					exec("convert " . $file . " -resize " . $value . "%  $filePath");
				}
				
				// Upload file to S3 server
				$result = $this->client->putObject(array(
								'Bucket' => $this->bucket,
								'Key'    => $folder."/".$fileName,
								'SourceFile' => $file,
								'ACL'    => $perm
							));
				$data[$folder] = $result['ObjectURL'];
				
				@unlink($file);  //  delete file from local server
			}
			
			return $data;
		}
	}
	
	// Function to get list of files from S3 server
	public function fetchFiles($folder)
	{
		$result = $this->client->listObjects(array('Bucket' => $this->bucket, 'Prefix'=>$folder));
		return $result['Contents'];
	}
	
	// Function to delete file from S3 server
	public function deleteFile($file)
	{
		$result = $this->client->deleteObject(array('Bucket' => $this->bucket,'Key' => $file));
		return $result;
	}
	
	// Function to copy file on S3 server
	public function copyFile($from,$to)
	{
		$result = $this->client->copyObject(array(
							'Bucket'     => $this->bucket,
							'Key'        => $from,
							'CopySource' => $this->bucket."/".$to,
						));
		return $result;
	}
}
