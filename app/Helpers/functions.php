<?php

use App\Exceptions\InvalidPhoneNumberExceptionAbstract;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{App, Auth, DB, Log};
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

if (!function_exists("null_if_empty")) {
    /**
     * Return null if the value is empty
     *
     * @param $value
     * @return null
     */
    function null_if_empty($value)
    {
        if ((empty($value) || $value === "null") && !is_zero($value) && $value !== false) {
            return null;
        }

        return $value;
    }
}

if (!function_exists("array_normalize")) {
    /**
     * Normalize the data to array
     *
     * @param $value
     * @return array
     */
    function array_normalize($value)
    {
        if (empty($value)) {
            return [];
        }

        if (!is_array($value)) {
            return [$value];
        }

        return $value;
    }
}

if (!function_exists("string_value")) {
    /**
     * Get the string value of the value
     *
     * @param $value
     * @return ?string
     */
    function string_value($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            if (method_exists($value, 'label')) {
                return $value->label();
            }

            return $value->value;
        }

        return value($value);
    }
}

if (!function_exists("enum_or_value")) {
    /**
     * Get the string value of the value
     *
     * @param $value
     * @return ?string
     */
    function enum_or_value($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return value($value);
    }
}

if (!function_exists("array_cast")) {
    /**
     * Normalize the data to array
     *
     * @param $value
     * @return array
     */
    function array_cast($value)
    {
        if (is_array($value)) {
            return $value;
        }

        try {
            if (is_string($value)) {
                return json_decode($value, true);
            }
        } catch (Throwable $e) {
        }

        return [];
    }
}

if (!function_exists("boolean_normalize")) {
    /**
     * Normalize boolean if necessary
     *
     * @param $value
     * @return mixed
     */
    function boolean_normalize($value)
    {
        if (in_array($value, ['true', 'false'])) {
            return $value == "true";
        }

        if (in_array($value, ['true', 'false'])) {
            return $value == "true";
        }

        return $value;
    }
}

if (!function_exists('is_zero')) {
    /**
     * Check if a value is zero
     *
     * @param $value
     * @return bool
     */
    function is_zero($value)
    {
        return $value === 0 || $value === "0";
    }
}

if (!function_exists("bc_equals")) {
    /**
     * Check if the values are equals
     *
     * @param $left
     * @param $right
     * @param $scale
     * @return bool
     */
    function bc_equals($left, $right, $scale = 2)
    {
        return bccomp($left, $right, $scale) == 0;
    }
}

if (!function_exists("bc_less_than")) {
    /**
     * Check if the left value is less than the right value
     *
     * @param $left
     * @param $right
     * @param $scale
     * @return bool
     */
    function bc_less_than($left, $right, $scale = 2)
    {
        return bccomp($left, $right, $scale) == -1;
    }
}

if (!function_exists("money_formatter")) {
    /**
     * Format a number in money format
     *
     * @param $float
     * @param int $decimals
     * @return string
     */
    function money_formatter($float, int $decimals = 2)
    {
        return number_format($float, $decimals, ',', '.');
    }
}

if (!function_exists("money_normalizer")) {
    /**
     * Normalize a number in money format
     *
     * @param $value
     * @return float
     */
    function money_normalizer($value)
    {
        $value = str_replace('R$', '', $value);
        return (float)str_replace(',', '.', $value);
    }
}

if (!function_exists("br_date_formatter")) {
    /**
     * Format a date in BR style
     *
     * @param $date
     * @return string
     */
    function br_date_formatter($date)
    {
        return Carbon::createFromFormat("Y-m-d", $date)->format("d/m/Y");
    }
}

if (!function_exists("br_datetime_formatter")) {
    /**
     * Format a datetime in BR style
     *
     * @param $date
     * @return string
     */
    function br_datetime_formatter($date)
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $date)->format("d/m/Y H:i:s");
    }
}

