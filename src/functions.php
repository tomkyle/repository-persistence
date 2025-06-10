<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

if (!function_exists("json_validate")) {
    function json_validate(string $json): bool
    {
        try {
            json_decode($json, flags: JSON_THROW_ON_ERROR);
            return true;
        } catch (\JsonException) {
            return false;
        }
    }
}
