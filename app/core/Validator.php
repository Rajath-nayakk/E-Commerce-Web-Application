<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Class Validator
 * 
 * Elegant data validation engine for forms and API requests.
 * Generates user-friendly error messages and prevents bad inputs.
 */
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $ruleParams = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $ruleParams = explode(',', $paramStr);
                }

                $this->applyRule($field, $value, $rule, $ruleParams);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
                    $this->addError($field, "{$label} is required.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "{$label} must be a valid email address.");
                }
                break;

            case 'min':
                $min = (int) ($params[0] ?? 0);
                if (is_numeric($value)) {
                    if ((float) $value < $min) {
                        $this->addError($field, "{$label} must be at least {$min}.");
                    }
                } elseif (is_string($value) && mb_strlen($value) < $min) {
                    $this->addError($field, "{$label} must be at least {$min} characters.");
                }
                break;

            case 'max':
                $max = (int) ($params[0] ?? 0);
                if (is_numeric($value)) {
                    if ((float) $value > $max) {
                        $this->addError($field, "{$label} may not be greater than {$max}.");
                    }
                } elseif (is_string($value) && mb_strlen($value) > $max) {
                    $this->addError($field, "{$label} may not exceed {$max} characters.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "{$label} must be a number.");
                }
                break;

            case 'integer':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, "{$label} must be an integer.");
                }
                break;

            case 'matches':
                $otherField = $params[0] ?? '';
                $otherLabel = ucfirst(str_replace('_', ' ', $otherField));
                $otherValue = $this->data[$otherField] ?? null;
                if ($value !== $otherValue) {
                    $this->addError($field, "{$label} does not match {$otherLabel}.");
                }
                break;

            case 'in':
                if ($value !== null && $value !== '' && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "{$label} is invalid. Allowed values: " . implode(', ', $params));
                }
                break;

            case 'unique':
                // unique:table,column[,ignore_id]
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $ignoreId = $params[2] ?? null;

                if ($value !== null && $value !== '' && $table !== '') {
                    $sql = "SELECT id FROM `{$table}` WHERE `{$column}` = :val";
                    $queryParams = ['val' => $value];
                    if ($ignoreId !== null) {
                        $sql .= " AND id != :ignore_id";
                        $queryParams['ignore_id'] = $ignoreId;
                    }

                    $exists = Database::getInstance()->fetchOne($sql, $queryParams);
                    if ($exists) {
                        $this->addError($field, "{$label} is already registered or taken.");
                    }
                }
                break;
        }
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(?string $field = null): ?string
    {
        if ($field !== null) {
            return $this->errors[$field][0] ?? null;
        }
        $firstField = array_key_first($this->errors);
        return $firstField !== null ? $this->errors[$firstField][0] : null;
    }
}
