<?php

/**
 * Lightweight stubs for Bitrix D7 classes used in production code.
 * Only loaded in the test environment — never in production.
 */

namespace Bitrix\Main\ORM\Fields {
    abstract class ScalarField
    {
        protected string $name;
        protected mixed $defaultValue = null;

        public function __construct(string $name)
        {
            $this->name = $name;
        }

        public function getName(): string
        {
            return $this->name;
        }

        public function getDefaultValue(): mixed
        {
            return $this->defaultValue;
        }

        public function configurePrimary(bool $v): static        { return $this; }
        public function configureAutocomplete(bool $v): static   { return $this; }
        public function configureRequired(bool $v): static       { return $this; }
        public function configureSize(int $v): static            { return $this; }
        public function configureDefaultValue(mixed $v): static  { $this->defaultValue = $v; return $this; }
    }

    class IntegerField  extends ScalarField {}
    class StringField   extends ScalarField {}
    class DatetimeField extends ScalarField {}
}

namespace Bitrix\Main\ORM\Data {
    abstract class DataManager
    {
        public static function getTableName(): string { return ''; }
        public static function getMap(): array        { return []; }
        public static function getById(mixed $id): object { return new class { public function fetch(): false { return false; } }; }
        public static function add(array $data): object   { return new class { public function isSuccess(): bool { return true; } public function getId(): int { return 0; } public function getErrorMessages(): array { return []; } }; }
        public static function update(mixed $id, array $data): object { return new class { public function isSuccess(): bool { return true; } }; }
    }
}

namespace Bitrix\Main\Type {
    class DateTime
    {
        private \DateTime $dt;

        public function __construct()
        {
            $this->dt = new \DateTime();
        }

        public function __toString(): string
        {
            return $this->dt->format('Y-m-d H:i:s');
        }
    }
}

namespace {
    if (!class_exists('CFile')) {
        class CFile
        {
            public static function SaveFile(array $file, string $module): int|false { return false; }
            public static function GetFileArray(int $id): array|false               { return false; }
            public static function GetFileSRC(array $file): string                  { return ''; }
            public static function Delete(int $id): void                            {}
        }
    }
}
