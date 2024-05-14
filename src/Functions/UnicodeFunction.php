<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class UnicodeFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    /*private const CLASS_UNI_CODES = [
        'Arabic', 'Armenian', 'Avestan', 'Balinese', 'Bamum', 'Batak', 'Bengali', 'Bopomofo', 'Brahmi', 'Braille',
        'Buginese', 'Buhid', 'Canadian_Aboriginal', 'Carian', 'Chakma', 'Cham', 'Cherokee', 'Common', 'Coptic',
        'Cuneiform', 'Cypriot', 'Cyrillic', 'Deseret', 'Devanagari', 'Egyptian_Hieroglyphs', 'Ethiopic', 'Georgian',
        'Glagolitic', 'Gothic', 'Greek', 'Gujarati', 'Gurmukhi', 'Han', 'Hangul', 'Hanunoo', 'Hebrew', 'Hiragana',
        'Imperial_Aramaic', 'Inherited', 'Inscriptional_Pahlavi', 'Inscriptional_Parthian', 'Javanese', 'Kaithi',
        'Kannada', 'Katakana', 'Kayah_Li', 'Kharoshthi', 'Khmer', 'Lao', 'Latin', 'Lepcha', 'Limbu', 'Linear_B', 'Lisu',
        'Lycian', 'Lydian', 'Malayalam', 'Mandaic', 'Meetei_Mayek', 'Meroitic_Cursive', 'Meroitic_Hieroglyphs', 'Miao',
        'Mongolian', 'Myanmar', 'New_Tai_Lue', 'Nko', 'Ogham', 'Old_Italic', 'Old_Persian', 'Old_South_Arabian',
        'Old_Turkic', 'Ol_Chiki', 'Oriya', 'Osmanya', 'Phags_Pa', 'Phoenician', 'Rejang', 'Runic', 'Samaritan',
        'Saurashtra', 'Sharada', 'Shavian', 'Sinhala', 'Sora_Sompeng', 'Sundanese', 'Syloti_Nagri', 'Syriac', 'Tagalog',
        'Tagbanwa', 'Tai_Le', 'Tai_Tham', 'Tai_Viet', 'Takri', 'Tamil', 'Telugu', 'Thaana', 'Thai', 'Tibetan',
        'Tifinagh', 'Ugaritic', 'Vai', 'Yi',
    ];

    private const SINGLE_UNI_CODES = [
        'C' => 'Other',
        'L' => 'Letter',
        'M' => 'Mark',
        'N' => 'Number',
        'P' => 'Punctuation',
        'S' => 'Symbol',
        'Z' => 'Separator',
    ];

    /*private const UNI_CODES = [
        'Cc' => 'Control',
        'Cf' => 'Format',
        'Cn' => 'Unassigned',
        'Co' => 'Private use',
        'Cs' => 'Surrogate',
        'Ll' => 'Lower case letter',
        'Lm' => 'Modifier letter',
        'Lo' => 'Other letter',
        'Lt' => 'Title case letter',
        'Lu' => 'Upper case letter',
        'Mc' => 'Spacing mark',
        'Me' => 'Enclosing mark',
        'Mn' => 'Non-spacing mark',
        'Nd' => 'Decimal number',
        'Nl' => 'Letter number',
        'No' => 'Other number',
        'Pc' => 'Connector punctuation',
        'Pd' => 'Dash punctuation',
        'Pe' => 'Close punctuation',
        'Pf' => 'Final punctuation',
        'Pi' => 'Initial punctuation',
        'Po' => 'Other punctuation',
        'Ps' => 'Open punctuation',
        'Sc' => 'Currency symbol',
        'Sk' => 'Modifier symbol',
        'Sm' => 'Mathematical symbol',
        'So' => 'Other symbol',
        'Zl' => 'Line separator',
        'Zp' => 'Paragraph separator',
        'Zs' => 'Space separator',
    ];*/

    final public function __construct(
        public string $value,
        public bool $not,
    ) {
    }

    public function __toString(): string
    {
        return match (strlen($this->value)) {
            0 => '\\X',
            1 => '\\'.($this->not ? 'P' : 'p').$this->value,
            default => '\\'.($this->not ? 'P' : 'p').'{'.$this->value.'}',
        };
    }

    /** @param array{value?: string, not?: bool} $data */
    public static function fromArray(array $data): static
    {
        return new static($data['value'] ?? '', $data['not'] ?? false);
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getMaxLength(): ?int
    {
        return 1;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
