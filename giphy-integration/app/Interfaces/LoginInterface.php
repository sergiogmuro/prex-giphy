<?php

namespace App\Interfaces;

interface LoginInterface
{
    /**
     * @return array{
     *          user_id: integer,
     *          token: string,
     *          expires_at: integer
     *      }
     *
     * @throws \Exception
     */
    function login(string $email, string $password): array;
}
