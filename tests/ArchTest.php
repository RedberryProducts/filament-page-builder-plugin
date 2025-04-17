<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'info'])
    ->each->not->toBeUsed();

it('ensures `env` is only used in config files')
    ->expect('env')
    ->not->toBeUsed()
    ->ignoring('config');
