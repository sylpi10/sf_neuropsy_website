<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotReservedSlug extends Constraint
{
    public string $message = 'L\'adresse « /{{ slug }} » est déjà utilisée par le site, choisissez-en une autre.';
}
