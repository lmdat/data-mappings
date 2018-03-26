<?php
namespace App\Modules\Backend\Controllers;

use App\Libs\Utils\Vii;
use Request;

trait SidebarMenuTrait{


    public function createMenu(){
        $lang_mod = 'backend/sidebar';

        $menu = [
            [
                'id' => '',
                'text' => trans($lang_mod . '.dashboard'),
                'url' => './',
                'params' => [],
                'icon' => "<i class='app-menu__icon fa fa-dashboard' aria-hidden='true'></i>",
                'level-icon' => "<i class='treeview-indicator fa fa-angle-left pull-right' aria-hidden='true'></i>",
                'children' => []
            ],

            // [
            //     'id' => '',
            //     'text' => trans($lang_mod . '.mapping_item'),
            //     'url' => '#',
            //     'params' => [],
            //     'icon' => "<i class='fa fa-folder-open' aria-hidden='true'></i>",
            //     'level-icon' => "<i class='fa fa-angle-left pull-right' aria-hidden='true'></i>",
            //     'children' => [
            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.catalog_list'),
            //             'url' => 'catalog',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],

            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.add_catalog'),
            //             'url' => 'catalog/create',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ]

            //     ],
            // ],

            // [
            //     'id' => '',
            //     'text' => trans($lang_mod . '.product_management'),
            //     'url' => '#',
            //     'params' => [],
            //     'icon' => "<i class='fa fa-th-large' aria-hidden='true'></i>",
            //     'level-icon' => "<i class='fa fa-angle-left pull-right' aria-hidden='true'></i>",
            //     'children' => [
            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.product_list'),
            //             'url' => 'product',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],

            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.product_trashed_list'),
            //             'url' => 'product/trashed',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],

            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.add_product'),
            //             'url' => 'product/create',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ]

            //     ],
            // ],

            // [
            //     'id' => '',
            //     'text' => trans($lang_mod . '.page_management'),
            //     'url' => '#',
            //     'params' => [],
            //     'icon' => "<i class='fa fa-file-text-o' aria-hidden='true'></i>",
            //     'level-icon' => "<i class='fa fa-angle-left pull-right' aria-hidden='true'></i>",
            //     'children' => [
            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.page_list'),
            //             'url' => 'page',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],

            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.page_trashed_list'),
            //             'url' => 'page/trashed',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],

            //         [
            //             'id' => '',
            //             'text' => trans($lang_mod . '.add_page'),
            //             'url' => 'page/create',//'admin/users',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right' aria-hidden='true'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ]

            //     ],
            // ],

            // [
            //     'id' => '',
            //     'text' => trans($lang_mod . '.menu_management'),
            //     'url' => '#',
            //     'params' => [],
            //     'icon' => "<i class='fa fa-list'></i>",
            //     'level-icon' => "<i class='fa fa-angle-left pull-right'></i>",
            //     'children' => [
            //         [
            //             'id' => '51',
            //             'text' => trans($lang_mod . '.menu_list'),
            //             'url' => 'menu',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],
            //         [
            //             'id' => '52',
            //             'text' => trans($lang_mod . '.menu_trashed_list'),
            //             'url' => 'menu/trashed',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],
            //         [
            //             'id' => '53',
            //             'text' => trans($lang_mod . '.add_menu'),
            //             'url' => 'menu/create',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],


            //     ]
            // ],

            [
                'id' => '',
                'text' => trans($lang_mod . '.mappings_item'),
                'url' => '#',
                'params' => [],
                'icon' => "<i class='app-menu__icon fa fa-dashboard' aria-hidden='true'></i>",
                'level-icon' => "<i class='treeview-indicator fa fa-angle-right pull-right' aria-hidden='true'></i>",
                'children' => [
                    [
                        'id' => '41',
                        'text' => trans($lang_mod . '.define_mappings_item'),
                        'url' => 'mappings-item',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ],

                    // [
                    //     'id' => '42',
                    //     'text' => trans($lang_mod . '.mappings_item_account'),
                    //     'url' => 'mappings-item/mount',
                    //     'params' => [],
                    //     'icon' => "<i class='fa fa-angle-double-right'></i>",
                    //     'level-icon' => "",
                    //     'children' => []
                    // ],
                    
                ]
            ],

            [
                'id' => '',
                'text' => trans($lang_mod . '.account'),
                'url' => '#',
                'params' => [],
                'icon' => "<i class='app-menu__icon fa fa-dashboard' aria-hidden='true'></i>",
                'level-icon' => "<i class='treeview-indicator fa fa-angle-right pull-right' aria-hidden='true'></i>",
                'children' => [
                    [
                        'id' => '51',
                        'text' => trans($lang_mod . '.define_account'),
                        'url' => 'account',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ],

                ]
            ],

            [
                'id' => '',
                'text' => trans($lang_mod . '.dimension'),
                'url' => '#',
                'params' => [],
                'icon' => "<i class='app-menu__icon fa fa-dashboard' aria-hidden='true'></i>",
                'level-icon' => "<i class='treeview-indicator fa fa-angle-right pull-right' aria-hidden='true'></i>",
                'children' => [
                    [
                        'id' => '61',
                        'text' => trans($lang_mod . '.define_dimension'),
                        'url' => 'dimension',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ],

                ]
            ],

            [
                'id' => '',
                'text' => trans($lang_mod . '.ledger'),
                'url' => '#',
                'params' => [],
                'icon' => "<i class='app-menu__icon fa fa-dashboard' aria-hidden='true'></i>",
                'level-icon' => "<i class='treeview-indicator fa fa-angle-right pull-right' aria-hidden='true'></i>",
                'children' => [
                    [
                        'id' => '70',
                        'text' => trans($lang_mod . '.list_ledgers'),
                        'url' => 'ledger',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ],
                    [
                        'id' => '71',
                        'text' => trans($lang_mod . '.import_ledger'),
                        'url' => 'ledger/import',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ],

                    [
                        'id' => '72',
                        'text' => trans($lang_mod . '.ledger_revision'),
                        'url' => 'ledger/revision',
                        'params' => [],
                        'icon' => "<i class='fa fa-angle-double-right'></i>",
                        'level-icon' => "",
                        'children' => []
                    ]

                ]
            ],

            // [
            //     'id' => '',
            //     'text' => 'Settings',
            //     'url' => '#',//'admin/setting',
            //     'params' => [],
            //     'icon' => "<i class='app-menu__icon fa fa-gears'></i>",
            //     'level-icon' => "<i class='treeview-indicator fa fa-angle-right pull-right'></i>",
            //     'children' => [
            //         [
            //             'id' => '61',
            //             'text' => trans($lang_mod . '.define_dimension'),
            //             'url' => 'setting/dimension',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],
            //         [
            //             'id' => '62',
            //             'text' => trans($lang_mod . '.define_mappings_item'),
            //             'url' => 'setting/mappings-item',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => []
            //         ],
            //         [
            //             'id' => '63',
            //             'text' => trans($lang_mod . '.create_mappings'),
            //             'url' => '#',
            //             'params' => [],
            //             'icon' => "<i class='fa fa-angle-double-right'></i>",
            //             'level-icon' => "",
            //             'children' => [
            //             ]
            //         ]
            //     ]
            // ]

            
            
        ];
        
        return $this->renderMenu($menu);
    }
    
