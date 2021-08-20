<?php

class S3Test extends S3BaseTest {

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

}