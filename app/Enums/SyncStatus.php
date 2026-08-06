<?php

namespace App\Enums;

enum SyncStatus:string
{
    case Pending='pending';

    case Running='running';

    case Success='success';

    case Failed='failed';
}