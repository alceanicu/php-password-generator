<?php

namespace alcea\generator;

final class PhpPasswordGenerator
{
    public const TYPES = ['uppercase', 'lowercase', 'number', 'special'];
    public const UPPERCASE_DEFAULT_MIN = 4;
    public const UPPERCASE_DEFAULT_MAX = 7;
    public const LOWERCASE_DEFAULT_MIN = 4;
    public const LOWERCASE_DEFAULT_MAX = 7;
    public const NUMBER_DEFAULT_MIN = 1;
    public const NUMBER_DEFAULT_MAX = 5;
    public const SPECIAL_DEFAULT_MIN = 1;
    public const SPECIAL_DEFAULT_MAX = 5;

    /** @var string[] */
    private array $uppercase = [];
    private int $uppercaseMin;
    private int $uppercaseMax;
    /** @var string[] */
    private array $lowercase = [];
    private int $lowercaseMin;
    private int $lowercaseMax;
    /** @var string[] */
    private array $number = [];
    private int $numberMin;
    private int $numberMax;
    /** @var string[] */
    private array $special = [];
    private int $specialMin;
    private int $specialMax;

    /**
     * @param array|bool $uppercase - if true will use as default uppercase letters from range [A-Z],
     * if false will not use uppercase letters,
     * if array will use only letters from that array (eg: ['A', 'I', 'X']).
     * @param array|bool $lowercase - if true will use default lowercase letters from range [a-z],
     * if false will not use lowercase letters,
     * if array will use only letters from that array (eg: ['a', 'i', 'x']).
     * @param array|bool $number - - if true will use as default numbers from range [0-9],
     * if false will not use numbers,
     * if array will use only numbers from that array (eg: ['5', '3', '0']).
     * @param array|bool $special - if true will use as default special character from range [~!@#$%^&*?_+-],
     * if false will not use special characters,
     * if array will use only special characters from that array (eg: ['!', '@', '#']).
     * @param int|null $uppercaseMin - default 4
     * @param int|null $uppercaseMax - default 7
     * @param int|null $lowercaseMin - default 4
     * @param int|null $lowercaseMax - default 7
     * @param int|null $numberMin - default 1
     * @param int|null $numberMax - default 5
     * @param int|null $specialMin - default 1
     * @param int|null $specialMax - default 5
     */
    public function __construct(
        array|bool $uppercase = true,
        array|bool $lowercase = true,
        array|bool $number = true,
        array|bool $special = true,
        ?int       $uppercaseMin = null,
        ?int       $uppercaseMax = null,
        ?int       $lowercaseMin = null,
        ?int       $lowercaseMax = null,
        ?int       $numberMin = null,
        ?int       $numberMax = null,
        ?int       $specialMin = null,
        ?int       $specialMax = null
    )
    {
        $types = $this->getTypes($uppercase, $lowercase, $number, $special);

        foreach ($types as $type) {
            $value = is_bool($$type) ? null : $$type;
            $this->setTypeRange($type, $value, ${"{$type}Min"}, ${"{$type}Max"});
        }
    }

    /**
     * @param string $type
     * @param array|null $value
     * @param int|null $min
     * @param int|null $max
     */
    private function setTypeRange(
        string $type,
        ?array $value = null,
        ?int   $min = null,
        ?int   $max = null
    ): void
    {
        $this->{$type} = $value ?? self::{"getDefault{$type}"}();
        $this->setMinMaxForType($type, $min, $max);
    }

    /**
     * @param string $type
     * @param int|null $min
     * @param int|null $max
     */
    private function setMinMaxForType(string $type, ?int $min = null, ?int $max = null): void
    {
        $min = $min ?? self::getConstant("{$type}_DEFAULT_MIN");
        $max = $max ?? self::getConstant("{$type}_DEFAULT_MAX");

        # if min/max are negative we set the default
        $min = ($min < 0) ? self::getConstant("{$type}_DEFAULT_MIN") : $min;
        $max = ($max < 0) ? self::getConstant("{$type}_DEFAULT_MAX") : $max;

        # if min are bigger than max we set $min=$max
        $min = ($min > $max) ? $max : $min;

        # if min/max are bigger than actual type size
        $count = count($this->{$type});
        $min = ($min > $count) ? $count : $min;
        $max = ($max > $count) ? $count : $max;

        # if min are bigger than max we set $min=$max
        $min = ($min > $max) ? $max : $min;

        $this->{"{$type}Min"} = $min;
        $this->{"{$type}Max"} = $max;
    }

    /**
     * Length of the password will be 20 (in default config).
     * @param array|bool|int $uppercase
     * @param array|bool|int $lowercase
     * @param array|bool|int $number
     * @param array|bool|int $special
     * @param bool $shuffle
     * @return string
     */
    public function generate(
        array|bool|int $uppercase = true,
        array|bool|int $lowercase = true,
        array|bool|int $number = true,
        array|bool|int $special = true,
        bool           $shuffle = true,
    ): string
    {
        $types = $this->getTypes($uppercase, $lowercase, $number, $special);

        foreach ($types as $type) {
            # set new config
            # key::0 => is min;
            # key::1 => is max;
            if (is_array($$type)) {
                $this->setMinMaxForType($type, $$type[0] ?? null, $$type[1] ?? null);
                continue;
            }
            # set new config - fix length
            if (is_int($$type)) {
                $this->setMinMaxForType($type, $$type, $$type);
            }
        }

        $passwordCharArray = [];

        foreach ($types as $type) {
            $min = $this->{"{$type}Min"};
            $max = $this->{"{$type}Max"};

            $maxIndex = rand($min, $max);
            if ($maxIndex > 0) {
                $charsArray = $this->{$type};
                $randKeys = array_rand($charsArray, $maxIndex);
                if (is_array($randKeys)) {
                    for ($i = 0; $i < $maxIndex; $i++) {
                        $passwordCharArray[] = $charsArray[$randKeys[$i]];
                    }
                } else {
                    $passwordCharArray[] = $charsArray[$randKeys];
                }
            }
        }

        if ($shuffle) {
            shuffle($passwordCharArray);
        }

        return implode($passwordCharArray);
    }

    /**
     * @param array|bool $uppercase
     * @param array|bool $lowercase
     * @param array|bool $number
     * @param array|bool $special
     * @return string[]
     */
    private function getTypes(
        array|bool $uppercase = true,
        array|bool $lowercase = true,
        array|bool $number = true,
        array|bool $special = true,
    ): array
    {
        $types = self::TYPES;

        foreach ($types as $type) {
            # remove type from config
            if ($$type === false) {
                $types = array_filter($types, fn($m) => $m != $type);
            }
        }

        return $types;
    }

    /**
     * @return string[]
     */
    public static function getDefaultUppercase(): array
    {
        return range('A', 'Z');
    }

    /**
     * @return string[]
     */
    public static function getDefaultLowercase(): array
    {
        return range('a', 'z');
    }

    /**
     * @return string[]
     */
    public static function getDefaultNumber(): array
    {
        return str_split('0123456789');
    }

    /**
     * @return string[]
     */
    public static function getDefaultSpecial(): array
    {
        return ['~', '!', '@', '#', '$', '%', '^', '&', '*', '?', '_', '+', '-'];
    }

    /**
     * @param string $type
     * @return int|null
     */
    public static function getConstant(string $type): ?int
    {
        $type = strtoupper($type);
        $class = PhpPasswordGenerator::class;
        return constant("$class::$type");
    }
}
