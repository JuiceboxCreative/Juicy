<?php

namespace Juicy\PostType;

use Juicy\PostType\CustomPostType;

class Example extends CustomPostType
{
    protected static $postType = 'example';

    protected static $postName = 'Example';

    protected static $singularName = 'Example';

    protected static $pluralName = 'Examples';

    protected static $supports = array(
        'title',
        'thumbnail',
        'excerpt'
    );
}
