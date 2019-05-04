<?php

    //////////////////////////////////////////////////
    //
    // CONSTANTS
    //
    /////////////////////////////////////////////////

    $constants['region'] = 'us-west-1';
    $constants['sdk_version'] = 'latest';

    foreach($constants as $key => $value){
        define(strtoupper($key), $value);
    }

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    $bucket = getenv('S3_BUCKET', true) ?: getenv('S3_BUCKET');
    if(!empty($bucket)){
        require('/app/vendor/autoload.php');

        // this will simply read AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY from env vars
        $s3 = new S3Client([
            'version'  => SDK_VERSION,
            'region'   => REGION,
        ]);
    }

?>