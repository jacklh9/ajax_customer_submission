<?php

    $bucket = getenv('S3_BUCKET', true) ?: getenv('S3_BUCKET');
    if(!empty($bucket)){
        require('/app/vendor/autoload.php');
        // this will simply read AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY from env vars
        $s3 = new Aws\S3\S3Client([
            'version'  => '2006-03-01',
            'region'   => 'us-west-1',
        ]);
    }

?>