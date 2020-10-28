<?php

namespace App\Mrchimp\Chimpcom;

class Id
{
    /**
     * Convert integer ID to front-facing id
     */
    public static function encode(int $id): string
    {
        return dechex($id);
    }

    /**
     * Encode an array of Ids
     */
    public static function encodeMany(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = static::encode($id);
        }

        return $ids;
    }

    /**
     * Convert front-facing id to integer
     */
    public static function decode(string $id): int
    {
        try {
            return hexdec($id);
        } catch (\Exception $e) {
            return -1;
        }
    }

    /**
     * Decode an array of IDs
     */
    public static function decodeMany(array $ids): array
    {
        foreach ($ids as &$id) {
            $id = static::decode($id);
        }

        return $ids;
    }
}
