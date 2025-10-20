<?php

namespace App\Enums;

enum PhotoUsage: string
{
    case COVER = 'cover';
    case THUMBNAIL = 'thumbnail';
    case BANNER = 'banner';
    case PROFILE = 'profile';
    case LOGO = 'logo';
    case ATTACHMENT = 'attachment';
}
