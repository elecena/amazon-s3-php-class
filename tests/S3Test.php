<?php

use PHPUnit\Framework\TestCase;

class S3Test extends TestCase {
    public function testClassIsPresentInAutoloader() {
        $this->assertTrue( class_exists( S3::class ) );
    }
}
