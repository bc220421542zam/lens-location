<?php

use Illuminate\Support\Facades\Broadcast;

    Broadcast::channel('admin', function ($user) {
        return $user->role === 'admin';
    });
