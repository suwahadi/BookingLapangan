<?php

namespace App\Livewire\Admin\Venues;

use App\Models\Venue;
use App\Models\VenueMedia;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Kelola Media Venue - Admin Panel')]
class VenueMediaManage extends Component
{
    use WithFileUploads;

    public Venue $venue;
    public $photos = [];

    public function mount(Venue $venue)
    {
        $this->venue = $venue;
    }

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:5120', // max 5MB per photo
        ]);

        foreach ($this->photos as $photo) {
            $path = $photo->store('venues/' . $this->venue->id, 'public');
            
            // Check if this is the first photo, if so set as cover
            $isFirst = $this->venue->media()->count() === 0;

            $this->venue->media()->create([
                'file_path' => $path,
                'is_cover' => $isFirst,
                'order_column' => $this->venue->media()->max('order_column') + 1,
            ]);
        }

        $this->photos = [];
        $this->dispatch('toast', message: 'Foto berhasil diunggah!', type: 'success');
    }

    public function setCover(VenueMedia $media)
    {
        // Reset all covers for this venue
        $this->venue->media()->update(['is_cover' => false]);
        
        // Set new cover
        $media->update(['is_cover' => true]);

        $this->dispatch('toast', message: 'Foto sampul berhasil diperbarui!', type: 'success');
    }

    public function deleteMedia(VenueMedia $media)
    {
        // Delete file from storage
        Storage::disk('public')->delete($media->file_path);
        
        $wasCover = $media->is_cover;
        $media->delete();

        // If cover was deleted, set the next one as cover if exists
        if ($wasCover) {
            $nextMedia = $this->venue->media()->first();
            if ($nextMedia) {
                $nextMedia->update(['is_cover' => true]);
            }
        }

        $this->dispatch('toast', message: 'Foto telah dihapus.', type: 'info');
    }

    public function reorder($ids)
    {
        foreach ($ids as $index => $id) {
            VenueMedia::where('id', $id)->update(['order_column' => $index]);
        }
    }

    public function render()
    {
        return view('livewire.admin.venues.venue-media-manage', [
            'media' => $this->venue->media()->orderBy('order_column')->get(),
        ]);
    }
}
