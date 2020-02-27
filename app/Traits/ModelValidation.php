<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

trait ModelValidation
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'create' => [],
        'update' => [],
    ];

    /**
     * Custom messages for validation errors
     *
     * @var array
     */
    public static $error_messages = [];

    /**
     * The labels associated to the attributes
     *
     * @var array
     */
    public static $custom_attrs = [];

    /**
     * Validate the model against the rules based on the action.
     *
     * @param string $action create | update
     *
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return array
     */
    public function validate(string $action)
    {
        if (! in_array($action, ['create', 'update'])) {
            throw new InvalidArgumentException('Invalid action (' . $action . '), allowed "create" or "update"', 422);
        }

        if (empty(static::$rules[$action] ?? [])) {
            throw new InvalidArgumentException('Validation rules not defined to "' . $action . '"', 422);
        }

        return Validator::make(
            $this->getAttributes(),
            static::$rules[$action],
            static::$error_messages,
            static::$custom_attrs
        )->validate();
    }
}
