<?php

namespace GetCandy\Api\Core\Search\Drivers\Elasticsearch;

use Elastica\Client;
use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use GetCandy\Api\Core\Products\Models\Product;
use GetCandy\Api\Core\Search\Drivers\AbstractSearchDriver;
use GetCandy\Api\Core\Search\Drivers\Elasticsearch\Actions\SetIndexLive;
use GetCandy\Api\Core\Search\Drivers\Elasticsearch\Actions\IndexProducts;
use GetCandy\Api\Core\Search\Drivers\Elasticsearch\Actions\Searching\Search;
use GetCandy\Api\Core\Search\Drivers\Elasticsearch\Events\IndexingCompleteEvent;

class Elasticsearch extends AbstractSearchDriver
{
    public function __construct(Dispatcher $events, Container $container)
    {
        $events->listen(IndexingCompleteEvent::class, function ($event) {
            SetIndexLive::run([
                'indexes' => $event->indexes,
                'type' => $event->type,
            ]);
        });
    }

    public function index($documents, $final = false)
    {
        $type = get_class($documents->first());
        switch ($type) {
            case Product::class:
                IndexProducts::run([
                    'products' => $documents,
                    'uuid' => $this->reference,
                    'final' => $final,
                ]);
                break;
            default:
            break;
        }
    }

    public function search($data)
    {
        if ($data instanceof Request) {
            return (new Search)->runAsController($data);
        }
        return Search::run([
            'type' => $data['type'] ?? 'products',
            'filters' => $data['filters'] ?? [],
            'aggregates' => $data['aggregates'] ?? [],
            'term' => $data['term'] ?? null,
            'language' => $data['language'] ?? app()->getLocale(),
        ]);
    }

    public function config()
    {
        return [
            'features' => [
                'faceting',
                'aggregates',
            ]
        ];
    }
}