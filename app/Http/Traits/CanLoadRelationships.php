<?php

namespace App\Http\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationships
{
    public function loadRelationships(
        Model|QueryBuilder|EloquentBuilder|HasMany $for,
        ?array $relations = null
    ): Model|QueryBuilder|EloquentBuilder|HasMany {
        $relations = $relations ?? $this->relations ?? [];

        // 逐筆查看此relataion使用shouldIncludeRelation查看是否有在$relations，如果有才使用with做關聯
        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn ($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;
    }

    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
    }
}
