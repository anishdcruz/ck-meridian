<?php

namespace App\Support;

trait HasManyRelation {

    public function storeHasMany($relations)
    {
        $this->save();

        foreach($relations as $relation => $items) {
            $list = [];
            foreach($items as $item) {
                $list[] = $this->{$relation}()->getModel()
                    ->fill($item);
            }
            $this->{$relation}()
                ->saveMany($list);
        }
    }

    public function updateHasMany($relations)
    {
            $this->save();
            foreach($relations as $relation => $items) {
                $list = [];
                $updates = [];
                foreach($items as $item) {
                    $model = $this->{$relation}()->getModel();
                    $localKey = $model->getKeyName();
                    $foreginKey = $this->{$relation}()->getForeignKeyName();
                    $parentId = $this->getAttribute($this->getKeyName());

                    if(isset($item[$localKey])) {
                        $localId = $item[$localKey];
                        $found = $model->where($foreginKey, $parentId)
                            ->where($localKey, $localId)
                            ->first();

                        if($found) {
                            $found->fill($item);
                            $found->save();

                            $updates[] = $localId;
                        }
                    } else {
                        $list[] = $model->fill($item);
                    }
                }

                // delete
                $model = $this->{$relation}()->getModel();
                $localKey = $model->getKeyName();
                $foreginKey = $this->{$relation}()->getForeignKeyName();
                $parentId = $this->getAttribute($this->getKeyName());
                $model->whereNotIn($localKey, $updates)
                    ->where($foreginKey, $parentId)
                    ->delete();

                // save new
                if(count($list)) {
                    $this->{$relation}()
                        ->saveMany($list);
                }
            }
    }
}
