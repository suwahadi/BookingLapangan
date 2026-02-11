<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use App\Models\VenuePolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Kebijakan Venue - Admin Panel')]
class VenuePolicyManageAdmin extends Component
{
    public Venue $venue;

    // DP Policy
    public bool $allow_dp = false;
    public int $dp_min_percent = 50;

    // Reschedule Policy
    public bool $reschedule_allowed = false;
    public int $reschedule_deadline_hours = 24;

    // Refund Policy
    public bool $refund_allowed = false;
    public int $refund_h72 = 100;
    public int $refund_h24 = 50;
    public int $refund_below24 = 0;

    public ?string $flash = null;
    public ?string $error = null;

    public function mount(Venue $venue): void
    {
        $this->venue = $venue;
        $policy = $venue->policy;

        if ($policy) {
            $this->allow_dp = $policy->allow_dp;
            $this->dp_min_percent = $policy->dp_min_percent ?: 50;
            $this->reschedule_allowed = $policy->reschedule_allowed;
            $this->reschedule_deadline_hours = $policy->reschedule_deadline_hours ?: 24;
            $this->refund_allowed = $policy->refund_allowed;

            $rules = $policy->refund_rules ?? [];
            $this->refund_h72 = $rules['h_minus_72'] ?? 100;
            $this->refund_h24 = $rules['h_minus_24'] ?? 50;
            $this->refund_below24 = $rules['below_24'] ?? 0;
        }
    }

    public function save(): void
    {
        $this->flash = null;
        $this->error = null;

        // Validasi
        if ($this->allow_dp && ($this->dp_min_percent < 1 || $this->dp_min_percent > 100)) {
            $this->error = 'Persentase DP minimal harus antara 1 - 100.';
            return;
        }

        if ($this->reschedule_allowed && $this->reschedule_deadline_hours < 1) {
            $this->error = 'Deadline reschedule minimal 1 jam.';
            return;
        }

        try {
            DB::transaction(function () {
                VenuePolicy::updateOrCreate(
                    ['venue_id' => $this->venue->id],
                    [
                        'allow_dp' => $this->allow_dp,
                        'dp_min_percent' => $this->allow_dp ? $this->dp_min_percent : 0,
                        'reschedule_allowed' => $this->reschedule_allowed,
                        'reschedule_deadline_hours' => $this->reschedule_allowed ? $this->reschedule_deadline_hours : 0,
                        'refund_allowed' => $this->refund_allowed,
                        'refund_rules' => $this->refund_allowed ? [
                            'h_minus_72' => (int) $this->refund_h72,
                            'h_minus_24' => (int) $this->refund_h24,
                            'below_24' => (int) $this->refund_below24,
                        ] : null,
                    ]
                );
            });

            $this->venue->refresh()->load('policy');
            $this->flash = 'Kebijakan venue berhasil disimpan.';
        } catch (\Throwable $e) {
            $this->error = 'Gagal menyimpan kebijakan: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-policy-manage-admin');
    }
}
