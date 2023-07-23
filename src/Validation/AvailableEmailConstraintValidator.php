<?php

namespace App\Validation;

use App\Service\UserManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvailableEmailConstraintValidator extends ConstraintValidator
{
    public function __construct(private UserManager $userManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        $user = $this->userManager->findByEmail($value);
        if (false === empty($user)) {
            $this->context->addViolation($constraint->message);
        }
    }

}
