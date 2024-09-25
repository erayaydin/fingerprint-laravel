<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'dump_if', 'dump_if_empty', 'dump_if_not_empty'])
    ->each->not->toBeUsed();
