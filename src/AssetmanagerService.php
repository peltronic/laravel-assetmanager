<?php
namespace Peltronic\Assetmanager;

// see:
// https://medium.com/@markustripp/laravel-5-5-package-development-e72f3e7a8f38
// https://github.com/markustripp/mongo/blob/master/src/MongoService.php
// http://laraveldaily.com/how-to-create-a-laravel-5-package-in-10-easy-steps/
// https://laracasts.com/discuss/channels/tips/developing-your-packages-in-laravel-5
// https://medium.com/@tabacitu/creating-laravel-5-packages-for-dummies-ec6a4ded2e93
// https://medium.com/@lasselehtinen/getting-started-on-laravel-package-development-a62110c58ba1
class AssetmanagerService
{

    private $_jsInlinePaths;
    private $_jsLibPaths;
    private $_cssInlinePaths;
    private $_service;

    public function __construct() 
    {
        $this->_jsInlinePaths = [];
        $this->_jsLibPaths = [];
        $this->_cssInlinePaths = [];
    }

    public function get() {
        return $this->_service;
    }

    // Can be called multiple times
    public function registerJsLibs($libPaths = [])
    {
        foreach ($libPaths as $l) {
            $this->pushFile($l,'js-lib');
        }
        \View::share('g_assetMgr', $this);
    }

    public function registerJsInlines($inlinePaths = [])
    {
        foreach ($inlinePaths as $l) {
            $this->pushFile($l,'js-inline');
        }
        \View::share('g_assetMgr', $this);
    }

    public function registerCssInlines($inlinePaths = [])
    {
        foreach ($inlinePaths as $l) {
            $this->pushFile($l,'css-inline');
        }
        \View::share('g_assetMgr', $this);
    }

    public function renderCssInlines()
    {
        $html = '';
        foreach ($this->_cssInlinePaths as $file) {
            $isLocal = $this->isLocal($file);
            if ($isLocal and !file_exists(public_path().$file)) {
                //continue; // local and not updated (? PSG)
                throw new \Exception('could not find css inline: '.$file);
            }
            $time = ($isLocal ? filemtime(public_path().$file) : null);
            $html .= '<link media="all" type="text/css" rel="stylesheet" href="'.$file.($time ? '?'.$time : '').'">'."\n";
        }

        return $html;
    } // render()

    public function renderJsInlines()
    {
        $html = '';
        foreach ($this->_jsInlinePaths as $file) {
            $isLocal = $this->isLocal($file);
            if ($isLocal and !file_exists(public_path().$file)) {
                continue; // local and not updated (? PSG) (or doesn't actually exist : %FIXME -- throw exception?)
            }
// %FIXME: put IS_THROTTLING...in the configuration file
            if (1 || defined('IS_THROTTLING_DISABLED') && IS_THROTTLING_DISABLED) { 
                $html .= '<script type="application/javascript" src="'.$file.'"></script>'."\n";
            } else {
                $time = ($isLocal ? filemtime(public_path().$file) : null);
                $html .= '<script type="application/javascript" src="'.$file.($time ? '?'.$time : '').'"></script>'."\n";
            }
        }

        return $html;
    } // render()

    public function renderJsLibs()
    {
        $html = '';
        foreach ($this->_jsLibPaths as $file) {
            $isLocal = $this->isLocal($file);
            if ($isLocal and !file_exists(public_path().$file)) {
                continue; // local and not updated (? PSG) (or doesn't actually exist)
            }
// %FIXME
            if (1 || defined('IS_THROTTLING_DISABLED') && IS_THROTTLING_DISABLED) {
                $html .= '<script type="application/javascript" src="'.$file.'"></script>'."\n";
            } else {
                $time = ($isLocal ? filemtime(public_path().$file) : null);
                $html .= '<script type="application/javascript" src="'.$file.($time ? '?'.$time : '').'"></script>'."\n";
            }
        }

        return $html;
    } // renderJsLibs()

    protected function pushFile($file,$type)
    {
        if ((substr($file, 0, 1) != '/') && !strstr($file, '//')) {
            $file = '/'.$file;
        }
        switch ($type) {
            case 'css-inline':
                $this->_cssInlinePaths[] = $file;
                break;
            case 'js-inline':
                $this->_jsIlnnePaths[] = $file;
                break;
            case 'js-lib':
                $this->_jsLibPaths[] = $file;
                break;
            default:
                throw new \Exception('Unrecognized push type: '.$type);
        }
    } // pushFile()

    protected function isLocal($file) 
    {
        $isLocal = (strstr($file, '//') ? 0 : 1);
        return $isLocal;
    }

    public function minifyJs()
    {
    }
    public function minifyCss()
    {
    }
}
