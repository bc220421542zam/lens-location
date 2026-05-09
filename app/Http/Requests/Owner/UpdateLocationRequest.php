<?php

namespace App\Http\Requests\Owner;

class UpdateLocationRequest extends StoreLocationRequest
{
    // Same rules as creation; subclassing keeps a single source of truth
    // and lets us diverge later (e.g. allowing partial updates) without churn.
}
