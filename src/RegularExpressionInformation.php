<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

use EugeneErg\RegularExpression\Functions\CharFunction;
use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\GroupFunction;
use EugeneErg\RegularExpression\Functions\StructureFunction;
use EugeneErg\RegularExpression\Parser\ParserItem;
use EugeneErg\RegularExpression\Parser\ParserOption;
use EugeneErg\RegularExpression\Parser\ParserResult;
use EugeneErg\RegularExpression\Parser\StringParser;
use LogicException;

class RegularExpressionInformation
{
    private const ESCAPES = [
        'd' => 'decimal',
        'D' => 'not a decimal',
        'h' => 'horizontal whitespace',
        'H' => 'not a horizontal whitespace',
        's' => 'whitespace',
        'S' => 'not a whitespace',
        'v' => 'vertical whitespace',
        'V' => 'not a vertical whitespace',
        'w' => 'word',
        'W' => 'non-word',
        'b' => 'word boundary',
        'B' => 'not a word boundary',
        'a' => 'alarm',
        'A' => 'start of subject',
        'z' => 'end of subject',
        'Z' => 'end of subject or newline at end',
        'G' => 'first matching position in subject',
        'n' => 'newline',
        'r' => 'carriage return',
        'R' => 'line break',
        't' => 'tab',
        'e' => 'escape',
        'f' => 'form feed',
        'X' => 'unicode',
    ];