    protected function renderMenu($menu=[]){
        $html = '';
        
        if(!empty($menu)){
            foreach($menu as $k => $item){
                //$item['id'] = strval($k+1);
                $pid = $k + 1;
                $item['id'] = hash('crc32', $pid);
                $pa = '';
                $child = $this->childMenu($item['children'], $pid, $pa, 1);
                if($child == ''){
                    $query_string = Vii::queryStringBuilder(['mid' => $item['id']]);
                    $_active = '';
                    if(strval(Request::input('mid')) == $item['id']){
                        $_active = 'active';
                    }
                    $html .= "<li>";
                    $html .= "<a href='".url($item['url'], $item['params']) . $query_string . "' class='app-menu__item ".$_active."'>";
                    $html .= $item['icon'] . "<span class='app-menu__label'>" . $item['text'] . "</span>";
                    $html .= "</a>";
                    $html .= "</li>";
                }
                else{
                    $html .= "<li class='treeview $pa'>";
                    $html .= "<a class='app-menu__item' data-toggle='treeview' href='".url($item['url'])."'>";
                    $html .= $item['icon'] . "<span class='app-menu__label'>" . $item['text'] . "</span>" . $item['level-icon'];
                    $html .= "</a>";
                    $html .= $child;
                    $html .= "</li>";
                }
            }
        }
        
        return $html;
        
    }
    
    protected function childMenu($children=[], $parent_id, &$parent_active='', $h=1){
        if(empty($children))
            return '';
        
        $html = '';
        foreach($children as $k => $item){
            //$item['id'] = $parent_id . ($k+1);
            $cid = $parent_id . ($k + 1);
            $item['id'] = hash('crc32', $cid);
            $query_string = '';
            if(empty($item['children']))
                $query_string = Vii::queryStringBuilder(['mid' => $item['id']]);
            
            $_active = '';
            if(strval(Request::input('mid')) == $item['id']){
                $_active = 'active';
                $parent_active = 'is-expanded';//'active';
            }
            $pa = '';
            $child = $this->childMenu($item['children'], $cid, $pa, $h + 1);
            if($child == ''){
                $html .= "<li>";
                $html .= "<a class='treeview-item $_active' href='".url($item['url'], $item['params']) . $query_string . "'>";
                $html .= $item['icon'] . "<span class='menu-sub-item'>" . $item['text'] . "</span>";
                
            }
            else{
                $html .= "<li class='treeview $pa'>";
                $html .= "<a class='app-menu__item' data-toggle='treeview' href='".url($item['url'])."'>";
                $html .= $item['icon'] . "<span class='menu-sub-item'>" . $item['text'] . "</span>";
                $html .= $item['level-icon'];
            }
            
            $html .= "</a>";
            $html .= $child;
            $html .= "</li>";
        }
        
        return "<ul class='treeview-menu'>$html</ul>";
    }
}