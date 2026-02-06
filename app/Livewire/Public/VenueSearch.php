<?php

namespace App\Livewire\Public;

use App\Services\Venue\VenueSearchService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VenueSearch extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $sport_type = '';

    #[Url(except: '')]
    public string $keyword = '';

    #[Url(except: '')]
    public string $date = '';

    #[Url(except: '')]
    public string $start_time = '';
    
    #[Url(except: '')]
    public string $end_time = '';

    public function render(VenueSearchService $service)
    {
        $filters = [
            'sport_type' => $this->sport_type,
            'keyword' => $this->keyword,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];

        $venues = $service->search($filters, 12);

        return view('livewire.public.venue-search', [
            'venues' => $venues
        ]);
    }

    public function updated($property)
    {
        if (in_array($property, ['sport_type', 'keyword', 'date', 'start_time', 'end_time'])) {
            $this->resetPage();
        }
    }

    public function search()
    {
        $this->resetPage();
    }
}
