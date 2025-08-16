<?php
namespace App\Enums;

enum WorkOrderStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case WAITING_PARTS = 'waiting_parts';
    case DONE = 'done';
    case INVOICED = 'invoiced';
    case CANCELED = 'canceled';
}
