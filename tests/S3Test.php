<?php

class S3Test extends S3BaseTest {

    public function setUp(): void {
        parent::setUp();

        // TODO: use a new, virtual-hosted style references
        // e.g. https://jbarr-public.s3.amazonaws.com/images/ritchie_and_thompson_pdp11.jpeg
        S3::$endpoint = sprintf( 's3-%s.amazonaws.com', $this->s3Region );

        S3::setAuth( $this->s3AccessKey, $this->s3SecretKey );
        S3::$region = $this->s3Region;
        S3::setSSL(true);
    }

    public function testClassIsPresentInAutoloader() {
        $this->assertTrue( class_exists( S3::class ) );
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