<?php


namespace App\Services;


use App\Enums\CacheKey;
use App\Enums\CacheTags;
use App\Models\Channel;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\ArrayShape;

class CacheService
{
    /**
     * @param string $key
     * @param array $tags
     * @param callable $function
     * @return mixed
     */
    public function cache(string $key, array $tags, Callable $function): mixed
    {
        if(config('cache.default') !== 'redis'){
            return Cache::rememberForever($key, $function);
        }

        return Cache::tags($tags)->rememberForever($key, $function);
    }

    /**
     * @return array[]
     */
    #[ArrayShape([CacheTags::COMPANY => "array", CacheTags::CHANNEL => "array"])]
    public function tagToKeyMapping()
    {
        return [
            CacheTags::COMPANY => [
                CacheKey::ALL_COMPANIES_COLLECTION
            ],
            CacheTags::CHANNEL => [
                CacheKey::ALL_CHANNELS_COLLECTION
            ],
        ];
    }

    /**
     * @param array $tags arary of tags (string)
     */
    public function forget(array $tags)
    {
        if(config('cache.default') !== 'redis'){

            // Remove all cache keys associated to the tag
            collect($this->tagToKeyMapping())
                ->filter(function($keys, $tag) use ($tags){
                    return in_array($tag, $tags);
                })
                ->collapse()
                ->each(function(string $key){
                    Cache::forget($key);
                });
        }else{
            Cache::tags($tags)->flush();
        }
    }

    public function companies(int $id = null): Collection|Model
    {
        $models = $this->cache(CacheKey::ALL_COMPANIES_COLLECTION, [CacheTags::COMPANY], fn() => Company::all()->keyBy('id'));

        if(is_null($id)) return $models;
        if(empty($models[$id])) abort(402);

        return $models[$id];
    }

    /**
     * Get Company model for a given channel id
     * @param int $id
     * @return Model|Collection
     */
    public function companyOfChannel(int $id)
    {
        return $this->companies($this->channels($id)->company_id);
    }

    public function channels(int $id = null): Collection|Model
    {
        $models = $this->cache(CacheKey::ALL_CHANNELS_COLLECTION, [CacheTags::CHANNEL], fn() => Channel::all()->keyBy('id'));

        if(is_null($id)) return $models;
        if(empty($models[$id])) abort(404);

        return $models[$id];
    }
}