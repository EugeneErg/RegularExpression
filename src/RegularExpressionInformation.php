<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

use EugeneErg\RegularExpression\Functions\CustomFunction;
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

    private readonly StringParser $parser;

    public function __construct()
    {
        $groupStrings = ParserItem::options(
            ParserOption::match('\\\\(?<value>[' . $this->escape(self::ALL_SPECIAL) . '])', 'value'),
            ParserOption::match('(?<value>[^' . $this->escape(self::GROUP_SPECIAL) . ']+)', 'value'),
        );
        $charsStrings = ParserItem::options(
            ParserOption::match('\\\\(?<value>[' . $this->escape(self::ALL_SPECIAL) . '])', 'value'),
            ParserOption::match('(?<value>[^' . $this->escape(self::CHARS_SPECIAL) . ']+)', 'value'),
        );
        $decimal = ParserItem::options(
            ParserOption::new('\\\\d', ['not' => false]),
            ParserOption::new('\\\\D', ['not' => true]),
        );
        $whitespace = ParserItem::options(
            ParserOption::new('\\\\h', ['horizontal' => true, 'vertical' => false, 'not' => false]),
            ParserOption::new('\\\\H', ['horizontal' => true, 'vertical' => false, 'not' => true]),
            ParserOption::new('\\\\s', ['horizontal' => true, 'vertical' => true, 'not' => false]),
            ParserOption::new('\\\\S', ['horizontal' => true, 'vertical' => true, 'not' => true]),
            ParserOption::new('\\\\v', ['horizontal' => false, 'vertical' => true, 'not' => false]),
            ParserOption::new('\\\\V', ['horizontal' => false, 'vertical' => true, 'not' => true]),
        );
        $word = ParserItem::options(
            ParserOption::new('\\\\w', ['not' => false]),
            ParserOption::new('\\\\W', ['not' => true]),
        );
        $alarm = ParserItem::equal('\\\\a');
        $newline = ParserItem::equal('\\\\n');
        $carriageReturn = ParserItem::equal('\\\\r');
        $tab = ParserItem::equal('\\\\t');
        $escape = ParserItem::equal('\\\\e');
        $formFeed = ParserItem::equal('\\\\f');
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
            ParserOption::new('\\?<', ['once' => true]),
            ParserOption::new('\\?(?<type>P?<)(?<value>[a-z0-9_]+)\\>', fn (array $match) => [
                'name' => $match['value'],
                'name_type' => $match['type'],
            ]),
            ParserOption::new('\\?(?<type>\\\')(?<value>[a-z0-9_]+)\\\'', fn (array $match) => [
                'name' => $match['value'],
                'name_type' => $match['type'],
            ]),
            ParserOption::match('\\?(?<add>[imsxUXJ]*)(?:\\-(?<remove>[imsxUXJ]*))?\\:', 'add', 'remove'),
            ParserOption::new('\\?(?=\\()', ['condition' => true]),
        );

        $structure = ParserItem::children(
            recursive: ParserItem::equal('\\(\\?\\R\\)'),
            flags: ParserItem::options(ParserOption::match(
                '\\(\\?(?<add>[imsxUXJ])*(?:\\-(?<remove>[imsxUXJ]*))?\\)',
                'add',
                'remove',
            )),
            chars: ParserItem::group('\\[', '\\]', ParserOption::new('\\^', ['not' => true]))->addChildren(
                class: ParserItem::options(
                    ParserOption::new(
                        '\\[(?<not>\\^)?\\:(?<value>' . implode('|', self::CHAR_CLASSES) . ')\\:\\]',
                        fn (array $match) => [
                            'value' => $match['value'],
                            'not' => isset($match['not']),
                        ],
                    ),
                ),
                decimal: $decimal,
                whitespace: $whitespace,
                word: $word,
                word_boundary: ParserItem::options(ParserOption::new('\\\\b', ['not' => false])),
                alarm: $alarm,
                newline: $newline,
                carriage_return: $carriageReturn,
                tab: $tab,
                escape: $escape,
                form_feed: $formFeed,
                unicode: ParserItem::options(
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
                between: ParserItem::options(
                    ParserOption::match(
                        '(?:(?<from>[^' . $this->escape(self::CHARS_SPECIAL) . ')|\\\\(?<from>[' . $this->escape(self::ALL_SPECIAL) . ']))\\-(?:(?<to>[^' . $this->escape(self::CHARS_SPECIAL) . '])|\\\\(?<to>[' . $this->escape(self::ALL_SPECIAL) . ']))',
                        'from',
                        'to',
                    ),
                ),
                strings: $charsStrings,
            ),
            count: ParserItem::options(
                ParserOption::new('\\*', ['from' => 0, 'type' => 'unsigned']),
                ParserOption::new('\\+', ['from' => 1, 'type' => 'positive']),
                ParserOption::new('\\?', ['from' => 0, 'type' => 'boolean', 'to' => 1]),
                ParserOption::new('\\{(?<from>\\d+),(?<to>\\d+)?\\}', fn (array $match) => [
                    'from' => (int) $match['from'],
                    'to' => isset($match['to']) ? (int) $match['to'] : null,
                    'type' => 'between',
                ]),
                ParserOption::new('\\{(?<equal>\\d+)\\}', fn (array $match) => [
                    'from' => (int) $match['equal'],
                    'to' => (int) $match['equal'],
                    'type' => 'equal',
                ]),
            ),
            any: ParserItem::equal('\\.'),
            start: ParserItem::equal('\\^'),
            begin: ParserItem::equal('\\$'),
            decimal: $decimal,
            whitespace: $whitespace,
            word: $word,
            word_boundary: ParserItem::options(
                ParserOption::new('\\\\b', ['not' => false]),
                ParserOption::new('\\\\B', ['not' => true]),
            ),
            alarm: $alarm,
            start_of_subject: ParserItem::equal('\\\\A'),
            end_of_subject: ParserItem::equal('\\\\z'),
            end_of_subject_or_newline_at_end: ParserItem::equal('\\\\Z'),
            first_matching_position_in_subject: ParserItem::equal('\\\\G'),
            newline: $newline,
            carriage_return: $carriageReturn,
            line_break: ParserItem::equal('\\\\R'),
            tab: $tab,
            escape: $escape,
            form_feed: $formFeed,
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
                ParserOption::new('\\\\g(?<value>-?[0-9]+)', fn (array $match) => [
                    'value' => (int) $match['value'],
                    'type' => 'group',
                ]),
                ParserOption::new('\\\\g\\{(?<value>-?[0-9]+)\\}', fn (array $match) => [
                    'value' => (int) $match['value'],
                    'type' => 'group brace',
                ]),
                ParserOption::new('\\\\(?<type>k|g)\'(?<value>\'[a-z_][a-z0-9_]*)\'', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' quote',
                ]),
                ParserOption::new('\\\\(?<type>k|g)\\{(?<value>[a-z_][a-z0-9_]*)\\}', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' brace',
                ]),
                ParserOption::new('\\\\(?<type>k|g)\\<(?<value>[a-z_][a-z0-9_]*)\\>', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => ['k' => 'key', 'g' => 'group'][$match['type']] . ' tag',
                ]),
                ParserOption::new('\\(\\?(?<value>\d+)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'recursive',
                ]),
                ParserOption::new('\\(\\?P=(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'recursive equal',
                ]), // ссылка на группу v
                ParserOption::new('\\(\\?P>(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'recursive greather',
                ]), // ссылка на рекурсиную группу V
                ParserOption::new('\\(\\?&(?<value>[a-z][a-z0-9_]*)\\)', fn (array $match) => [
                    'value' => $match['value'],
                    'type' => 'recursive and',
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
            string: $groupStrings,
        );

        $group->addChildren(...$structure->getChildren());

        $this->parser = new StringParser($structure);
    }

    /**
     * @throws RegularExpressionException
     */
    public static function fromPattern(string $pattern): static
    {
        return new RegularExpressionInformation(RegularExpression::fromPattern($pattern));
    }

    public function getStructure(RegularExpression $regularExpression): ?ParserResult
    {
        return $this->parser->parse($regularExpression->pattern);




        $pattens = [
            '(?<type>p|P)(?<value>[CLMNPSZ])',
            '(?<type>(?:p|P)\\{)(?<not>\\^)?(?<value>' . implode('|', self::UNI_CODES) . ')\\}',
            '(?<type>g)(?<value>-?[0-9]+)',
            '(?<type>g\\{)(?<value>-?[0-9]+)\\}',
            '(?<type>(?:k|g)\')(?<value>\'[a-z0-9]+)\'',
            '(?<type>(?:k|g)\\{)(?<value>[a-z0-9]+)\\}',
            '(?<type>(?:k|g)\\<)(?<value>[a-z0-9]+)\\>',
        ];
        $groupString = (string) $groupCount;

        if ($groupCount === 0) {
            $pattens[] = '(?<type>)(?<value>[0-7]{1,3}|0)';
        } elseif ($groupCount <= 7) {
            $pattens[] = '(?<type>)(?<value>[0-7]{1,3})';
        } elseif ($groupCount < 10) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|[0-' . $groupCount . '])';
        } elseif ($groupCount < 100) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0) . ']|[0-9])';
        } elseif ($groupCount < 1000) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0) . ']|[1-9][0-9]?|0)';
        } else {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0)
                . ']|[1-9][0-9]{0,' . (strlen($groupString) - 2) . '}|0)';
        }




        $length = strlen($this->regularExpression->pattern);
        $result = new CustomFunction('group');
        $groupCount = 0;

        for ($i = 0; $i < $length; $i++) {
            $char = $this->getChar($i);

            if ($char === '|') {
                $result = $this->getChoisParse($i, $length, $result);
            }

            if (str_contains('{?*+', $char)) {
                $this->getCountParse($i, $result);
            } else {
                $result->addChild(match ($char) {
                    '\\' => $this->getSlashParse($i, $groupCount),
                    '(' => $this->getGroupParse($i, $length, $groupCount),
                    '[' => $this->getCharsParse($i, $length, $groupCount),
                    '.' => new CustomFunction('any'),
                    default => $this->getStringParse($i, $length),
                });
            }
        }

        return count($result->getChildren()->items) === 1 ? $result->getLastChild() : $result;
    }

    private function getChar(int $pos): ?string
    {
        return $this->regularExpression->pattern[$pos] ?? null;
    }

    private function getStringParse(int &$i, int $length): string
    {
        $result = '';

        for (; $i < $length; $i++) {
            $char = $this->getChar($i);

            if (str_contains(self::ALL_SPECIAL, $char)) {
                return $result;
            }

            $result .= $char;
        }

        return $result;
    }

    private function getSlashParse(int &$i, int $groupCount): string|CustomFunction
    {
        $i++;
        $char = $this->getChar($i);

        if (str_contains(self::ALL_SPECIAL . '-', $char)) {
            return $char;
        }

        $name = self::ESCAPES[$char] ?? null;

        if ($name !== null) {
            return new CustomFunction($name);
        }

        $pattens = [
            '(?<type>x)((?<value>(?:[0-9a-f]|[0-9A-F]){0,2}))',
            '(?<type>x\\{)(?<value>(?:[0-9a-f]|[0-9A-F]){1,4})\\}',
            '(?<type>c)(?<value>.)',
            '(?<type>p|P)(?<value>[CLMNPSZ])',
            '(?<type>(?:p|P)\\{)(?<not>\\^)?(?<value>' . implode('|', self::UNI_CODES) . ')\\}',
            '(?<type>g)(?<value>-?[0-9]+)',
            '(?<type>g\\{)(?<value>-?[0-9]+)\\}',
            '(?<type>(?:k|g)\')(?<value>\'[a-z0-9]+)\'',
            '(?<type>(?:k|g)\\{)(?<value>[a-z0-9]+)\\}',
            '(?<type>(?:k|g)\\<)(?<value>[a-z0-9]+)\\>',
        ];
        $groupString = (string) $groupCount;

        if ($groupCount === 0) {
            $pattens[] = '(?<type>)(?<value>[0-7]{1,3}|0)';
        } elseif ($groupCount <= 7) {
            $pattens[] = '(?<type>)(?<value>[0-7]{1,3})';
        } elseif ($groupCount < 10) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|[0-' . $groupCount . '])';
        } elseif ($groupCount < 100) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0) . ']|[0-9])';
        } elseif ($groupCount < 1000) {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0) . ']|[1-9][0-9]?|0)';
        } else {
            $pattens[] = '(?<type>)(?<value>[0-7]{2,3}|'
                . substr_replace($groupString, '[0-', -1, 0)
                . ']|[1-9][0-9]{0,' . (strlen($groupString) - 2) . '}|0)';
        }

        $result = $this->match($i, '(?:' . implode('|', $pattens) . ')');

        if ($result === []) {
            throw new LogicException('Invalid parser.');
        }

        return match ($result['type']) {
            'x','x{' => (new CustomFunction('hex'))->setOption(value: $result['value'], type: $result['type']),
            'c' => (new CustomFunction('hex'))->setOption(value: $this->charToHex($result['value']), type: 'c'),
            'p','p{' => (new CustomFunction('unicode'))->setOption(value: $result['value'], type: $result['type'], not: isset($result['not'])),
            'P','P{' => (new CustomFunction('unicode'))->setOption(value: $result['value'], type: $result['type'], not: !isset($result['not'])),
            'g','g{','g\'','g<','k{','k\'','k<' => (new CustomFunction('group_number'))->setOption(value: (int) $result['value'], type: $result['type']),
            '' => $this->getNumberFunction($result['value'], $groupCount),
            default => throw new LogicException('Invalid pattern'),
        };
    }

    private function getCountParse(int &$i, CustomFunction $parent): void
    {
        $lastValue = $parent->getLastChild();
        $result = new CustomFunction('count');
        $char = $this->getChar($i);
        $result->setOption(type: ['?' => 'boolean', '+' => 'positive', '*' => 'unsigned', '{' => 'between'][$char]);

        if ($char === '?') {
            $result->setOption(from: 0, to: 1);
        } elseif ('+') {
            $result->setOption(from: 1);
        } elseif ('*') {
            $result->setOption(from: 0);
        } else {
            $match = $this->match($i, '\\{(?:(?<from>\\d+)?,(?<to>\\d+)?|(?<from>(?<to>\\d+)))\\}');

            if (isset($match['from'])) {
                $result->setOption(from: (int) $match['from']);
            }

            if (isset($match['to'])) {
                $result->setOption(to: (int) $match['to']);
            }

            $i += strlen($match[0]) - 1;
        }

        if ($lastValue instanceof CustomFunction || strlen($lastValue) === 1) {
            $result->addChild($lastValue);
            $parent->replaceLastChild($result);
        } else {
            $parent->replaceLastChild(substr($lastValue, 0, -1));
            $result->addChild(substr($lastValue, -1));
            $parent->addChild($result);
        }
    }

    private function getCharsParse(int &$i, int $length, int $groupCount): CustomFunction
    {
        $result = new CustomFunction('chars');
        $i++;

        if ($this->getChar($i) === '^') {
            $result->setOption(not: true);
            $i++;
        }

        for (; $i < $length; $i++) {
            $char = $this->getChar($i);

            if ($char === ']' && $result->getLastKey() !== null) {
                return $result;
            }

            if ($char === '[') {
                $result->addChild($this->checkCharClasses($i));
            } elseif ($char === '\\') {
                $result->addChild($this->getSlashParse($i, $groupCount));
            } elseif ($char === '-' && is_string($result->getLastChild()) && $this->getChar($i + 1) !== ']') {
                $value = $result->getLastChild();
                $i++;
                $child = (new CustomFunction('between'))->setOption(from: substr($value, -1), to: $this->getChar($i));

                if (strlen($value) === 1) {
                    $value->replaceLastChild($child);
                } else {
                    $result->replaceLastChild(substr($value, 0, -1));
                    $value->addChild($child);
                }
            } else {
                $result->addChild($char);
            }
        }

        throw new LogicException('Invalid pattern');
    }

    private function getGroupParse(int &$i, int $length, int &$groupCount): CustomFunction
    {
        $result = new CustomFunction('group');

        if ($this->getChar($i + 1) === '?') {
            $i++;
            $match = $this->match($i, '(?:' . implode('|', [
                '(?<type>)(?<direction><|)(?<not>=|!)',//утверждения - assertions
                '(?<type>\\>)',// однократные шаблоны - onlyonce
                '\\(\\?(?<this>)(?<plus>[imsxUXJ]*(?:\\-(?<minus>[imsxUXJ]*))?)(?<type>\\:|\\))',// флаги
                '\\(\\?\\((?<plus>[imsxUXJ]*(?:\\-(?<minus>[imsxUXJ]*))?)(?<type>\\))',// флаги
                '(?<type>P?<)(?<value>[a-z0-9_]+)\\>', // группа
                '(?<type>\\\')(?<value>[a-z0-9_]+)\\\'', // группа
                '(?<type>P=)(?<value>[a-z0-9_]+)\\)', // ссылка на группу v
                '(?<type>#)(?<value>[^)]*)\\)', // комментарий V
                '(?<type>\d+)\\)', //ссылка на рекурсивный подшаблон
                '(?<type>P>)(?<value>[a-z0-9_]+)\\)', // ссылка на рекурсиную группу V
                '(?<type>&)(?<value>[a-z0-9_]+)\\)', // ссылка на рекурсиную группу V
                '(?<type>R)\\)', //рекурсивный шаблон V
                '(?<type>\\()(?<value>\\d+|R)\\)', // условия
                '(?<type>\\()\\?(?:<|)(?:=|!)', // условия
            ]) . ')');

            if ($match === []) {
                throw new LogicException('Invalid parser.');
            }

            $type = $match['type'];

            if (is_numeric($type)) {
                return (new CustomFunction('group_number'))->setOption(value: (int) $type, type: 'recursive');
            }

            return match ($type) {
                '&','P>','P=' => (new CustomFunction('group_number'))->setOption(value: $match['value'], type: $type),
                '#' => (new CustomFunction('comment'))->setOption(value: $match['value']),
                'R' => new CustomFunction('recursive'),
                //')' =>
                //'(' => (new CustomFunction('condition'))->setOption(),
            };




            match ($type) {
                '' => $result->setOption(direction: ['<' => 'before', '' => 'after'][$match['direction']], not: $match['not'] === '!'),
                '>' => $result->setOption(once: true),
                'P<','<','\'' => $result->setOption(name: $match['value'], type: $type),
            };
        }






        throw new LogicException('Invalid pattern');
    }

    private function getSubPattern(int $i, ?int $length = null): string
    {
        return substr($this->regularExpression->pattern, $i, $length);
    }

    private function checkCharClasses(int &$i): string|CustomFunction
    {
        $match = $this->match($i, '\\[\\:(?:(?<negative>)\\^)(?<value>'
            . implode('|', self::CHAR_CLASSES) . ')\\:\\]');

        if (count($match) === 0) {
            return '[';
        }

        $result = new CustomFunction('char_class');
        $result->setOption(value: $match['value']);
        $result->setOption(negative: isset($match['negative']));
        $i += strlen($match[0]);

        return $result;
    }

    /**
     * @return string[]
     */
    private function match(int $pos, string $pattern): array
    {
        try {
            return (new RegularExpression('{', "^.{$pos}}{$pattern}", '}', RegularExpression::PCRE_INFO_JCHANGED))
                ->match($this->regularExpression->pattern);
        } catch (RegularExpressionException $exception) {
            throw new LogicException('Invalid internal pattern.', 0, $exception);
        }
    }

    private function charToHex(string $value): string
    {
        return dechex(ord(strtoupper($value)) ^ 0x40);
    }

    private function getNumberFunction(string $value, int $groupCount): CustomFunction
    {
        return in_array($value[0], ['-', '0'])
            || strlen($value) > 3
            || ($value !== '0' && strlen($value) === 1)
            || preg_match('{[89]}', $value)
            || (int) $value <= $groupCount
                ? (new CustomFunction('group_number'))->setOption(type: '', value: (int) $value)
                : (new CustomFunction('eight'))->setOption(value: $value);
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

    private function escape(string $value): string
    {
        return $value === '' ? '' : '\\' . implode('\\', str_split($value));
    }
}
//   (?<!\\)(?:\\\\)*\[(?:\\.|[^\\\[\]])*\]