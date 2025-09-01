<?php

namespace App\Core;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Uploader
{
    private static ?S3Client $s3Client = null;

    private static function getClient(): S3Client
    {
        if (self::$s3Client === null) {
            self::$s3Client = new S3Client([
                'region'      => $_ENV['AWS_DEFAULT_REGION'],
                'version'     => 'latest',
                'credentials' => [
                    'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                    'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                ]
            ]);
        }
        return self::$s3Client;
    }

    public static function upload(string $sourceFile, string $key): ?string
    {
        $s3 = self::getClient();
        $bucket = $_ENV['S3_BUCKET'];

        try {
            $result = $s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => $key,
                'SourceFile' => $sourceFile,
                'ACL'        => 'public-read'
            ]);
            return $result['ObjectURL'] ?? null;
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}