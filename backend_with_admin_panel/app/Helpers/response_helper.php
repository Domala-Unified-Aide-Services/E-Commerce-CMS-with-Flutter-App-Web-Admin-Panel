<?php

if (!function_exists('respond'))
{
    function respond($data = [], int $status = 200)
    {
        return \Config\Services::response()
            ->setStatusCode($status)
            ->setJSON($data);
    }
}
