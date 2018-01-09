<?php

namespace Juicy\Core;

use Timber\Timber;

class PostPreview extends PostPreview
{
    protected $end = '&hellip;';
    protected $force = false;
    protected $length = 20;
    protected $readmore = '';
    protected $strip = true;
}
