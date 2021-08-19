<?php

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class S3Test extends TestCase {

    const PUBLIC_OBJECT = 'README';
    const PRIVATE_OBJECT = '.private';

    /** @var string */
    private $s3AccessKey;
    /** @var string */
    private $s3SecretKey;
    /** @var string e.g. test.macbre.net */
    private $s3Bucket;
    /** @var string e.g. test.macbre.net.s3-eu-west-1.amazonaws.com */
    private $s3Endpoint;

    public function setUp(): void {
        $this->s3AccessKey = getenv( 'S3_ACCESS_KEY' );
        $this->s3SecretKey = getenv( 'S3_SECRET_KEY' );
        $this->s3Bucket = getenv( 'S3_BUCKET' );
        $this->s3Endpoint = getenv( 'S3_ENDPOINT' );
    }

    private function getGuzzleClient(): GuzzleHttp\Client {
        return new GuzzleHttp\Client([
            'base_uri'        => sprintf('http://%s/', $this->s3Endpoint),
        ]);
    }

    public function testClassIsPresentInAutoloader() {
        $this->assertTrue( class_exists( S3::class ) );
    }

    /**
     * @throws GuzzleException
     */
    public function testPublicFileExistsOnS3() {
        $resp = $this->getGuzzleClient()->get(self::PUBLIC_OBJECT);
        $this->assertEquals(200, $resp->getStatusCode());
    }
    /**
     * @throws GuzzleException
     */
    public function testPrivateFileIsNotAccessible() {
        try {
            $this->getGuzzleClient()->get(self::PRIVATE_OBJECT);
        }
        catch (ClientException $ex) {
            $this->assertEquals(403, $ex->getResponse()->getStatusCode());
        }
    }
}
