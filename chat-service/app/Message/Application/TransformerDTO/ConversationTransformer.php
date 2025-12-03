<?php
declare(strict_types=1);

namespace App\Message\Application\TransformerDTO;

use App\Message\Application\DTO\ConversationDto;
use App\Shared\Application\InterfaceDto\ConversationTransformerDtoInterface;

class ConversationTransformer implements ConversationTransformerDtoInterface
{

    public function transform(array $row): ConversationDto
    {
        return new ConversationDto(
            (string) $row['contact_id'],
            (int) $row['unread_count']
        );
    }
}
