<?php
declare(strict_types=1);

namespace App\Message\Domain\Repository;

use App\Message\Domain\Message;

interface MessageRepositoryInterface
{
    public function insert(Message $message):void;

    public function readBy():void;

    public function findByUserId():void;
}
