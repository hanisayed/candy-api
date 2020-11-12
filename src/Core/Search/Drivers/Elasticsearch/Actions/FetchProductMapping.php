<?php

namespace GetCandy\Api\Core\Search\Drivers\Elasticsearch\Actions;

use Lorisleiva\Actions\Action;
use GetCandy\Api\Core\Search\Indexables\ProductIndexable;

class FetchProductMapping extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
//        if (app()->runningInConsole()) {
//            return true;
//        }
//        return $this->user()->can('index-documents');
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return array
     */
    public function handle()
    {
        return (new ProductIndexable)->getMapping();
    }
}
