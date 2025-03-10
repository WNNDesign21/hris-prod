<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('live-attendance.{organisasiId}', function (User $user, int $organisasiId) {
    return (int) $user->organisasi_id === (int) $organisasiId;
});
