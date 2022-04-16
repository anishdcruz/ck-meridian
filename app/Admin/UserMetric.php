<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Filter;

class UserMetric extends Model
{
	protected $connection = 'mysql';
    protected $fillable = [
		'card_label',
		'filter_id',
		'metric_type',
		'chart_type',
		'color',
		'value_type',
		'value_field',
		'x_axis_type',
		'y_axis_type',
		'x_axis_field',
		'y_axis_field',
		'i', 'x', 'y', 'h', 'w'
	];

    public function filter()
    {
    	return $this->belongsTo(Filter::class, 'filter_id', 'id');
    }
}
