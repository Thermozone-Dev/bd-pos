<?php

if (!function_exists('generate_hex_color')) {
    function generate_hex_color(): string
    {
        return '#' . strtoupper(dechex(mt_rand(0x000000, 0xFFFFFF)));
    }
}