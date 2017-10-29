<?php
namespace StudentCheckIn;
abstract class UUID {
    public static function CreateV4() {
        $r = unpack('v*', random_bytes(16));
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            $r[1], $r[2], $r[3], $r[4] & 0x0fff | 0x4000,
            $r[5] & 0x3fff | 0x8000, $r[6], $r[7], $r[8]);
    }
}