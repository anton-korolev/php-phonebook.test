<?php

declare(strict_types=1);

namespace common\helpers;

class FormField
{
    static function render(
        string $formName,
        string $fieldName,
        string $label,
        array|null $values = null,
        bool $required = false,
        int $maxLength = 0,
        array|null $errors = null
    ): string {
        // $error = $error ?? '';
        $class = (empty($errors)) ? 'form-control' : 'form-control is-invalid';
        $maxLength = ($maxLength > 0) ?  "maxlength=\"$maxLength\"" : '';
        $aria = $required ? ' aria-required="true"' : '';
        $aria = $aria . ((empty($errors)) ? '' : ' aria-invalid="true"');
        $required = $required ? ' required' : '';
        if (isset($values[$fieldName])) {
            $value = htmlspecialchars($values[$fieldName]);
            $value = " value=\"$value\"";
        } else {
            $value = '';
        }
        $errorText = '';
        foreach ($errors ?? [] as $error) {
            $errorText .= "<div class=\"invalid-feedback\">$error</div>\n";
        }
        $errorText = ('' === $errorText) ? '<div class="invalid-feedback"></div>' : $errorText;

        return
            <<<"END"
            <div class="mb-3 field-$formName-$fieldName$required">
                            <label class="form-label" for="$formName-$fieldName">$label</label>
                            <input type="text" id="$formName-$fieldName" class="$class" name="{$formName}[{$fieldName}]"$value$maxLength$aria>
                            $errorText
                        </div>
            END;
        // <input type="text" id="$formName-$fieldName" class="$class" name="{$formName}[{$fieldName}]"$maxLength$aria>

    }
}
