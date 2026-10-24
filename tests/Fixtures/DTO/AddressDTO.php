<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\Property;

class AddressDTO
{
    #[Property(description: 'City name', example: 'Berlin')]
    public string $city = '';

    public ?string $postalCode = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
