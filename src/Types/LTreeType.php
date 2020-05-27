<?php

declare(strict_types=1);

namespace Umbrellio\LTree\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class LTreeType extends Type
{
    public const TYPE_NAME = 'ltree';
    public const TYPE_SEPARATE = '.';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return static::TYPE_NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        return collect(explode(static::TYPE_SEPARATE, (string) $value))->toArray();
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            $value = (array) $value;
        }

        return implode(static::TYPE_SEPARATE, $value);
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}
