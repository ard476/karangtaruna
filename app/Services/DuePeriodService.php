<?php

namespace App\Services;

use App\Enums\DuePaymentStatus;
use App\Enums\MemberStatus;
use App\Models\DuePeriod;
use App\Models\DuePayment;
use App\Models\Member;

class DuePeriodService
{
    public function generatePaymentsForActiveMembers(DuePeriod $period): void
    {
        $members = Member::where('status', MemberStatus::Aktif)->get();

        foreach ($members as $member) {
            DuePayment::firstOrCreate(
                [
                    'due_period_id' => $period->id,
                    'member_id' => $member->id,
                ],
                ['status' => DuePaymentStatus::BelumBayar]
            );
        }
    }
}
