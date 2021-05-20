<?php


namespace App\Classes\DocGenerator;

use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use BenSampo\Enum\Enum;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;
use JetBrains\PhpStorm\Pure;

class SchemaConfig
{
    public function __construct(protected bool $nullable = false, protected string $description = "")
    {
    }

    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function optional(bool $optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function applyToSchema(Schema $schema): Schema
    {
        return $schema
            ->nullable($this->nullable)
            ->description($this->description);
    }
}
