<?php

class S3Test extends S3BaseTest {

    public function testClassIsPresentInAutoloader() {
        $this->assertTrue( class_exists( S3::class ) );
    }

}