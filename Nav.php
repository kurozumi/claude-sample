<?php

namespace Plugin\ClaudeSample;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    public static function getNav(): array
    {
        return [
            'claude_sample' => [
                'name' => 'claude_sample.admin.nav.title',
                'icon' => 'fa-users',
                'children' => [
                    'claude_sample_admin_group' => [
                        'name' => 'claude_sample.admin.nav.group_list',
                        'url' => 'claude_sample_admin_group',
                    ],
                    'claude_sample_admin_group_new' => [
                        'name' => 'claude_sample.admin.nav.group_new',
                        'url' => 'claude_sample_admin_group_new',
                    ],
                ],
            ],
        ];
    }
}
