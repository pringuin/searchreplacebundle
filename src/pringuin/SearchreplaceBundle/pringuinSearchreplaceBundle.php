<?php

namespace pringuin\SearchreplaceBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class pringuinSearchreplaceBundle extends AbstractPimcoreBundle
{
    const PACKAGE_NAME = 'pringuin/searchreplacebundle';

    public function getJsPaths()
    {
        return [
            '/bundles/pringuinsearchreplace/js/pimcore/startup.js'
        ];
    }

    public function getNiceName()
    {
        return 'Searchreplace Bundle';
    }

    public function getDescription()
    {
        return 'Searchreplace Bundle for Pimcore';
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

}
