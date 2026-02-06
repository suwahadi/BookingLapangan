<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Form Venue - Admin Panel')]
class VenueFormAdmin extends Component
{
    public ?Venue $venue = null;
    public bool $isEdit = false;

    // Fields
    public string $name = '';
    public string $sport_type = '';
    public string $slug = '';
    public string $address = '';
    public string $city = '';
    public string $province = '';
    public string $postal_code = '';
    public string $phone = '';
    public string $email = '';
    public string $description = '';
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sport_type' => 'required|string|max:100',
            'slug' => 'required|string|max:255|unique:venues,slug,' . ($this->venue->id ?? 'NULL'),
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?Venue $venue = null)
    {
        if ($venue && $venue->exists) {
            $this->venue = $venue;
            $this->isEdit = true;
            $this->name = $venue->name ?? '';
            $this->sport_type = $venue->sport_type ?? '';
            $this->slug = $venue->slug ?? '';
            $this->address = $venue->address ?? '';
            $this->city = $venue->city ?? '';
            $this->province = $venue->province ?? '';
            $this->postal_code = $venue->postal_code ?? '';
            $this->phone = $venue->phone ?? '';
            $this->email = $venue->email ?? '';
            $this->description = $venue->description ?? '';
            $this->is_active = (bool) ($venue->is_active ?? true);
        }
    }

    public function updatedName($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'sport_type' => $this->sport_type,
            'slug' => $this->slug,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $this->venue->update($data);
            $message = 'Venue berhasil diperbarui!';
        } else {
            $venue = Venue::create($data);
            
            // Initialize default setting & policy
            $venue->setting()->create([
                'slot_duration_minutes' => 60,
                'min_booking_hours_ahead' => 2,
                'max_booking_days_ahead' => 30,
            ]);

            $venue->policy()->create([
                'allow_dp' => true,
                'dp_min_percent' => 50,
                'reschedule_allowed' => true,
                'reschedule_deadline_hours' => 24,
                'refund_allowed' => false,
            ]);

            $message = 'Venue baru berhasil ditambahkan!';
        }

        session()->flash('success', $message);
        return $this->redirectRoute('admin.venues.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-form-admin');
    }
}
