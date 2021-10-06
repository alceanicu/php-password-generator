<?php

use alcea\generator\PhpPasswordGenerator;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

final class PhpPasswordGeneratorTest extends TestCase
{
    /**
     * @return array[]
     */
    #[Pure] public function phpConfigProvider(): array
    {
        return [
            # array|boll        $uppercase,
            # array|boll        $lowercase,
            # array|boll        $number,
            # array|boll        $special,
            # array             $char
            # array|bool|int    $uppercaseLength,
            # array|bool|int    $lowercaseLength,
            # array|bool|int    $numberLength,
            # array|bool|int    $specialLength
            # int               $expectedLength = $uppercaseLength + $lowercaseLength + $numberLength + $specialLength

            # pass length 6 allowed char ['A', 'B', 'C', 'x', 'y', 'z', '0', '7', '9', '#', '@', '!'],
            [
                ['A', 'B', 'C'],
                ['x', 'y', 'z'],
                ['0', '7', '9'],
                ['#', '@', '!'],
                ['A', 'B', 'C', 'x', 'y', 'z', '0', '7', '9', '#', '@', '!'],
                2,
                2,
                1,
                1,
                6
            ],
            # numeric pass length 9 allowed char [0-9] and wrong param,
            [
                false,
                false,
                true,
                false,
                array_merge(PhpPasswordGenerator::getDefaultNumber()),
                false,
                false,
                [2, 4],
                false,
                [2, 4]
            ],
            [
                false,
                ['a', 'b', 'c', 'd'],
                ['0', '1', '5', '9'],
                ['#', '@', '!', '&'],
                ['a', 'b', 'c', 'd', '0', '1', '5', '9', '#', '@', '!', '&'],
                0,
                2,
                1,
                1,
                4
            ],
            [
                false,
                false,
                false,
                true,
                array_merge(PhpPasswordGenerator::getDefaultSpecial()),
                0,
                0,
                0,
                100,
                count(PhpPasswordGenerator::getDefaultSpecial())
            ],
            [
                true,
                true,
                true,
                true,
                array_merge(
                    PhpPasswordGenerator::getDefaultUppercase(),
                    PhpPasswordGenerator::getDefaultLowercase(),
                    PhpPasswordGenerator::getDefaultNumber(),
                    PhpPasswordGenerator::getDefaultSpecial()
                ),
                0,
                2,
                1,
                1,
                4
            ],
            [
                true,
                true,
                true,
                true,
                array_merge(
                    PhpPasswordGenerator::getDefaultUppercase(),
                    PhpPasswordGenerator::getDefaultLowercase(),
                    PhpPasswordGenerator::getDefaultNumber(),
                    PhpPasswordGenerator::getDefaultSpecial()
                ),
                [5, 10],
                [5, 10],
                [3, 4],
                [2, 4],
                [15, 28]
            ],
        ];
    }

    /**
     * @dataProvider phpConfigProvider
     */
    public function test_password_from_config(
        $uppercase,
        $lowercase,
        $number,
        $special,
        $chars,
        $uppercaseLength,
        $lowercaseLength,
        $numberLength,
        $specialLength,
        $expectedLength,
    )
    {
        $passwordObj = new PhpPasswordGenerator($uppercase, $lowercase, $number, $special);
        $password = $passwordObj->generate($uppercaseLength, $lowercaseLength, $numberLength, $specialLength);

        $this->assertInstanceOf(PhpPasswordGenerator::class, $passwordObj);

        $this->assertIsString($password);

        if (is_array($expectedLength)) {
            $this->assertLessThanOrEqual(strlen($password), $expectedLength[0]);
            $this->assertGreaterThanOrEqual(strlen($password), $expectedLength[1]);
        }

        if (is_int($expectedLength)) {
            $this->assertEquals(strlen($password), $expectedLength);
        }

        foreach (str_split($password) as $char) {
            $this->assertContains($char, $chars);
        }
    }
}
