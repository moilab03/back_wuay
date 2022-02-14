<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponse
{
    private function successResponse($data, $code)
    {
        return response()->json(['data' => $data, 'code' => $code], 200);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['data' => $message, 'code' => $code], 200);
    }

    protected function showAll($collection, $transformer, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection, 'code' => $code], 200);
        }
        $collection = $this->transformData($collection, $transformer);
        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        if (isset($_GET['include']))
            $transformation->parseIncludes($_GET['include']);

        return $transformation->toArray();
    }

    /**
     * función para filtrar segun el campo enviado en el request.
     */
    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::attributes($query);

            if (isset($attribute, $value)) {
                $values = explode('|', $value);
                $countValues = count($values);

                if ($countValues === 1) {
                    $collection = $collection->where($query, $value);
                } elseif ($countValues === 2) {
                    switch ($values[0]) {
                        case '>':
                            $collection = $collection->where($query, '>', $values[1]);
                            break;
                        case '<':
                            $collection = $collection->where($query, '<', $values[1]);
                            break;
                        case '>=':
                            $collection = $collection->where($query, '>=', $values[1]);
                            break;
                        case '<=':
                            $collection = $collection->where($query, '<=', $values[1]);
                            break;
                        case 'like':
                            $collection = $collection->reject(function($query) use ($values) {
                                return mb_strpos($query, $values[1]) === false;
                            });
                            break;
                        case 'in':
                            $collection = $collection->whereIn($query, explode(',', $values[1]));
                            break;
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * función para ordenar segun el campo sort_by de un request.
     */
    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $values = explode('|', request()->sort_by);
            $countValues = count($values);

            if ($countValues === 1) {
                $attribute = $transformer::attributes($values[0]);

                $collection = $collection->sortBy->{$attribute};
            } elseif ($countValues === 2) {
                $attribute = $transformer::attributes($values[1]);

                switch ($values[0]) {
                    case 'desc':
                        $collection = $collection->sortByDesc->{$attribute};
                        break;
                    case 'asc':
                        $collection = $collection->sortBy->{$attribute};
                        break;
                }
            }
        }

        return $collection;
    }
}
