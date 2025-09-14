<?php

namespace App\Common\FormRequest;

use App\Common\FormRequest\DataTypes\Optional;
use App\Helpers\Date\DateInterval;
use Carbon\CarbonImmutable;

trait HasTypedFields
{
    /**
     * Retrieve an INTEGER input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?int
     */
    public function inputInt($key = null, $default = null): ?int
    {
        return transformInt($this->input($key), $default);
    }

    /**
     * Retrieve an FLOAT input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?float
     */
    public function inputFloat($key = null, $default = null): ?float
    {
        return transformFloat($this->input($key), $default);
    }

    /**
     * Retrieve an Date input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?Carbon
     */
    public function inputDate($key = null, $default = null): ?Carbon
    {
        return transformDate($this->input($key), $default);
    }

    /**
     * Retrieve an Date input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?CarbonImmutable
     */
    public function inputDateImmutable($key = null, $default = null): ?CarbonImmutable
    {
        return transformDateImmutable($this->input($key), $default);
    }

    /**
     * Retrieve an DateInterval input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?DateInterval
     */
    public function inputDateInterval($key = null, $default = null): ?DateInterval
    {
        return transform($this->input($key), fn(array $values) => new DateInterval(
            from : CarbonImmutable::parse($values[0]),
            until: CarbonImmutable::parse($values[1]),
        ));
    }

    /**
     * Retrieve an Date input item from the request.
     *
     * @param string|null $key
     * @param mixed $default
     * @return ?CarbonImmutable
     */
    public function inputEnum($key = null, $enumClass = null): mixed
    {
        return transform($this->input($key), fn($val) => forward_static_call([$enumClass, 'from'], $val));
    }

    /**
     * Retrieve a Optional field input
     *
     * @param string|null $key
     * @return Optional
     */
    public function inputOptional($key = null): Optional
    {
        if (!$this->has($key)) {
            return Optional::empty();
        }

        return Optional::from($this->input($key));
    }

    /**
     * Retrieve a Optional field input
     *
     * @param string|null $key
     * @return mixed
     */
    public function inputNullable(string $key): mixed
    {
        return transform($this->input($key), null_if_empty(...));
    }
}
