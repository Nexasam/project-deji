<?php

namespace App\Support;

use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\UserRole;

final readonly class ActiveBusinessContext
{
    public function __construct(
        public Business $business,
        public BusinessMembership $membership,
        public UserRole $roleAssignment,
    ) {}
}
