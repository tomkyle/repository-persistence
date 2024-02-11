<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

if(!function_exists("json_validate")) {
    function json_validate()
    {
        try {
            json_decode($json, JSON_THROW_ON_ERROR);
            return true;
        } catch(\JsonException) {
            return false;
        }
    }
}