if (!function_exists("phone_format_E164")) {
    /**
     * Format a phone number in E164
     *
     * @param $phoneNumber
     * @param string $country
     * @return string
     * @throws NumberParseException
     */
    function phone_format_E164($phoneNumber, $country = "BR")
    {
        try {
            return PhoneNumber::make(strip_text($phoneNumber), $country)->formatE164();
        } catch (Exception $exception) {
            Log::warning("process=phone_format_E164, status=error", compact('phoneNumber', 'exception'));

            throw new InvalidPhoneNumberExceptionAbstract();
        }
    }
}

if (!function_exists("strip_text")) {
    /**
     * Remove everything except numbers
     *
     * @param string $string
     *
     * @return string
     */
    function strip_text($string)
    {
        return preg_replace("/\D/", "", $string);
    }
}

if (!function_exists("phone_format")) {
    /**
     * Format a phone number
     *
     * @param $phoneNumber
     * @return string
     */
    function phone_format($phoneNumber)
    {
        if (empty($phoneNumber) || strlen($phoneNumber) <= 8) {
            return null;
        }

        $parameterService = resolve(SystemParameterService::class);
        $areaCode         = $parameterService->getCellphoneDefaultAreaCode();

        $phoneNumber = preg_replace("/\D/", "", $phoneNumber);

        if (strlen($phoneNumber) < 10) {
            $phoneNumber = $areaCode . $phoneNumber;
        }

        return $phoneNumber;
    }
}

if (!function_exists('estimation_time_from_seconds')) {
    /**
     * Format seconds in minutes or an hour
     *
     * @param $seconds
     * @return string
     */
    function estimation_time_from_seconds($seconds)
    {
        if ($seconds < 120) {
            return "1 minuto";
        } else if ($seconds < 3600) {
            return floor($seconds / 60) . " minutos";
        }

        return "1 hora";
    }
}

if (!function_exists("phone_variations")) {
    /**
     * Format a phone number
     *
     * @param $phoneNumber
     * @return array
     */
    function phone_variations($phoneNumber): array
    {
        $parameterService = resolve(SystemParameterService::class);
        $areaCode         = $parameterService->getCellphoneDefaultAreaCode();

        $phoneNumber = Str::of($phoneNumber)->replaceFirst("+55", "");
        $phoneNumber = preg_replace("/\D/", "", $phoneNumber);

        $result = [$phoneNumber];
        $length = strlen($areaCode);

        $result[] = strlen($phoneNumber) >= 10
            ? substr($phoneNumber, $length, strlen($phoneNumber) - $length)
            : $areaCode . $phoneNumber;

        return $result;
    }
}

if (!function_exists("is_app_user")) {
    /**
     * Check if is app user
     *
     * @return bool
     */
    function is_app_user()
    {
        return Auth::getDefaultDriver() == 'app';
    }
}

if (!function_exists("array_round")) {
    /**
     * Round all the values from the array
     *
     * @param array $values
     * @return array
     */
    function array_round(array $values, $precision = 2)
    {
        foreach ($values as $key => $value) {
            $values[$key] = round($value, 2);
        }

        return $values;
    }
}

if (!function_exists("array_money_formatter")) {
    /**
     * Format all the values in the array as money
     *
     * @param array $values
     * @return array
     */
    function array_money_formatter(array $values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = money_formatter($value);
        }

        return $values;
    }
}

if (!function_exists("is_production")) {
    /**
     * Check if it is running in production environment
     *
     * @return bool
     */
    function is_production()
    {
        return App::environment() === "production";
    }
}

if (!function_exists("query_relationship")) {
    /**
     * Helper function to create relationships between models
     *
     * @param $fields
     * @param array $with
     * @param null $orderBy
     * @return Closure
     */
    function query_relationship($fields, $with = [], $orderBy = null)
    {
        return function (Relation $query) use ($fields, $with, $orderBy) {
            $query->select($fields);
            $query->with($with);

            if ($orderBy) {
                [$column, $direction] = explode(":", $orderBy);
                $query->orderBy($column, $direction);
            }
        };
    }
}

