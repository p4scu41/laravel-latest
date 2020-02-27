<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

/**
 * Inspired by
 *  - https://github.com/Waavi/model
 *  - https://github.com/dwightwatson/validating
 *  - https://github.com/esensi/model
 */
trait ModelValidation
{
    /**
     * Validation rules when saving
     *
     * @var array
     */
    public $validation_rules = [];

    /**
     * Validation rules when updating
     *
     * @var array
     */
    public $validation_rules_update = [];

    /**
     * Custom messages for validation errors
     *
     * @var array
     */
    public $validation_messages = [];

    /**
     * The labels associated to the attributes
     *
     * @var array
     */
    public $validation_custom_attrs = [];

    /**
     * Whether the model should throw a \Illuminate\Validation\ValidationException
     * if it fails validation. If not set, it will default to true.
     *
     * @var bool
     */
    public $throw_validation_exception = true;

    /**
     * Validator instance
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Get the validation rules
     *
     * @return array
     */
    public function getValidatonRules()
    {
        return $this->validation_rules;
    }

    /**
     * Set the validation rules.
     *
     * @param array $validation_rules
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setValidationRules($validation_rules)
    {
        $this->validation_rules = $validation_rules;

        return $this;
    }

    /**
     * Adds or overwrites a validation rule.
     *
     * @param string $field
     * @param string $validation_ruleset
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setValidationRule($field, $validation_ruleset)
    {
        $this->validation_rules[$field] = $validation_ruleset;

        return $this;
    }

    /**
     * Removes a validation rule.
     *
     * @param string $field
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function removeValidationRule($field)
    {
        unset($this->validation_rules[$field]);

        return $this;
    }

    /**
     *Get the validation error messages from the model.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->validator ? $this->validator->errors() : new MessageBag();
    }

    /**
     * Validate the model against the rules based on the action.
     *
     * @return bool
     */
    public function isValid()
    {
        $this->makeValidator();

        return $this->validator->passes();
    }


    /**
     * Make a Validator instance with the ruleset.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function makeValidator()
    {
        $rules = $this->validation_rules;

        if ($this->exists) {
            $rules = array_merge($rules, $this->validation_rules_update);
            $rules = $this->injectIdToUniqueRules($rules);
        }

        $this->validator = Validator::make(
            $this->getAttributes(),
            $rules,
            $this->validation_messages,
            $this->validation_custom_attrs
        );

        return $this->validator;
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    public function injectIdToUniqueRules($rules)
    {
        foreach ($rules as $field => &$ruleset) {
            // If the ruleset is a pipe-delimited string, convert it to an array.
            $ruleset = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($ruleset as &$rule) {
                // Only treat stringy definitions and leave Rule classes and Closures as-is.
                // Seek unique validation rules so that when updating a model the constraint is not applied to itself
                if (!is_string($rule) || !Str::startsWith($rule, 'unique')) {
                    continue;
                }

                // unique:table,column,ignore,idColumn
                $params = explode(',', str_replace(['unique', ':'], '', $rule));

                $params[0] = $params[0] ?? $this->getTable();
                $params[1] = $params[1] ?? $field;
                $params[2] = $params[2] ?? $this->getKey();
                $params[3] = $params[3] ?? $this->getKeyName();

                $rule = 'unique:' . implode(',', $params);
            }
        }

        return $rules;
    }

    /**
     * Save the model to the database.
     *
     * @param array $options
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return bool
     */
    public function save($options = [])
    {
        if ($this->throw_validation_exception) {
            $this->makeValidator();
            $this->validator->validate();
        }

        if (! $this->isValid()) {
            return false;
        }

        return parent::save($options);
    }

    /**
     * Save the model to the database without validation.
     *
     * @param array $options
     *
     * @return bool
     */
    public function forceSave($options = [])
    {
        return parent::save($options);
    }
}
