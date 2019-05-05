<?php

    //////////////////////////////////////////////////
    //
    // CONSTANTS
    //
    /////////////////////////////////////////////////

    unset($constants);
    $constants['region'] = 'us-west-1';
    $constants['sdk_version'] = 'latest';
    $constants['object_timeout'] = '+5 minutes';  

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    $bucket = getenv('S3_BUCKET', true) ?: getenv('S3_BUCKET');
    if(!empty($bucket)){
        require('/app/vendor/autoload.php');

        // this will simply read AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY from env vars
        $client = new S3Client([
            'version'  => SDK_VERSION,
            'region'   => REGION,
        ]);
    }

    /*
        Return TRUE if Amazon S3 environment detected else FALSE;
     */
    function is_S3(){
        global $bucket;
        return !empty($bucket);
    }


    /* 
       AWS S3 
       SOURCE: https://docs.aws.amazon.com/aws-sdk-php/v2/guide/service-s3.html
     */ 

    // 
    // KEYS:                      // EXAMPLE: echo $result[key];
    // 'Expiration'
    // 'ServerSideEncryption'
    // 'ETag'
    // 'VersionId'
    // 'RequestId'
    // 'ObjectURL'
    function S3_upload_file($local_fullpath_source, $remote_fullpath_destination){
        global $bucket;
        global $client;

        try{
            // Upload an object by streaming the contents of a file
            // $pathToFile should be absolute path to a file on disk
            $result = $client->putObject(array(
                'Bucket'     => $bucket,
                'Key'        => $remote_fullpath_destination,
                'SourceFile' => $local_fullpath_source,
                'ACL'        => 'public-read',
            ));
        } catch(Exception $e) {
            echo $e->getResponse();
            $result = "";
        }

        return $result;
    }

    function S3_get_temp_file_url($remote_fullpath_destination){
        global $bucket;
        global $client;
        $key = $remote_fullpath_destination;
      
        try{
            // Get a pre-signed URL for an Amazon S3 object valid for OBJECT_TIMEOUT minutes
            // > https://my-bucket.s3.amazonaws.com/data.txt?AWSAccessKeyId=[...]&Expires=[...]&Signature=[...]
            //$request = $client->getObjectUrl($bucket, $key);
            $request = $client->get("{$bucket}/{$key}"); // get() returns a Guzzle\Http\Message\Request object
            $signedUrl = $client->createPresignedUrl($request, OBJECT_TIMEOUT);
        } catch(Exception $e) {
            echo "ERROR: Unable to get file from S3: " . $e->getResponse();
            $signedUrl = "";
        }

        return $signedUrl;
    }
?>