if (!function_exists("query_group_by_date_format")) {
    /**
     * Helper function to create query variations with date formats
     *
     * @param string $format
     * @param string $alias
     * @param string $column
     * @param bool $useMinOrderBy
     * @return Closure
     */
    function query_group_by_date_format($format, $alias = 'date', $column = 'created_at', $useMinOrderBy = true)
    {
        return function (Builder $builder) use ($format, $alias, $column, $useMinOrderBy) {
            $dateFormat = "DATE_FORMAT($column, '$format')";
            $builder
                ->selectRaw("$dateFormat as $alias")
                ->groupBy(DB::raw($dateFormat))
                ->orderBy(DB::raw($useMinOrderBy ? "min($column)" : $dateFormat));
        };
    }
}

if (!function_exists("query_filter_boolean_nullable")) {
    /**
     * Helper function to create query filter where the value indicates whether or not the field is nullable
     *
     * @param string $field
     * @return Closure
     */
    function query_filter_boolean_nullable(string $field)
    {
        return function (Builder $builder, $value) use ($field) {
            if ($value === "1" || $value === 1) {
                $builder->whereNotNull($field);
            } else if ($value === "0" || $value === 0) {
                $builder->whereNull($field);
            }
        };
    }
}

if (!function_exists("normalize_words")) {
    /**
     * Normalize words with ucwords and strtolower
     *
     * @param string $string
     * @return string
     */
    function normalize_words($string)
    {
        return ucwords(mb_strtolower($string, "UTF-8"));
    }
}


if (!function_exists("random_id")) {
    /**
     * Return a random integer id
     * @return int
     */
    function random_id(): int
    {
        return rand(1, 99999999);
    }
}

if (!function_exists("random_ids")) {
    /**
     * Return an array of integer ids
     * @param int $amount
     * @return array
     */
    function random_ids($amount): array
    {
        return Collection::times($amount, function () {
            return random_id();
        })->all();
    }
}

if (!function_exists("random_uuids")) {
    /**
     * Return an array of UUIDs
     * @param int $amount
     * @return array
     */
    function random_uuids($amount): array
    {
        return Collection::times($amount, function () {
            return Str::uuid();
        })->all();
    }
}

if (!function_exists("cors_headers")) {
    /**
     * Cors headers
     *
     * @return array
     */
    function cors_headers()
    {
        return [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization, X-Client-Secret-Key',
        ];
    }
}

if (!function_exists("seconds_to_time")) {
    /**
     * Convert seconds to time
     *
     * @param string $time
     * @return string
     */
    function seconds_to_time($time)
    {
        return gmdate('H:i:s', $time);
    }
}

if (!function_exists("add_time")) {
    /**
     * Add 2 times and returns the sum
     *
     * @param string $time1
     * @param string $time2
     * @return string
     */
    function add_time(string $time1, string $time2)
    {
        return Carbon::createFromTimeString($time1)->addSeconds(
            Carbon::createFromTimeString($time2)->secondsSinceMidnight()
        )->toTimeString();
    }
}

if (!function_exists("positive_number")) {
    /**
     * Make sure the number is always positive
     *
     * @param string $number
     * @return float
     */
    function positive_number($number)
    {
        return $number > 0 ? $number : 0;
    }
}

if (!function_exists("query_filter_by_cost_center_tree")) {
    /**
     * Filter query by cost center tree
     *
     * @return callable
     */
    function query_filter_by_cost_center_tree()
    {
        return function (Builder $builder, $value) {
            /** @var CostCenterService $costCenterService */
            $costCenterService = resolve(CostCenterService::class);

            $tree = $costCenterService->getTreeByCostCenterId($value);

            $builder->whereIn('cost_center_id', $tree->getIds());
        };
    }
}

if (!function_exists("ifactory")) {
    /**
     * Factory for integration models
     *
     * @param string $class
     * @param int $count
     *
     * @return \Illuminate\Database\Eloquent\FactoryBuilder
     */
    function ifactory($class, $count = null)
    {
        return factory($class, 'integration', $count);
    }
}

if (!function_exists("factory_unset_keys")) {
    /**
     * Try to retrieve a config or fail if not found
     *
     * @param $keys
     * @return callable
     */
    function factory_unset_keys($keys = 'id'): callable
    {
        $keys = (array)$keys;

        return function (Model $model) use ($keys) {
            $model->setRawAttributes(
                Arr::except($model->getAttributes(), $keys),
                true
            );

            return $model;
        };
    }
}

