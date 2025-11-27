<?php
declare(strict_types=1);

namespace App\Shared\Application\InterfaceDto;

use App\Message\Application\DTO\ConversationDto;
interface ConversationTransformerDtoInterface
{
    public function transform(array $row): ConversationDto;
}