    private const CLASS_UNI_CODES = [
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

    private const UNI_CODES = [
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
    ];

    private const CHAR_CLASSES = [
        'alnum', 'alpha', 'ascii', 'blank', 'cntrl', 'digit', 'graph',
        'lower', 'print', 'punct', 'space', 'upper', 'word', 'xdigit',
    ];

    private const GROUP_SPECIAL = '()[?+*^$.|\\';

    private const CHARS_SPECIAL = ']\\';

    private const ALL_SPECIAL = '{}]<>-=#:()[?+*^$.|\\!';

    private readonly FunctionInterface $structure;

    public function __construct(RegularExpression $regularExpression)
    {
        $decimal = ParserItem::equal('\\\\(?<value>(?i)d)', fn (array $match) => ['not' => $match['value'] === 'D']);
        $whitespace = ParserItem::equal('\\\\(?<value>(?i)[hsv])', fn (array $match) => [
            'horizontal' => str_contains('sShH', $match['value']),
            'vertical' => str_contains('sSvV', $match['value']),
            'not' => str_contains('HSV', $match['value']),
        ]);
        $word = ParserItem::equal('\\\\(?<value>(?i)w)', fn (array $match) => ['not' => $match['value'] === 'W']);
        $hex = ParserItem::options(
            ParserOption::new('\\\\x(?<value>(?i)[0-9a-f]{0,2})', fn (array $match) => [
                'value' => $match['value'],
                'type' => 'value',
            ]),
            ParserOption::new('\\\\x\\{(?<value>(?i)[0-9a-f]{1,4})\\}', fn (array $match) => [
                'value' => $match['value'],
                'type' => 'brace',
            ]),
            ParserOption::new('\\\\c(?<value>.)', fn (array $match) => [
                'value' => $this->charToHex($match['value']),
                'type' => 'ord',
            ]),
        );
        $group = ParserItem::group(
            '\\(',
            '\\)',
            ParserOption::new(
                '\\?(?<direction><|)(?<not>=|!)',
                fn (array $match) => [
                    'direction' => ['<' => 'before', '' => 'after'][$match['direction']],
                    'not' => $match['not'] === '!',
                ],
            ),
            ParserOption::new('\\?(?<type>P?<)(?<value>[a-z0-9_]+)\\>', fn (array $match) => [
                'name' => $match['value'],
                'name_type' => $match['type'],
            ]),
            ParserOption::new('\\?(?<type>\\\')(?<value>[a-z0-9_]+)\\\'', fn (array $match) => [
                'name' => $match['value'],
                'name_type' => $match['type'],
            ]),
            ParserOption::match('\\?(?<add>[imsxUXJ]*)(?:\\-(?<remove>[imsxUXJ]*))?\\:', 'add', 'remove'),
            ParserOption::new('\\?<', ['once' => true]),
            ParserOption::new('\\?(?=\\()', ['condition' => true]),
        );

        $structure = ParserItem::children(
            flags: ParserItem::options(ParserOption::match(
                '\\(\\?(?<add>[imsxUXJ])*(?:\\-(?<remove>[imsxUXJ]*))?\\)',
                'add',
                'remove',
            )),
            chars: ParserItem::group('\\[', '\\]', ParserOption::new('\\^', ['not' => true]))->addChildren(
                class: ParserItem::equal(
                    '\\[(?<not>\\^)?\\:(?<value>' . implode('|', self::CHAR_CLASSES) . ')\\:\\]',
                    fn (array $match) => [
                        'value' => $match['value'],
                        'not' => isset($match['not']),
                    ],
                ),
                decimal: $decimal,
                whitespace: $whitespace,
                word: $word,
                word_boundary: ParserItem::options(ParserOption::new('\\\\b', ['not' => false])),
                unicode: ParserItem::options(
                    ParserOption::new('\\\\(?<type>p|P)(?<value>[' . $this->getSingleCodes() . '])', fn (array $match) => [
                        'value' => $match['value'],
                        'not' => $match['type'] === 'P',
                        'type' => 'value',
                    ]),
                    ParserOption::new('\\\\(?<type>p|P)\\{(?<not>\\^)?(?<value>' . $this->getUniCodes() . ')\\}', fn (array $match) => [
                        'value' => $match['value'],
                        'not' => isset($match['not']) === ($match['type'] === 'p'),
                        'type' => 'brace',
                    ]),
                ),
                hex: $hex,
                between: ParserItem::options(
                    ParserOption::match(
                        '(?:(?<from>[^' . $this->escape(self::CHARS_SPECIAL) . ')|\\\\(?<from>[' . $this->escape(self::ALL_SPECIAL) . ']))\\-(?:(?<to>[^' . $this->escape(self::CHARS_SPECIAL) . '])|\\\\(?<to>[' . $this->escape(self::ALL_SPECIAL) . ']))',
                        'from',
                        'to',
                    ),
                ),
                string: ParserItem::options(
                    ParserOption::match('\\\\(?<value>[' . $this->escape(self::ALL_SPECIAL) . '])', 'value'),
                    ParserOption::match('(?<value>[^' . $this->escape(self::CHARS_SPECIAL) . '])', 'value'),
                    ParserOption::new(
                        '\\\\(?<value>[ertfna])',
                        fn (array $match) => [
                            'value' => ['e' => "\e", 'r' => "\r", 't' => "\t", 'f' => "\f", 'n' => "\n", 'a' => "\x07"][$match['value']],
                        ],
                    ),
                ),
            ),
            count: ParserItem::options(
                ParserOption::new('(?<value>[*+?])(?<lazy>\\?)', fn (array $match) => [
                    'from' => $match['value'] === '+' ? 1 : 0,
                    'to' => $match['value'] === '?' ? 1 : null,
                    'type' => ['*' => 'unsigned', '+' => 'positive', '?' => 'boolean'][$match['value']],
                    'lazy' => isset($match['lazy']),
                ]),
                ParserOption::new('\\{(?<from>\\d+),(?<to>\\d+)?\\}(?<lazy>\\?)', fn (array $match) => [
                    'from' => (int) $match['from'],
                    'to' => isset($match['to']) ? (int) $match['to'] : null,
                    'type' => 'between',
                    'lazy' => isset($match['lazy']),
                ]),
                ParserOption::new('\\{(?<equal>\\d+)\\}', fn (array $match) => [
                    'from' => (int) $match['equal'],
                    'to' => (int) $match['equal'],
                    'type' => 'equal',
                    'lazy' => false,
                ]),
            ),
            any: ParserItem::equal('\\.'),
            start: ParserItem::equal('\\^'),
            begin: ParserItem::equal('\\$'),
            decimal: $decimal,
            whitespace: $whitespace,
            word: $word,
            word_boundary: ParserItem::equal('\\\\(?<value>(?i)b)', fn (array $match) => ['not' => $match['value'] === 'B']),
            start_of_subject: ParserItem::equal('\\\\A'),
            end_of_subject: ParserItem::equal('\\\\z'),
            end_of_subject_or_newline_at_end: ParserItem::equal('\\\\Z'),
            first_matching_position_in_subject: ParserItem::equal('\\\\G'),
            unicode: ParserItem::options(
                ParserOption::new('\\\\X', ['type' => 'any']),
                ParserOption::new('\\\\(?<type>p|P)(?<value>[' . $this->getSingleCodes() . '])', fn (array $match) => [
                    'value' => self::SINGLE_UNI_CODES[$match['value']],
                    'not' => $match['type'] === 'P',
                    'type' => 'value',
                ]),
                ParserOption::new('\\\\(?<type>p|P)\\{(?<not>\\^)?(?<value>' . $this->getUniCodes() . ')\\}', fn (array $match) => [
                    'value' => self::SINGLE_UNI_CODES[$match['value']] ?? self::UNI_CODES[$match['value']] ?? $match['value'],
                    'not' => isset($match['not']) === ($match['type'] === 'p'),
                    'type' => 'brace',
                ]),
            ),
            hex: $hex,
            group_link: ParserItem::options(
                ParserOption::new('\\(\\?\\R\\)', [
                    'value' => 0,
                    'recursive' => true,
                ]),
                ParserOption::new(
                    '\\((?:\\R|(?<value>\d+)|(?<value>[a-z_][a-z0-9_]*)|\'(?<value>[a-z_][a-z0-9_]*)\'|<(?<value>[a-z_][a-z0-9_]*)>)\\)',
                    function (array $match, int $position, string $parent, array $parentOptions) {
                        if ($position > 0 || empty($parentOptions['condition'])) {
                            return null;
                        }

                        $value = $match['value'] ?? 0;

                        return [
                            'value' => is_numeric($value) ? (int) $value : $value,
                            'recursive' => true,
                            'first_in_condition' => true,
                            'template_or_group_with_name_R' => !isset($match['value']),
                        ];
                    },
                ),
                ParserOption::new('\\\\g(?<value>-?[0-9]+)', fn (array $match) => [
                    'value' => (int) $match['value'],
                    'type' => 'group',
                    'recursive' => false,
                ]),
                ParserOption::new('\\\\g\\{(?<value>-?[0-9]+)\\}', fn (array $match) => [
                    'value' => (int) $match['value'],
                    'type' => 'group brace',
                    'recursive' => false,
                ]),
                ParserOption::new('\\\\(?<type>k|g)\'(?<value>\'[a-z_][a-z0-9_]*)\'', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' quote',
                    'recursive' => false,
                ]),
                ParserOption::new('\\\\(?<type>k|g)\\{(?<value>[a-z_][a-z0-9_]*)\\}', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' brace',
                    'recursive' => false,
                ]),
                ParserOption::new('\\\\(?<type>k|g)\\<(?<value>[a-z_][a-z0-9_]*)\\>', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' tag',
                    'recursive' => false,
                ]),
                ParserOption::new('\\(\\?(?<value>\d+)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'recursive' => true,
                ]),
                ParserOption::new('\\((?<value>\d+)\\)', function (array $match, int $position, string $parent, array $parentOptions) {
                    if ($position === 0 || empty($parentOptions['condition'])) {
                        return null;
                    }

                    return [
                        'value' => $match['value'],
                        'recursive' => true,
                    ];
                }),
                ParserOption::new('\\(\\?P=(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'equal',
                    'recursive' => true,
                ]), // ссылка на группу v
                ParserOption::new('\\(\\?P>(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'greather',
                    'recursive' => true,
                ]), // ссылка на рекурсиную группу V
                ParserOption::new('\\(\\?&(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'and',
                    'recursive' => true,
                ]),
            ),
            comment: ParserItem::options(
                ParserOption::match('\\(#(?<value>.*?)\\)', 'value'),
            ),
            numbers: ParserItem::options(
                ParserOption::match('\\\\(?<value>\\d+)', 'value'),
            ),
            or: ParserItem::equal('\\|'),
            group: $group,
            string: ParserItem::options(
                ParserOption::match('\\\\(?<value>[' . $this->escape(self::ALL_SPECIAL) . '])', 'value'),
                ParserOption::match('(?<value>[^' . $this->escape(self::GROUP_SPECIAL) . '])', 'value'),
                ParserOption::new('\\\\(?<value>[ertfna])', fn (array $match) => [
                    'value' => ['e' => "\e", 'r' => "\r", 't' => "\t", 'f' => "\f", 'n' => "\n", 'a' => "\x07"][$match['value']],
                ]),
            ),
        );

        $group->addChildren(...$structure->getChildren());

        $result = (new StringParser($structure))->parse($regularExpression->pattern);

        if ($result === null) {
            throw new LogicException('invalid code.');
        }

        $groups = [];
        $this->structure = $this->prepare(
            $result,
            new StructureFunction($regularExpression->modifiers),
            $groups,
        );
    }

    /**
     * @throws RegularExpressionException
     */
    public static function fromPattern(string $pattern): static
    {
        return new self(RegularExpression::fromPattern($pattern));
    }

    /**
     * @param GroupFunction[] $groups
     */
    private function prepare(ParserResult $value, ParentFunctionInterface $parent, array &$groups): FunctionInterface
    {
        return match ($value->name) {
            'chars' => $this->prepareChars($value, $parent),
        };
    }

    private function prepareChars(ParserResult $value, ParentFunctionInterface $parent): CharFunction
    {
        return CharFunction::fromArray($value->options, $parent);
    }

    private function escape(string $value): string
    {
        return $value === '' ? '' : '\\' . implode('\\', str_split($value));
    }

    private function charToHex(string $value): string
    {
        return dechex(ord(strtoupper($value)) ^ 0x40);
    }

    private function getSingleCodes(): string
    {
        return implode('', array_keys(self::SINGLE_UNI_CODES));
    }

    private function getUniCodes(): string
    {
        return implode('|', array_merge(
            array_keys(self::UNI_CODES),
            array_keys(self::SINGLE_UNI_CODES),
            self::CLASS_UNI_CODES,
        ));
    }
}
