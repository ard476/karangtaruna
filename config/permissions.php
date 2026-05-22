<?php

use App\Enums\UserRole;

return [
    /*
    |--------------------------------------------------------------------------
    | Hak akses per peran pengurus & anggota
    |--------------------------------------------------------------------------
    */
    'members.manage' => [UserRole::Ketua, UserRole::Sekretaris],
    'members.view' => [UserRole::Ketua, UserRole::Sekretaris, UserRole::Bendahara],

    'finance.manage' => [UserRole::Ketua, UserRole::Bendahara],
    'finance.view' => [UserRole::Ketua, UserRole::Bendahara, UserRole::Sekretaris],

    'activities.manage' => [UserRole::Ketua, UserRole::Sekretaris],
    'activities.view' => [UserRole::Ketua, UserRole::Sekretaris, UserRole::Bendahara, UserRole::Anggota],

    'announcements.manage' => [UserRole::Ketua, UserRole::Sekretaris],
    'announcements.view' => [UserRole::Ketua, UserRole::Sekretaris, UserRole::Bendahara, UserRole::Anggota],

    'organization.manage' => [UserRole::Ketua],
    'organization.view' => [UserRole::Ketua, UserRole::Sekretaris, UserRole::Bendahara],

    'reports.view' => [UserRole::Ketua, UserRole::Sekretaris, UserRole::Bendahara],

    'users.manage' => [UserRole::SuperAdmin],
    'users.view' => [UserRole::SuperAdmin],
];
