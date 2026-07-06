<?php

namespace App\Services;

use App\Models\CoreConfig;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SystemData;

class SettingsService
{
    public function getTabsStructure(): array
    {
        return [
            'general_tab' => ['label' => 'GENERAL'],
            'design_tab' => ['label' => 'DESIGN'],
            'security_tab' => ['label' => 'SECURITY'],
            'database_tab' => ['label' => 'DATABASE CONNECTIONS'],
            'software_connections_tab' => ['label' => 'SOFTWARE CONNECTIONS'],
            'permissions_tab' => ['label' => 'PERMISSIONS'],
        ];
    }

    public function getConfigStructure(): array
    {
        return [
            'general' => [
                'tab' => 'general_tab',
                'label' => 'General',
                'groups' => [
                    'country' => [
                        'label' => 'Country Options',
                        'fields' => [
                            'default' => [
                                'label' => 'Default Country',
                                'type' => 'select',
                                'options' => SystemData::getCountries(),
                            ],
                            'allow' => [
                                'label' => 'Allow Countries',
                                'type' => 'multiselect',
                                'options' => SystemData::getCountries(),
                            ],
                            'optional_zip' => [
                                'label' => 'Zip/Postal Code is Optional for',
                                'type' => 'multiselect',
                                'options' => SystemData::getCountries(),
                            ],
                            'eu_countries' => [
                                'label' => 'European Union Countries',
                                'type' => 'multiselect',
                                'options' => SystemData::getCountries(),
                            ],
                            'top_destinations' => [
                                'label' => 'Top destinations',
                                'type' => 'multiselect',
                                'options' => SystemData::getCountries(),
                            ]
                        ]
                    ],
                    'region' => [
                        'label' => 'State Options',
                        'fields' => [
                            'state_required' => [
                                'label' => 'State is Required for',
                                'type' => 'multiselect',
                                'options' => SystemData::getCountries(),
                            ],
                            'display_all' => [
                                'label' => 'Allow to Choose State if It is Optional for Country',
                                'type' => 'select',
                                'options' => ['1' => 'Yes', '0' => 'No']
                            ]
                        ]
                    ],
                    'locale' => [
                        'label' => 'Locale Options',
                        'fields' => [
                            'timezone' => [
                                'label' => 'Timezone',
                                'type' => 'select',
                                'options' => SystemData::getTimezones()
                            ],
                            'code' => [
                                'label' => 'Locale',
                                'type' => 'select',
                                'options' => SystemData::getLocales()
                            ],
                            'firstday' => [
                                'label' => 'First Day of the Week',
                                'type' => 'select',
                                'options' => [
                                    '0' => 'Sunday',
                                    '1' => 'Monday'
                                ]
                            ],
                            'weekend' => [
                                'label' => 'Weekend Days',
                                'type' => 'multiselect',
                                'options' => [
                                    '0' => 'Sunday',
                                    '6' => 'Saturday'
                                ]
                            ]
                        ]
                    ],
                    'store_information' => [
                        'label' => 'Store Information',
                        'fields' => [
                            'name' => ['label' => 'Store Name', 'type' => 'text'],
                            'phone' => ['label' => 'Store Phone Number', 'type' => 'text'],
                            'hours' => ['label' => 'Store Hours of Operation', 'type' => 'text'],
                            'country_id' => [
                                'label' => 'Country',
                                'type' => 'select',
                                'options' => SystemData::getCountries()
                            ],
                            'region_id' => ['label' => 'Region/State', 'type' => 'text'],
                            'postcode' => ['label' => 'ZIP/Postal Code', 'type' => 'text'],
                            'city' => ['label' => 'City', 'type' => 'text'],
                            'street_line1' => ['label' => 'Street Address', 'type' => 'text'],
                            'street_line2' => ['label' => 'Street Address Line 2', 'type' => 'text'],
                            'vat_number' => ['label' => 'VAT Number', 'type' => 'text'],
                        ]
                    ],
                    'single_store_mode' => [
                        'label' => 'Single-Store Mode',
                        'fields' => [
                            'enabled' => [
                                'label' => 'Enable Single-Store Mode',
                                'type' => 'select',
                                'options' => [
                                    '0' => 'No',
                                    '1' => 'Yes'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'web' => [
                'tab' => 'general_tab',
                'label' => 'Web',
                'groups' => [
                    'url_options' => [
                        'label' => 'Url Options',
                        'fields' => [
                            'add_store_code' => ['label' => 'Add Store Code to Urls', 'type' => 'select', 'options' => ['0' => 'No', '1' => 'Yes']]
                        ]
                    ]
                ]
            ],
            'currency' => [
                'tab' => 'general_tab',
                'label' => 'Currency Setup',
                'groups' => [
                    'currency_options' => [
                        'label' => 'Currency Options',
                        'fields' => [
                            'base' => ['label' => 'Base Currency', 'type' => 'select', 'options' => SystemData::getCurrencies()]
                        ]
                    ]
                ]
            ],
            'store_emails' => [
                'tab' => 'general_tab',
                'label' => 'Store Email Addresses',
                'groups' => [
                    'general_contact' => [
                        'label' => 'General Contact',
                        'fields' => [
                            'sender_name' => ['label' => 'Sender Name', 'type' => 'text'],
                            'sender_email' => ['label' => 'Sender Email', 'type' => 'email']
                        ]
                    ]
                ]
            ],
            'contacts' => [
                'tab' => 'general_tab',
                'label' => 'Contacts',
                'groups' => [
                    'contact_us' => [
                        'label' => 'Contact Us',
                        'fields' => [
                            'enable' => ['label' => 'Enable Contact Us', 'type' => 'select', 'options' => ['1' => 'Yes', '0' => 'No']]
                        ]
                    ]
                ]
            ],
            'reports' => [
                'tab' => 'general_tab',
                'label' => 'Reports',
                'groups' => [
                    'dashboard' => [
                        'label' => 'Dashboard',
                        'fields' => [
                            'ytd_start' => ['label' => 'Year-To-Date Starts', 'type' => 'select', 'options' => ['1' => 'January', '2' => 'February']]
                        ]
                    ]
                ]
            ],
            'content_management' => [
                'tab' => 'general_tab',
                'label' => 'Content Management',
                'groups' => [
                    'wysiwyg' => [
                        'label' => 'WYSIWYG Options',
                        'fields' => [
                            'enabled' => ['label' => 'Enable WYSIWYG Editor', 'type' => 'select', 'options' => ['1' => 'Yes', '0' => 'No']]
                        ]
                    ]
                ]
            ],
            'new_relic' => [
                'tab' => 'general_tab',
                'label' => 'New Relic Reporting',
                'groups' => [
                    'general' => [
                        'label' => 'General',
                        'fields' => [
                            'enable' => ['label' => 'Enable New Relic Integration', 'type' => 'select', 'options' => ['0' => 'No', '1' => 'Yes']]
                        ]
                    ]
                ]
            ],
            'advanced_reporting' => [
                'tab' => 'general_tab',
                'label' => 'Advanced Reporting',
                'groups' => [
                    'general' => [
                        'label' => 'General',
                        'fields' => [
                            'enable' => ['label' => 'Enable Advanced Reporting', 'type' => 'select', 'options' => ['1' => 'Yes', '0' => 'No']]
                        ]
                    ]
                ]
            ],
            'design' => [
                'tab' => 'design_tab',
                'label' => 'Design',
                'groups' => [
                    'theme' => [
                        'label' => 'Theme Settings',
                        'fields' => [
                            'color' => ['label' => 'Primary Accent Color', 'type' => 'color']
                        ]
                    ]
                ]
            ],
            'security' => [
                'tab' => 'security_tab',
                'label' => 'Security',
                'groups' => [
                    'session' => [
                        'label' => 'Session Options',
                        'fields' => [
                            'timeout' => ['label' => 'Session Timeout (Minutes)', 'type' => 'number']
                        ]
                    ]
                ]
            ],
            'database' => [
                'tab' => 'database_tab',
                'label' => 'Database Connections',
                'groups' => [
                    'tracker' => [
                        'label' => 'DB Tracker Connection',
                        'fields' => [
                            'enable' => [
                                'label' => 'Enable DB Tracker',
                                'type' => 'select',
                                'options' => [
                                    '1' => 'Yes',
                                    '0' => 'No'
                                ]
                            ],
                            'host' => ['label' => 'Host', 'type' => 'text'],
                            'port' => ['label' => 'Port', 'type' => 'number'],
                            'dbname' => ['label' => 'Database Name', 'type' => 'text'],
                            'username' => ['label' => 'Username', 'type' => 'text'],
                            'password' => ['label' => 'Password', 'type' => 'password'],
                        ]
                    ]
                ]
            ],
            'software_connections' => [
                'tab' => 'software_connections_tab',
                'label' => 'Software Connections',
                'groups' => [
                    'products' => [
                        'label' => 'Product Connections',
                        'fields' => [
                            'list' => [
                                'label' => 'Connected Products',
                                'type' => 'repeater',
                                'columns' => [
                                    'name' => 'Product Name',
                                    'host' => 'Host / IP',
                                    'port' => 'Port',
                                    'username' => 'Username',
                                    'password' => 'Password'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'permissions' => [
                'tab' => 'permissions_tab',
                'label' => 'Roles & Rules',
                'groups' => [
                    'roles' => [
                        'label' => 'System Roles (ABAC Rules)',
                        'fields' => [
                            'list' => [
                                'label' => 'Roles List',
                                'type' => 'repeater',
                                'columns' => [
                                    'id' => 'Role ID (e.g. content_manager)',
                                    'name' => 'Role Name',
                                    'description' => 'Description & Access Rules'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getSettingsData(string $section, string $scope, string $scopeId): array
    {
        $structure = $this->getConfigStructure();
        $values = [];
        $useSystem = [];

        if (!isset($structure[$section])) {
            return ['values' => $values, 'useSystem' => $useSystem];
        }

        foreach ($structure[$section]['groups'] as $groupId => $group) {
            foreach ($group['fields'] as $fieldId => $field) {
                $path = "{$section}/{$groupId}/{$fieldId}";
                
                if ($scope === 'default') {
                    $values[$path] = CoreConfig::getValue($path, 'default', 0);
                    $useSystem[$path] = false;
                } else {
                    $exact = CoreConfig::getExactValue($path, $scope, $scopeId);
                    if ($exact !== null) {
                        $values[$path] = $exact;
                        $useSystem[$path] = false;
                    } else {
                        // Inherited value
                        $values[$path] = CoreConfig::getValue($path, 'default', 0);
                        $useSystem[$path] = true;
                    }
                }
            }
        }

        return ['values' => $values, 'useSystem' => $useSystem];
    }

    public function getTreeStructure(): array
    {
        $tabs = $this->getTabsStructure();
        $structure = $this->getConfigStructure();
        $tree = [];
        
        foreach ($tabs as $tabId => $tab) {
            $tree[$tabId] = $tab;
            $tree[$tabId]['sections'] = [];
        }
        foreach ($structure as $secId => $sec) {
            if (isset($tree[$sec['tab']])) {
                $tree[$sec['tab']]['sections'][$secId] = $sec;
            }
        }

        return $tree;
    }

    public function saveSettings(string $section, string $scope, string $scopeId, array $config, array $useSystem): bool
    {
        $structure = $this->getConfigStructure();
        if (!isset($structure[$section])) {
            return false;
        }

        $fields = [];
        $fieldTypes = [];
        foreach ($structure[$section]['groups'] as $groupId => $group) {
            foreach ($group['fields'] as $fieldId => $field) {
                $path = "{$section}/{$groupId}/{$fieldId}";
                $fields[] = $path;
                $fieldTypes[$path] = $field['type'] ?? 'text';
            }
        }

        foreach ($fields as $path) {
            if ($scope !== 'default' && isset($useSystem[$path])) {
                // Delete exact config to fallback/inherit
                CoreConfig::where('path', $path)
                    ->where('scope', $scope)
                    ->where('scope_id', $scopeId)
                    ->delete();
                Cache::forget("core_config_{$scope}_{$scopeId}_{$path}");
            } else {
                if (isset($config[$path])) {
                    if ($fieldTypes[$path] === 'repeater') {
                        // Ensure it's stored as JSON
                        $val = json_encode(array_values((array)$config[$path]), JSON_UNESCAPED_UNICODE);
                    } else {
                        $val = is_array($config[$path]) ? implode(',', $config[$path]) : $config[$path];
                    }
                    CoreConfig::setValue($path, $val, $scope, $scopeId);
                }
            }
        }
        return true;
    }
}
