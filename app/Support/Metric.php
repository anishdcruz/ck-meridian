<?php

namespace App\Support;

use DB;
use Carbon\Carbon;

trait Metric {
	public function scopeMetrics($query, $filter, $item)
    {
        $request = [
            'f' => $filter->getParams(),
            'filter_match' => $filter->filter_match
        ];

        $response = $this->process($request, $query);

        if($item->metric_type == 'value') {
            if($item->value_type == 'count') {
                return $response->count();
            }

            if($item->value_type == 'sum') {
                return $response->sum($item->value_field) ?? 0;;
            }

            if($item->value_type == 'avg') {
                return $response->avg($item->value_field) ?? 0;
            }
        } else if($item->metric_type == 'chart') {
        	if($item->chart_type == 'pie' || $item->chart_type == 'doughnut') {
        		// get all items

        		if (strpos($item->x_axis_field, '.') !== false) {
	        		$data = explode('.', $item->x_axis_field);
	        		$relation = $data[0];
	        		$res = $response
	        			->with($relation)
			            ->select(
			            	'*',
			                $this->getYAxis($item->y_axis_type, $item->y_axis_field)
			                // $this->getXAxis(null, $item->x_axis_field)
			            )
			            ->groupBy($relation.'_id')
			            ->orderBy($relation.'_id')
			            ->get()
			            ->pluck('y_axis', $item->x_axis_field)
			            ->toArray();
        		} else {
	        		$res = $response
			            ->select(
			                $this->getYAxis($item->y_axis_type, $item->y_axis_field),
			                $this->getXAxis(null, $item->x_axis_field)
			            )
			            ->groupBy('x_axis')
			            ->orderBy($this->getOrderBy($item->x_axis_type, $item->x_axis_field))
			            ->pluck('y_axis', 'x_axis')
			            ->toArray();
        		}
        	} else {
	            $res = $response
		            ->select(
		                $this->getYAxis($item->y_axis_type, $item->y_axis_field),
		                $this->getXAxis($item->x_axis_type, $item->x_axis_field)
		            )
		            ->groupBy('x_axis')
		            ->orderBy($this->getOrderBy($item->x_axis_type, $item->x_axis_field))
		            ->pluck('y_axis', 'x_axis')
		            ->toArray();
        	}

            $data = array_values($res);
            $labels = array_keys($res);

            return [
                'data' => $data,
                'labels' => $labels
            ];
        }

    }

    public function getOrderBy($type, $field)
    {
    	if($type == 'monthly') {
            return DB::raw('DATE_FORMAT('.$field.', "%m")');
        }
        if($type == 'weekly') {
            return DB::raw('DATE_FORMAT('.$field.', "%w")');
        }
        if($type == 'daily') {
            return DB::raw('DATE_FORMAT('.$field.', "%d")');
        }
        return $field;
    }

    public function getYAxis($type, $field)
    {
        if($type == 'sum') {
            return DB::raw('sum('.$field.') as y_axis');
        }
        if($type == 'avg') {
            return DB::raw('avg('.$field.') as y_axis');
        }

        return DB::raw('count(*) as y_axis');
    }

    protected function getXAxis($type, $field)
    {
        if($type == 'yearly')
        {
            return DB::raw('DATE_FORMAT('.$field.', "%Y") as x_axis');
        }
        if($type == 'monthly')
        {
            return DB::raw('DATE_FORMAT('.$field.', "%b") as x_axis');
        }
        if($type == 'weekly')
        {
            return DB::raw('DATE_FORMAT('.$field.', "%W") as x_axis');
        }
        if($type == 'daily')
        {
            return DB::raw('DATE_FORMAT('.$field.', "%D") as x_axis');
        }

        return DB::raw($field.' as x_axis');
    }
}