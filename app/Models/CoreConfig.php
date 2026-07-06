<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CoreConfig extends Model
{
    protected $table = 'core_config_data';
    protected $primaryKey = 'config_id';

    protected $fillable = [
        'scope',
        'scope_id',
        'path',
        'value',
    ];

    /**
     * Get exact value without fallback to check for inheritance.
     */
    public static function getExactValue($path, $scope = 'default', $scopeId = 0)
    {
        $config = self::where('path', $path)
            ->where('scope', $scope)
            ->where('scope_id', $scopeId)
            ->first();
            
        return $config ? $config->value : null;
    }

    /**
     * Magento-style scope config reader.
     * Returns the value for a specific path, falling back from scope specific to default.
     */
    public static function getValue($path, $scope = 'default', $scopeId = 0)
    {
        $cacheKey = "core_config_{$scope}_{$scopeId}_{$path}";
        
        return Cache::rememberForever($cacheKey, function () use ($path, $scope, $scopeId) {
            // First try to find exact match
            $config = self::where('path', $path)
                ->where('scope', $scope)
                ->where('scope_id', $scopeId)
                ->first();
                
            if ($config) {
                return $config->value;
            }

            // Fallback to default scope if exact match not found
            if ($scope !== 'default') {
                $defaultConfig = self::where('path', $path)
                    ->where('scope', 'default')
                    ->where('scope_id', 0)
                    ->first();
                    
                if ($defaultConfig) {
                    return $defaultConfig->value;
                }
            }
            
            return null;
        });
    }

    /**
     * Magento-style scope config writer.
     */
    public static function setValue($path, $value, $scope = 'default', $scopeId = 0)
    {
        self::updateOrCreate(
            ['scope' => $scope, 'scope_id' => $scopeId, 'path' => $path],
            ['value' => $value]
        );
        
        // Clear cache for this specific path
        Cache::forget("core_config_{$scope}_{$scopeId}_{$path}");
    }
}
