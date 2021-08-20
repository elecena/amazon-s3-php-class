<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

abstract class S3BaseTest extends TestCase {

    /** @var string */
    protected $s3AccessKey;
    /** @var string */
    protected $s3SecretKey;
    /** @var string e.g. test.macbre.net */
    protected $s3Bucket;
    /** @var string e.g. s3-eu-west-1.amazonaws.com/test.macbre.net */
    protected $s3Endpoint;

    /**
     * Refer to tests/README.md on how to set up your local testing env.
     */
    public function setUp(): void {
        $this->s3AccessKey = getenv( 'S3_ACCESS_KEY' );
        $this->s3SecretKey = getenv( 'S3_SECRET_KEY' );
        $this->s3Bucket = getenv( 'S3_BUCKET' );
        $this->s3Endpoint = getenv( 'S3_ENDPOINT' );
    }

    protected function getGuzzleClient(): Client {
        return new Client([
            'base_uri' => sprintf('https://%s/', $this->s3Endpoint),
        ]);
    }
}