if (!function_exists("normalize_endpoint")) {
    /**
     * Normalize the / in URL endpoint and path
     *
     * @param string $host
     * @param string $path
     * @return string
     */
    function normalize_endpoint(string $host, string $path): string
    {
        if (Str::of($host)->endsWith("/")) {
            $host = Str::of($host)->replaceLast("/", "");
        }

        return $host . Str::start($path, "/");
    }
}


if (!function_exists("bff_template")) {
    /**
     * Load a BFF array template
     *
     * @param string $path
     * @return array
     */
    function bff_template(string $path): array
    {
        return include(resource_path("bff/{$path}.php"));
    }
}


if (!function_exists("str_replacements")) {
    /**
     * Make replacements in a string
     *
     * @param string $line
     * @param array $replacements
     * @return string
     */
    function str_replacements(string $line, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $line = str_replace(':' . $key, $value, $line);
        }

        return $line;
    }
}

if (!function_exists("stringify")) {
    function stringify($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        if ($value instanceof JsonSerializable) {
            return json_encode($value->jsonSerialize());
        }
        if (is_null($value)) {
            return "null";
        }
        if (
            (is_object($value) && method_exists($value, '__toString'))
            || is_scalar($value)
            || $value instanceof Stringable
        ) {
            return $value;
        }
        if (property_exists($value, 'value')) {
            return $value->value;
        }
        return "Unable to stringify object";
    }
}


if (!function_exists("invoke")) {
    /**
     * Invoke a class with parmas
     *
     * @param string $class
     * @param $params
     * @return mixed
     */
    function invoke(string $class, ...$params)
    {
        return call_user_func_array(resolve($class), $params);
    }
}

if (!function_exists("transformInt")) {
    /**
     * Safely converts a value to Int
     *
     * @param $value
     * @param null $default
     * @return mixed|null
     */
    function transformInt($value, $default = null)
    {
        return transform($value, 'intval', $default);
    }
}

if (!function_exists("transformBool")) {
    /**
     * Safely converts a value to Boolean
     *
     * @param $value
     * @param null $default
     * @return mixed|null
     */
    function transformBool($value, $default = null)
    {
        return transform($value, 'boolval', $default);
    }
}

if (!function_exists("transformAscii")) {
    /**
     * Safely converts a value to Ascii string
     *
     * @param $value
     * @param null $default
     * @return string|null
     */
    function transformAscii($value, $default = null)
    {
        return transform($value, fn($v) => Str::ascii($value), $default);
    }
}

if (!function_exists("transformFloat")) {
    /**
     * Safely converts a value to Float
     *
     * @param $value
     * @param null $default
     * @return mixed|null
     */
    function transformFloat($value, $default = null)
    {
        return transform($value, 'floatval', $default);
    }
}

if (!function_exists("transformDate")) {
    /**
     * Safely converts a value to a Carbon instace
     *
     * @param $value
     * @param null $default
     * @return mixed|null
     */
    function transformDate($value, $default = null)
    {
        return transform($value, fn($val) => Carbon::parse($value), $default);
    }
}

if (!function_exists("transformDateImmutable")) {
    /**
     * Safely converts a value to a CarbonImmutable instace
     *
     * @param $value
     * @param null $default
     * @return mixed|null
     */
    function transformDateImmutable($value, $default = null)
    {
        return transform($value, fn($val) => CarbonImmutable::parse($value), $default);
    }
}

if (!function_exists("isTrue")) {
    /**
     * Checks if a value is true
     *
     * @param $value
     * @return bool
     */
    function isTrue($value)
    {
        return $value === 1 || $value === "1" || $value === true || $value === "true";
    }
}

if (!function_exists("isFalse")) {
    /**
     * Checks if a value is false
     *
     * @param $value
     * @return bool
     */
    function isFalse($value)
    {
        return $value === 0 || $value === "0" || $value === false || $value === "false";
    }
}
