<?php

namespace denis909\yii;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

class CascadeConfig
{

    public static $paths = [];

    public static function addPath(string $path, $checkPath = true)
    {
        $realpath = realpath($path);

        if (!$realpath)
        {
            if ($checkPath)
            {
                throw new Exception('Path not found: ' . $path);
            }

            $realpath = $path;
        }

        if (array_search($realpath, static::$paths))
        {
            static::$paths[] = $realpath;
        }
    }

    public static function findFiles(string $file)
    {
        $return = [];
        
        foreach(static::$paths as $path)
        {
            $filename = $path .'/' . $file;

            if (is_file($filename))
            {
                $return[] = $filename;
            }
        }

        return $return;
    }

    public static function requireOnce(string $file)
    {
        $files = static::findFiles($file);

        foreach($files as $filename)
        {
            require_once($filename);
        }
    }

    public static function require(string $file)
    {
        $files = static::findFiles($file);

        foreach($files as $filename)
        {
            require($filename);
        }
    }

    public static function mergeArray(string $file, array $return = [])
    {
        $files = static::findFiles($file);

        foreach($files as $filename)
        {
            $return = ArrayHelper::merge($return, require $filename);
        }

        return $return;
    }

    public static function mergeContent(string $file, string $devider = "\n")
    {
        $return = '';

        $files = static::findFiles($file);

        $i = 0;

        foreach($files as $filename)
        {
            if ($i > 0)
            {
                $return .= $devider;
            }

            $return .= file_get_contents($filename);

            $i++;
        }
        
        return $return;
    }
    
}