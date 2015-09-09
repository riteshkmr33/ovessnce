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
    
    // These are the same constants so this script should be upgrade safe, the values are different no doubt, but that won't hurt!
    private $IMG_FLIP_HORIZONTAL = 1;
    private $IMG_FLIP_VERTICAL = 2;
    private $IMG_FLIP_BOTH = 3;

    public function __construct($bucket = '')
    {

        $this->tempFolder = './public/temp/';
        ($bucket != "") ? $this->bucket = $bucket : "";
        $this->client = S3Client::factory(array('key' => $this->accessKey, 'secret' => $this->secretKey));
    }

    // Function to upload files on S3
    public function uploadFiles($file, $folder = "", array $paths, $sizes = array(), $perm = 'public-read')
    {
        if ($this->fix_orientation($file)) {
            $tempFile = $this->tempFolder.$file['name'];
        } else {
            $tempFile = $file['tmp_name'];
        }
        
        $data = array();
        $fileNameParts = explode(".", $file['name']);
        $ext = end($fileNameParts);
        $fileName = time() . "." . $ext;

        if (in_array(strtolower($ext), $this->allowedExt)) {

            if (count($paths) > 0) {

                foreach ($paths as $key => $value) {
                    $filePath = $this->tempFolder . $fileName;

                    // Resize image

                    $hostname = $_SERVER['SERVER_NAME'];

                    if ($hostname == 'ovessence.com') {
                        exec("/usr/bin/convert " . $tempFile . " -resize " . $value . "%  $filePath");
                    } else if ($hostname == 'dev.clavax.us') {
                        exec("/usr/local/bin/convert " . $tempFile . " -resize " . $value . "%  $filePath");
                    } else {
                        exec("convert " . $tempFile . " -resize " . $value . "%  $filePath");
                    }

                    // Upload file to S3 server
                    $result = $this->client->putObject(array(
                        'Bucket' => $this->bucket,
                        'Key' => $folder . "/" . $key . "/" . $fileName,
                        //'SourceFile' => $filePath,
                        //'SourceFile' => $file['tmp_name'],
                        'SourceFile' => $filePath,
                        'ACL' => $perm
                    ));

                    $data[$key] = $result['ObjectURL'];

                    // Delete temperary file			
                    (is_file($filePath))?@unlink($filePath):'';
                }
            } else {

                if (count($sizes) > 0) {

                    foreach ($sizes as $key => $value) {
                        $filePath = $this->tempFolder . $fileName;

                        // Resize image
                        $resizeTo = (is_numeric($value)) ? $value . '%' : $value;
                        $hostname = $_SERVER['SERVER_NAME'];

                        if ($hostname == 'ovessence.com') {
                            exec("/usr/bin/convert " . $tempFile . " -resize " . $resizeTo . "  $filePath");
                        } else if ($hostname == 'dev.clavax.us') {
                            exec("/usr/local/bin/convert " . $tempFile . " -resize " . $resizeTo . "  $filePath");
                        } else {
                            exec("convert " . $tempFile . " -resize " . $resizeTo . "  $filePath");
                        }

                        // Upload file to S3 server
                        $result = $this->client->putObject(array(
                            'Bucket' => $this->bucket,
                            'Key' => $key . "/" . $fileName,
                            //'SourceFile' => $filePath,
                            //'SourceFile' => $file['tmp_name'],
                            'SourceFile' => $filePath,
                            'ACL' => $perm
                        ));

                        $data[$key] = $result['ObjectURL'];

                        // Delete temperary file			
                        (is_file($filePath))?@unlink($filePath):'';
                        
                        
                    }
                } else {
                    // Upload file to S3 server
                    $result = $this->client->putObject(array(
                        'Bucket' => $this->bucket,
                        'Key' => $folder . "/" . $fileName,
                        'SourceFile' => $tempFile,
                        'ACL' => $perm
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

    public function uploadFile($file, $sizes = array(), $perm = 'public-read')
    {
        if (count($sizes) > 0) {
            $fileParts = explode("/", $file);
            $fileNameParts = explode(".", end($fileParts));
            $ext = end($fileNameParts);
            $fileName = time() . "." . $ext;
            $data = array();
            $hostname = $_SERVER['SERVER_NAME'];
            $filePath = $this->tempFolder . $fileName;

            foreach ($sizes as $folder => $size) {

                if ($hostname == 'ovessence.com') {
                    exec("/usr/bin/convert " . $file . " -resize " . $size . "  $filePath");
                } else if ($hostname == 'dev.clavax.us') {
                    exec("/usr/local/bin/convert " . $file . " -resize " . $size . "  $filePath");
                } else {
                    exec("convert " . $file . " -resize " . $size . "  $filePath");
                }

                // Upload file to S3 server
                $result = $this->client->putObject(array(
                    'Bucket' => $this->bucket,
                    'Key' => $folder . "/" . $fileName,
                    'SourceFile' => $filePath,
                    'ACL' => $perm
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
        $result = $this->client->listObjects(array('Bucket' => $this->bucket, 'Prefix' => $folder));
        return $result['Contents'];
    }

    // Function to delete file from S3 server
    public function deleteFile($file)
    {
        $result = $this->client->deleteObject(array('Bucket' => $this->bucket, 'Key' => $file));
        return $result;
    }

    // Function to copy file on S3 server
    public function copyFile($from, $to)
    {
        $result = $this->client->copyObject(array(
            'Bucket' => $this->bucket,
            'Key' => $from,
            'CopySource' => $this->bucket . "/" . $to,
        ));
        return $result;
    }

    public function fix_orientation($file)
    {
        $fileandpath = $file['tmp_name'];
        // Does the file exist to start with?
        if (!file_exists($fileandpath))
            return false;

        // Get all the exif data from the file
        $exif = read_exif_data($fileandpath, 'IFD0');

        // If we dont get any exif data at all, then we may as well stop now
        if (!$exif || !is_array($exif))
            return false;

        // I hate case juggling, so we're using loweercase throughout just in case
        $exif = array_change_key_case($exif, CASE_LOWER);

        // If theres no orientation key, then we can give up, the camera hasn't told us the 
        // orientation of itself when taking the image, and i'm not writing a script to guess at it!
        if (!array_key_exists('orientation', $exif))
            return false;

        // Gets the GD image resource for loaded image
        $img_res = $this->get_image_resource($file);
        
        // If it failed to load a resource, give up
        if (is_null($img_res))
            return false;

        // The meat of the script really, the orientation is supplied as an integer, 
        // so we just rotate/flip it back to the correct orientation based on what we 
        // are told it currently is 
        
        switch ($exif['orientation']) {

            // Standard/Normal Orientation (no need to do anything, we'll return true as in theory, it was successful)
            case 1: return false;
                break;

            // Correct orientation, but flipped on the horizontal axis (might do it at some point in the future)
            case 2:
                $final_img = $this->imageflip($img_res, $this->IMG_FLIP_HORIZONTAL);
                break;

            // Upside-Down
            case 3:
                $final_img = $this->imageflip($img_res, $this->IMG_FLIP_VERTICAL);
                break;

            // Upside-Down & Flipped along horizontal axis
            case 4:
                $final_img = $this->imageflip($img_res, $this->IMG_FLIP_BOTH);
                break;

            // Turned 90 deg to the left and flipped
            case 5:
                $final_img = imagerotate($img_res, -90, 0);
                $final_img = $this->imageflip($img_res, $this->IMG_FLIP_HORIZONTAL);
                break;

            // Turned 90 deg to the left
            case 6:
                $final_img = imagerotate($img_res, -90, 0);
                break;

            // Turned 90 deg to the right and flipped
            case 7:
                $final_img = imagerotate($img_res, 90, 0);
                $final_img = $this->imageflip($img_res, $this->IMG_FLIP_HORIZONTAL);
                break;

            // Turned 90 deg to the right
            case 8:
                $final_img = imagerotate($img_res, 90, 0);
                break;
        }

        // If theres no final image resource to output for whatever reason, give up
        if (!isset($final_img))
            return false;

        //-- rename original (very ugly, could probably be rewritten, but i can't be arsed at this stage)
        /*$parts = explode("/", $fileandpath);
        $oldname = array_pop($parts);
        $path = implode('/', $parts);
        $oldname_parts = explode(".", $oldname);
        $ext = array_pop($oldname_parts);
        $newname = implode('.', $oldname_parts) . '.orig.' . $ext;

        rename($fileandpath, $path . '/' . $newname);*/

        // Save it and the return the result (true or false)
        $done = $this->save_image_resource($final_img, $this->tempFolder.$file['name']);

        return $done;
    }

    /**
     * Simple function which takes the filepath, grabs the extension and returns the GD resource for it
     * Not fool-proof nor the best, but it does the job for now 
     */
    private function get_image_resource($file)
    {

        $img = null;
        $p = explode(".", strtolower($file['name']));
        $ext = array_pop($p);
        switch ($ext) {

            case "png":
                $img = imagecreatefrompng($file['tmp_name']);
                break;

            case "jpg":
            case "jpeg":
                $img = imagecreatefromjpeg($file['tmp_name']);
                break;
            case "gif":
                $img = imagecreatefromgif($file['tmp_name']);
                break;
        }

        return $img;
    }

    /**
     * Saves the final image resource to the given location
     * As above it works out the extension and bases its output command on that, not fool proof, but works nonetheless
     */
    private function save_image_resource($resource, $location)
    {

        $success = false;
        $p = explode(".", strtolower($location));
        $ext = array_pop($p);
        switch ($ext) {

            case "png":
                $success = imagepng($resource, $location);
                break;

            case "jpg":
            case "jpeg":
                $success = imagejpeg($resource, $location);
                break;
            case "gif":
                $success = imagegif($resource, $location);
                break;
        }

        return $success;
    }

    /**
     * Simple function that takes a gd image resource and the flip mode, and uses rotate 180 instead to do the same thing... Simples!
     */
    private function imageflip($resource, $mode)
    {

        if ($mode == $this->IMG_FLIP_VERTICAL || $mode == $this->IMG_FLIP_BOTH)
            $resource = imagerotate($resource, 180, 0);

        if ($mode == $this->IMG_FLIP_HORIZONTAL || $mode == $this->IMG_FLIP_BOTH)
            $resource = imagerotate($resource, 90, 0);

        return $resource;
    }

}
