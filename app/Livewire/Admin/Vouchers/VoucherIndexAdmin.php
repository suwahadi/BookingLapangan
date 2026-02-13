<?php

namespace App\Livewire\Admin\Vouchers;

use App\Enums\VoucherDiscountType;
use App\Models\Venue;
use App\Models\VenueCourt;
use App\Models\Voucher;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Voucher - Admin Panel')]
class VoucherIndexAdmin extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $q = '';

    #[Url(history: true)]
    public string $statusFilter = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $code = '';
    public string $name = '';
    public string $description = '';
    public string $discount_type = 'FIXED';
    public string $discount_value = '';
    public string $max_discount_amount = '';
    public string $min_order_amount = '';
    public string $scope = 'all';
    public string $venue_id = '';
    public string $venue_court_id = '';
    public string $max_usage_total = '';
    public string $max_usage_per_user = '';
    public string $valid_from = '';
    public string $valid_until = '';

    public function updatedQ()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedScope()
    {
        if ($this->scope === 'all') {
            $this->venue_id = '';
            $this->venue_court_id = '';
        }
        if ($this->scope !== 'court') {
            $this->venue_court_id = '';
        }
    }

    public function updatedVenueId()
    {
        $this->venue_court_id = '';
    }

    public function getVenuesProperty()
    {
        return Venue::orderBy('name')->get();
    }

    public function getCourtsProperty()
    {
        if ($this->venue_id) {
            return VenueCourt::where('venue_id', $this->venue_id)->orderBy('name')->get();
        }
        return collect();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $voucher = Voucher::findOrFail($id);
        $this->editingId = $voucher->id;
        $this->code = $voucher->code;
        $this->name = $voucher->name;
        $this->description = $voucher->description ?? '';
        $this->discount_type = $voucher->discount_type->value;
        $this->discount_value = (string) $voucher->discount_value;
        $this->max_discount_amount = $voucher->max_discount_amount ? (string) $voucher->max_discount_amount : '';
        $this->min_order_amount = $voucher->min_order_amount ? (string) $voucher->min_order_amount : '';
        $this->scope = $voucher->scope;
        $this->venue_id = $voucher->venue_id ? (string) $voucher->venue_id : '';
        $this->venue_court_id = $voucher->venue_court_id ? (string) $voucher->venue_court_id : '';
        $this->max_usage_total = (string) $voucher->max_usage_total;
        $this->max_usage_per_user = (string) $voucher->max_usage_per_user;
        $this->valid_from = $voucher->valid_from ? $voucher->valid_from->format('Y-m-d\TH:i') : '';
        $this->valid_until = $voucher->valid_until ? $voucher->valid_until->format('Y-m-d\TH:i') : '';
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'code' => ['required', 'string', 'max:20', $this->editingId ? 'unique:vouchers,code,' . $this->editingId : 'unique:vouchers,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'discount_type' => ['required', 'in:FIXED,PERCENTAGE'],
            'discount_value' => ['required', 'numeric', 'min:1'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'scope' => ['required', 'in:all,venue,court'],
            'venue_id' => ['nullable'],
            'venue_court_id' => ['nullable'],
            'max_usage_total' => ['required', 'numeric', 'min:1'],
            'max_usage_per_user' => ['required', 'numeric', 'min:1'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after:valid_from'],
        ];

        if ($this->discount_type === 'PERCENTAGE') {
            $rules['discount_value'][] = 'max:100';
        }

        if (in_array($this->scope, ['venue', 'court'])) {
            $rules['venue_id'] = ['required', 'exists:venues,id'];
        }

        if ($this->scope === 'court') {
            $rules['venue_court_id'] = ['required', 'exists:venue_courts,id'];
        }

        $messages = [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.unique' => 'Kode voucher sudah digunakan.',
            'code.max' => 'Kode voucher maksimal 20 karakter.',
            'name.required' => 'Nama voucher wajib diisi.',
            'name.max' => 'Nama voucher maksimal 100 karakter.',
            'discount_type.required' => 'Tipe diskon wajib dipilih.',
            'discount_type.in' => 'Tipe diskon tidak valid.',
            'discount_value.required' => 'Nilai diskon wajib diisi.',
            'discount_value.numeric' => 'Nilai diskon harus berupa angka.',
            'discount_value.min' => 'Nilai diskon minimal 1.',
            'discount_value.max' => 'Nilai persentase maksimal 100.',
            'max_discount_amount.numeric' => 'Maks. potongan harus berupa angka.',
            'max_discount_amount.min' => 'Maks. potongan minimal 0.',
            'min_order_amount.numeric' => 'Min. order harus berupa angka.',
            'min_order_amount.min' => 'Min. order minimal 0.',
            'scope.required' => 'Cakupan voucher wajib dipilih.',
            'scope.in' => 'Cakupan voucher tidak valid.',
            'venue_id.required' => 'Venue wajib dipilih.',
            'venue_id.exists' => 'Venue tidak ditemukan.',
            'venue_court_id.required' => 'Lapangan wajib dipilih.',
            'venue_court_id.exists' => 'Lapangan tidak ditemukan.',
            'max_usage_total.required' => 'Kuota total wajib diisi.',
            'max_usage_total.numeric' => 'Kuota total harus berupa angka.',
            'max_usage_total.min' => 'Kuota total minimal 1.',
            'max_usage_per_user.required' => 'Kuota per user wajib diisi.',
            'max_usage_per_user.numeric' => 'Kuota per user harus berupa angka.',
            'max_usage_per_user.min' => 'Kuota per user minimal 1.',
            'valid_from.required' => 'Tanggal mulai wajib diisi.',
            'valid_from.date' => 'Format tanggal mulai tidak valid.',
            'valid_until.required' => 'Tanggal berakhir wajib diisi.',
            'valid_until.date' => 'Format tanggal berakhir tidak valid.',
            'valid_until.after' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];

        $validated = $this->validate($rules, $messages);

        $data = [
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount_amount' => $validated['max_discount_amount'] ?: null,
            'min_order_amount' => $validated['min_order_amount'] ?: 0,
            'scope' => $validated['scope'],
            'venue_id' => in_array($validated['scope'], ['venue', 'court']) ? $validated['venue_id'] : null,
            'venue_court_id' => $validated['scope'] === 'court' ? $validated['venue_court_id'] : null,
            'max_usage_total' => $validated['max_usage_total'],
            'max_usage_per_user' => $validated['max_usage_per_user'],
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
        ];

        if ($this->editingId) {
            $voucher = Voucher::findOrFail($this->editingId);
            $voucher->update($data);
            $this->dispatch('toast', message: 'Voucher berhasil diperbarui.', type: 'success');
        } else {
            $data['is_active'] = true;
            $data['usage_count_total'] = 0;
            Voucher::create($data);
            $this->dispatch('toast', message: 'Voucher berhasil dibuat.', type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update(['is_active' => !$voucher->is_active]);
        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', message: "Voucher berhasil {$status}.", type: 'success');
    }

    public function confirmDelete(int $id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingId) {
            Voucher::findOrFail($this->deletingId)->delete();
            $this->dispatch('toast', message: 'Voucher berhasil dihapus.', type: 'success');
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    private function resetForm()
    {
        $this->code = '';
        $this->name = '';
        $this->description = '';
        $this->discount_type = 'FIXED';
        $this->discount_value = '';
        $this->max_discount_amount = '';
        $this->min_order_amount = '';
        $this->scope = 'all';
        $this->venue_id = '';
        $this->venue_court_id = '';
        $this->max_usage_total = '';
        $this->max_usage_per_user = '';
        $this->valid_from = '';
        $this->valid_until = '';
        $this->resetValidation();
    }

    public function render()
    {
        $query = Voucher::query()
            ->with(['venue', 'court'])
            ->when(trim($this->q) !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'ilike', '%' . $this->q . '%')
                      ->orWhere('name', 'ilike', '%' . $this->q . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        return view('livewire.admin.vouchers.voucher-index-admin', [
            'vouchers' => $query->paginate(15),
            'totalVouchers' => Voucher::count(),
            'activeVouchers' => Voucher::where('is_active', true)->count(),
        ]);
    }
}
