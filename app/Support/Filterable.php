<?php

namespace App\Support;

use App\Support\QueryBuilder;
use Illuminate\Validation\ValidationException;
use DB;
use Carbon\Carbon;
use App\Tenant\Filter as TenantFilter;
use App\Admin\Filter as AdminFilter;

trait Filterable {

	use Metric;

	protected $defaultSortColumn = 'created_at';
    protected $defaultSortDirection = 'desc';

    public function scopeTypeahead($query, $columns, $required)
    {
        return $query->when(request('query'), function($query) use ($columns) {
                foreach($columns as $column) {
                    $query->orWhere($column, 'like', '%'.request('query').'%');
                }
            })
            ->limit(10)
            ->get($required);
    }

    public function scopeSearch($query)
    {
        $this->validate(request()->all(), [
            'column' => 'required|not_in:'.$this->nonSearchable()
        ]);

        return $query->select('id',request('column'), DB::Raw(request('column'). ' as value'))
            ->orderBy(request('column'))
            ->whereNotNull(request('column'))
            ->when(request('query'), function($q) {
                $q->where(request('column'), 'like', '%'.request('query').'%');
            })
            ->limit(10)
            ->get();
    }

    public function scopeServerExport($query, $filter)
    {
        $request = $request = [
            'f' => $filter->getParams(),
            'filter_match' => $filter->filter_match
        ];

        $response = $this->process($request, $query);
        $response = $query->orderBy(
            request('sort_column', $this->defaultSortColumn),
            request('sort_direction', $this->defaultSortDirection)
        )->get();

        return $response;
    }

    public function scopeExport($query, $cols = null)
    {
        $request = request()->all(); // change
        $response = $this->process($request, $query);
        $response = $query->orderBy(
            request('sort_column', $this->defaultSortColumn),
            request('sort_direction', $this->defaultSortDirection)
        )->get();

        return $response;
    }

	public function scopeFilter($query, $cols = null)
	{
        $request = request()->all();

        $filter = null;

        if(request()->has('filter_id')) {
        	if(auth()->guard('admin')->check()) {
        		$filter = AdminFilter::findOrFail(request('filter_id'));
        	} else {
        		$filter = TenantFilter::findOrFail(request('filter_id'));
        	}

            $request = [
                'f' => $filter->getParams(),
                'filter_match' => $filter->filter_match
            ];
        }

        $response = $this->process($request, $query);

		$response = $query->orderBy(
            request('sort_column', $this->defaultSortColumn),
            request('sort_direction', $this->defaultSortDirection)
        );

        $result = $response->paginate(
            request('limit', config('meridian.per_page')), $cols
        )->toArray();


        $result['filter'] = $filter;

        return $result;
	}

	protected function process($data, $query)
    {
        $this->validate($data, [
            'sort_column' => 'sometimes|required|not_in:'.$this->nonSortable(),
            'sort_direction' => 'sometimes|required|in:asc,desc',

            'limit' => 'sometimes|required|integer|min:1',
            'page' => 'sometimes|required|integer|min:1',

            // advanced multi-column filter
            'filter_match' => 'sometimes|required|in:and,or',
            'f' => 'sometimes|required|array',
            'f.*.column' => 'required|not_in:'.$this->nonFilterable(),
            'f.*.operator' => 'required_with:f.*.column|in:'.$this->allowedOperators(),
            'f.*.query_1' => 'required_unless:f.*.operator,is_empty,is_not_empty',
            'f.*.query_2' => 'required_if:f.*.operator,between,between_date',
        ]);

        return (new QueryBuilder)->apply($query, $data);
    }

    protected function validate($data, $rules)
    {
        $v = validator()->make($data, $rules);

        if($v->fails()) {
            if (env('APP_ENV') == 'local') {
                // dd($v->messages()->all());
            }

            throw new ValidationException($v);
        }
    }

    protected function nonSortable()
    {
    	if(property_exists($this, 'nonSortable')) {
    		return implode(',', $this->nonSortable);
    	}

    	return '';
    }

    protected function nonFilterable()
    {
    	if(property_exists($this, 'nonFilterable')) {
    		return implode(',', $this->nonFilterable);
    	}

    	return '';
    }

    protected function nonSearchable()
    {
        if(property_exists($this, 'nonSearchable')) {
            return implode(',', $this->nonSearchable);
        }

        return '';
    }

	protected function allowedOperators()
    {
        return implode(',', [
            'equal_to',
            'not_equal_to',
            'less_than',
            'greater_than',
            'less_than_or_equal_to',
            'greater_than_or_equal_to',
            'between',
            'not_between',
            'includes',
            'not_includes',
            'is_empty',
            'is_not_empty',
            'contains',
            'starts_with',
            'ends_with',
            'in_the_past',
            'in_the_next',
            'in_the_peroid',
            'over',
            'between_date',
            'equal_to_date',
            'toggle'
        ]);
    }
}