<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;

class AvailableEmailConstraint extends Constraint
{
    public string $message = 'Email is already registered.';
}
