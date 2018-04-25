<?php

spl_autoload_register('autoload');

/**
 * 类库自动加载
 * @param string $class 对象类名
 * @return void
 */
function autoload($class) {
// 检查是否存在映射
    if (false !== strpos($class, '\\')) {
        $filename = str_replace('\\', '/', $class) . '.php';
//            print_r(is_file($filename) . $filename . '<br/>');
        $FirstNamespace = substr($filename, 0, strpos($filename, '/'));
        switch ($FirstNamespace) {
            case 'Ga':
                $filename = GA . '/' . substr($filename, 3); //去掉Ga/这个开头
                break;
            default :
                $filename = $filename;
        }
//        print_r(is_file($filename) . $filename . '<br/>' . dirname(GA));
        if (is_file($filename)) {
            include $filename;
        }
    }
}
