<?php

namespace App\Livewire\Public;

use App\Services\Venue\VenueSearchService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
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

        // Cache logic: Only cache if no filters are applied and on page 1
        $page = $this->paginators['page'] ?? 1;
        $isCleanSearch = empty(array_filter($filters)) && $page === 1;
        $cacheKey = 'venues_homepage_list';
        $cacheDuration = 600; // 10 minutes

        if ($isCleanSearch) {
            $venues = Cache::remember($cacheKey, $cacheDuration, function () use ($service, $filters) {
                return $service->search($filters, 12);
            });
        } else {
            $venues = $service->search($filters, 12);
        }

        // Return view
        return view('livewire.public.venue-search', [
            'venues' => $venues
        ]);
    }
    
    public function placeholder()
    {
        return view('livewire.public.venue-search-placeholder');
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
