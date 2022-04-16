<?php
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use App\Facades\TeamConfig;
use App\Facades\TeamManager;

function abort_in_demo()
{
	if (config('meridian.in_demo')) {
        abort(403, 'You are using demo version of this app.');
    }
}

function admin_id()
{
	return auth()->guard('admin')->id();
}

function getMeridianURL($section = 'app')
{
	$url = null;
	if(inSAASMode()) {
		$url = config('meridian.'.$section.'_prefix').'.'. parse_url(config('app.url'), PHP_URL_HOST);
	} else {
		$path = $section == 'app' ? '/' : $section.'_prefix';
		$url = parse_url(config('app.url'), PHP_URL_HOST).'/'.config('meridian.'.$path);
	}

	if (env('APP_ENV') !== 'local') {
        return 'https://'.$url;
    }

    return 'http://'.$url;
}

function modeSwitcher($prefix, $disablePrefix, $cb)
{
	if(inSAASMode()) {
		$domain = $prefix.'.'. parse_url(config('app.url'), PHP_URL_HOST);
		Route::domain($domain)
			->group(function () use ($cb) {
			call_user_func($cb);
		});
	} else {
		$config = !$disablePrefix ? ['prefix' => $prefix] : [];

		Route::group($config, function () use ($cb) {
			call_user_func($cb);
		});
	}
}

function inSAASMode()
{
	return config('meridian.app_mode') == 'saas';
}

function baseURL()
{
	return !inSAASMode()
		? '/'.config('meridian.admin_prefix')
		: '';
}

function confirm_array($tables)
{
	return !in_array(true, $tables);
}

function check_parent($table, $column, $model)
{
	// true if greater than zero
	return DB::connection('team')
		->table($table)
		->where($column, $model->id)
		->count() > 0;
}

function getBlankLines($items)
{
	$total = config('meridian.pdf.max_row_per_file');
	$len = config('meridian.pdf.char_per_line');
    $totalLines = 0;

    foreach($items as $item) {
    	$desc = $item->item->description;
        $lines = preg_split('/\n|\r/', $desc);

        if(count($lines) > 1) {
        	foreach ($lines as $line) {

        		// if line is longer divide
        		// dd(strlen($line) > $len, strlen($line), $len);
        		if(strlen($line) > $len) {
        			$newC = floor(strlen($line) / $len);
        			$totalLines += ($newC);
        		} else {
        			$totalLines += 0.85;
        		}
        	}
        } else {
        	$totalLines += 1;
        }
    }
    // $c = count($total - $totalLines);
    // dd($totalLines, $items->count(), $totalLines >= $total);
    if($totalLines >= $total) {
        return [];
    } else {
        return range(1, $total - round($totalLines));
    }
}

function get_team()
{
	return TeamManager::getTeam();
}
function if_team_config($key, &$arr, $arrKey, $str)
{
	$value = TeamConfig::get($key);

	if($value) {
		$arr[$arrKey] = $str;
	}
}

function team_config($key)
{
	return TeamConfig::get($key);
}

function team_configs($arr)
{
	return TeamConfig::getMany($arr);
}

function delete_first($message)
{
    return response()->json([
        'message' => $message,
        'errors' => []
    ], 422);
}

function _dd()
{
    array_map(function($x) { var_dump($x); }, func_get_args());
    die;
}

function counter($key)
{
    return App\Tenant\Counter::where('key', $key)
        ->firstOrFail();
}

function formatDate($date)
{
    if($date) {

        $team = TeamManager::getTeam();
        $format = $team->date_format;
        return Carbon\Carbon::parse($date)
            ->format($format);
    }

    return $date;
}

function formatMoneyStripe($value) {
    $amount = formatMoney($value, false);
    $amount = str_replace(',', '', $amount);
    return str_replace('.', '', $amount);
}

function formatMoney($value, $code = true)
{
	$team = TeamManager::getTeam();
	$currency = $team->currency;

	$amount = number_format(
        $value, $currency->precision,
        $currency->decimal_mark, $currency->thousands_separator
    );

    if($code) {
    	if($currency->symbol_first) {
    		return "$currency->code $amount";
    	} else {
    		return "$amount $currency->code";
    	}
    }

    return $amount;
}

function toMoney($value, $currency, $code = true)
{
	$amount = number_format(
        $value, $currency->precision,
        $currency->decimal_mark, $currency->thousands_separator
    );

    if($code) {
    	if($currency->symbol_first) {
    		return "$currency->code $amount";
    	} else {
    		return "$amount $currency->code";
    	}
    }

    return $amount;
}


function parseStringTemplate($html, $vals)
{
    $pattern = '/{+(.*?)}/';

    $replace = preg_replace_callback($pattern, function($match) use ($vals)
    {
        return isset($vals[$match[1]]) ? $vals[$match[1]] : $match[0];
    }, $html);

    return $replace;
}

function fromStripeAmount($amount)
{
    $team = TeamManager::getTeam();
    $currency = $team->currency;

    $l = strlen($amount);
    $p = $l - $currency->precision;

    return substr_replace($amount, '.', $p, 0);
}

function lang()
{
    $path = 'lang/'.config('app.locale').'.js';
    $manifestDirectory = '';
    static $manifests = [];
    if (! Str::startsWith($path, '/')) {
        $path = "/{$path}";
    }
    if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
        $manifestDirectory = "/{$manifestDirectory}";
    }
    if (file_exists(public_path($manifestDirectory.'/hot'))) {
        $url = file_get_contents(public_path($manifestDirectory.'/hot'));
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return new HtmlString(Str::after($url, ':').$path);
        }
        return new HtmlString("//localhost:8080{$path}");
    }
    $manifestPath = public_path($manifestDirectory.'/lang-manifest.json');
    if (! isset($manifests[$manifestPath])) {
        if (! file_exists($manifestPath)) {
            throw new Exception('The Lang manifest does not exist.');
        }
        $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
    }
    $manifest = $manifests[$manifestPath];
    if (! isset($manifest[$path])) {
        report(new Exception("Unable to locate Lang file: {$path}."));
        if (! app('config')->get('app.debug')) {
            return $path;
        }
    }
    return new HtmlString($manifestDirectory.$manifest[$path]);
}