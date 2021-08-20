<?php

class S3Test extends S3BaseTest {

    const TEST_STRING_CONTENT = "<strong>Hi</strong> I'm a test content";
    const TEST_STRING_MIME_TYPE = "text/html; charset=utf-8";
    const TEST_X_FOO_HEADER = "header value";

    public function setUp(): void {
        S3BaseTest::setUp();
        $this->setUpS3Client();
    }

    public function testGetBucket() {
        $bucket = S3::getBucket( $this->s3Bucket );
        $files = array_keys($bucket);

        $this->assertContains( self::PRIVATE_OBJECT, $files );
        $this->assertContains( self::PUBLIC_OBJECT, $files );
    }

    /**
     * @param string $uri
     * @param ?int $expectedSize
     * @dataProvider getObjectInfoProvider
     */
    public function testGetObjectInfo( string $uri, ?int $expectedSize ) {
        $obj = S3::getObjectInfo( $this->s3Bucket, $uri );

        if ($expectedSize === null) {
            $this->assertFalse($obj);
        }
        else {
            $this->assertEquals($expectedSize, $obj['size']);
        }
    }

    /**
     * Refer to .github/workflows/phpunit.yml for the test files content.
     */
    public function getObjectInfoProvider(): Generator {
        yield 'public file' => [
            self::PUBLIC_OBJECT, 4
        ];
        yield 'private file' => [
            self::PRIVATE_OBJECT, 20
        ];
        yield 'not existing file file' => [
            self::NOT_EXISTING_OBJECT, null
        ];
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testPutObject(string $acl = S3::ACL_PUBLIC_READ ) {
        $uri = uniqid('s3') . '.html';

        $res = S3::putObjectString(
            self::TEST_STRING_CONTENT,
            $this->s3Bucket,
            $uri,
            $acl,
            [
                // this will be returned as x-amz-meta-x-foo
                'X-Foo' => self::TEST_X_FOO_HEADER,
            ],
            self::TEST_STRING_MIME_TYPE
        );

        $this->assertTrue($res, 'putObjectString() was successful');

        // check the upload
        $obj = S3::getObjectInfo($this->s3Bucket, $uri);

        $this->assertEquals(strlen(self::TEST_STRING_CONTENT), $obj['size']);
        $this->assertEquals(self::TEST_X_FOO_HEADER, $obj['x-amz-meta-x-foo']);
        $this->assertEquals(self::TEST_STRING_MIME_TYPE, $obj['type']);

        // check the public access
        if ($acl === S3::ACL_PUBLIC_READ) {
            $resp = $this->getGuzzleClient()->get($uri);

            $this->assertEquals(200, $resp->getStatusCode());

            $this->assertEquals(strlen(self::TEST_STRING_CONTENT), $resp->getBody()->getSize());
            $this->assertEquals(self::TEST_STRING_CONTENT, $resp->getBody()->getContents());

            $this->assertEquals(self::TEST_STRING_MIME_TYPE, $resp->getHeader('content-type')[0]);
            $this->assertEquals(self::TEST_X_FOO_HEADER, $resp->getHeader('x-amz-meta-x-foo')[0]);
        }

        // remove it
        $res = S3::deleteObject($this->s3Bucket, $uri);
        $this->assertTrue($res, 'deleteObject() was successful');
    }

}
