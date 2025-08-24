<?php

namespace VdubDev\CsvPreviewConvert;

/**
 * Enum representing character encodings.
 *
 * This enum can be used for database storage, forms, and
 * programmatic checks on encoding compatibility.
 */
enum CharsetEnum: string
{
    case UTF_8 = 'utf-8';
    case WINDOWS_1252 = 'windows-1252';
    case ISO_8859_1 = 'iso-8859-1';
    case ISO_8859_15 = 'iso-8859-15';

    /**
     * Returns a human-readable label for the encoding.
     */
    public function label(): string
    {
        return match ($this) {
            self::UTF_8 => 'UTF-8',
            self::WINDOWS_1252 => 'Windows 1252',
            self::ISO_8859_1 => 'ISO-8859-1',
            self::ISO_8859_15 => 'ISO-8859-15',
        };
    }

    /**
     * Returns an array suitable for Symfony ChoiceType forms.
     *
     * The keys are human-readable labels, and the values are enum cases.
     *
     * Example usage in a Symfony form:
     *   $builder->add('encoding', ChoiceType::class, [
     *       'choices' => EncodingEnum::choices()
     *   ]);
     *
     * @return array<string, CharsetEnum>
     */
    public static function choices(): array
    {
        $choices = [];

        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case;
        }

        return $choices;
    }
}
