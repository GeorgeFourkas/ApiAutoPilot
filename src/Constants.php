<?php

namespace ApiAutoPilot\ApiAutoPilot;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Constants
{
    const HAS_ONE = HasOne::class;

    const HAS_MANY = 'HasMany';

    const BELONGS_TO = 'BelongsTo';

    const BELONGS_TO_MANY = 'BelongsToMany';

    const MORPH_ONE = 'MorphOne';

    const MORPH_MANY = 'MorphMany';

    const MORPH_BY_MANY = 'MorphedByMany';

    const MORPH_TO_MANY = 'MorphToMany';

    const GET_METHOD_NAME = 'GET';

    const POST_METHOD_NAME = 'POST';

    const PATCH_METHOD_NAME = 'PATCH';

    const DELETE_METHOD_NAME = 'DELETE';

    //    const HAS_ONE = "HasOne";
    //    const HAS_MANY = "HasMany";
    //    const MORPH_ONE = "MorphOne";
    //    const BELONGS_TO = "BelongsTo";
    //    const BELONGS_TO_MANY = "BelongsToMany";

    public const IS_ELIGIBLE_FOR_ATTACH = [
        self::BELONGS_TO_MANY,
        self::MORPH_BY_MANY,
        self::MORPH_TO_MANY,
    ];
}
