<?php

namespace App\Contracts;

interface ValidatesRequestInterface
{
    public function validated(): array;
}