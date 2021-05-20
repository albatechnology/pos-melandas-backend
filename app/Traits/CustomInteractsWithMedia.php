<?php


namespace App\Traits;

use App\Models\Media;
use App\Services\CoreService;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

trait CustomInteractsWithMedia
{
    use InteractsWithMedia;

    public function getImageAttribute()
    {
        $files = $this->getMedia('image');
        $files->each(function ($item) {
            $item->url       = $item->getUrl() ?? null;
            $item->thumbnail = $item->getUrl('thumb') ?? null;
            $item->preview   = $item->getUrl('preview') ?? null;
        });

        return $files;
    }

    public function getImagesAttribute()
    {
        if (method_exists(static::class, 'getPhotoAttribute')) {
            return $this->photo;
        } elseif (method_exists(static::class, 'getImageAttribute')) {
            return $this->image;
        }

        return null;
    }

    public function getRecordImages()
    {
        $data = $this->getApiImagesAttribute();

        return [
            'id'        => $data['id'],
            'url'       => $data['url'],
            'thumbnail' => $data['thumbnail'],
            'preview'   => $data['preview'],
            'mime_type' => $data['image/png'],
        ];
    }

    public function getApiImagesAttribute()
    {
        if (empty($this->images) || $this->images->isEmpty()) {
            $files = collect([
                Media::factory()->model($this)->make(['id' => 1]),
                Media::factory()->model($this)->make(['id' => 2]),
            ]);

            $files->each(function ($item) {
                $item->url       = app(CoreService::class)->getDummyImageUrl($this);
                $item->thumbnail = app(CoreService::class)->getDummyImageUrl($this);
                $item->preview   = app(CoreService::class)->getDummyImageUrl($this);
            });

            return $files;
        }

        return $this->images;
    }

    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }
}
