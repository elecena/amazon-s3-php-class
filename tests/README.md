Tests
=====

```
composer run test
```

## Setup

Make sure the following env variables are set before running tests locally:

* `S3_ACCESS_KEY`
* `S3_SECRET_KEY`
* `S3_BUCKET` (e.g `test.macbre.net`) - this used for S3 operations
* `S3_ENDPOINT` (e.g. `s3-eu-west-1.amazonaws.com/test.macbre.net`) - this is used to check uploads via HTTP
