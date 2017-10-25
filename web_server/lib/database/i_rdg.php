<?php
namespace StudentCheckIn;
interface IRDG {
    static function Insret($object);
    static function Update($object);
    static function Delete($identifier);
    static function Select($identifier);
    static function ResultToObject($input);
}
