<?php

namespace VdubDev\CsvPreviewConvert\Enum;

/**
 * Enum representing character encodings.
 *
 * This enum can be used for database storage, forms, and
 * programmatic checks on encoding compatibility.
 */
enum CharsetEnum: string
{
    case UTF_8 = 'UTF-8';
    case WINDOWS_1252 = 'Windows-1252';
    case ISO_8859_15 = 'ISO-8859-15';
    case ISO_8859_1 = 'ISO-8859-1';

    /**
     * Returns a human-readable label for the encoding.
     */
    public function label(): string
    {
        return match ($this) {
            self::UTF_8 => '1. UTF-8',
            self::WINDOWS_1252 => '2. Windows-1252',
            self::ISO_8859_15 => '3. ISO-8859-15',
            self::ISO_8859_1 => '4. ISO-8859-1',
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

    /**
     * Returns true if the given encoding string is a valid enum value.
     */
    public static function isValid(string $encoding): bool
    {
        return in_array($encoding, array_map(fn ($case) => $case->value, self::cases()), true);
    }

    /**
     * Returns a valid CharsetEnum value; defaults to UTF-8 if invalid.
     */
    public static function normalize(string $encoding): string
    {
        return self::isValid($encoding) ? $encoding : self::UTF_8->value;
    }
}
