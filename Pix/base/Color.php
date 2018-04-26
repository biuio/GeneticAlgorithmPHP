<?php

namespace Ga\Pix\base;

/**
 * 颜色点<br>
 * 包含四个数学，r:红色，g:绿色，b:蓝色，a:透明度
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/GeneticAlgorithmPHP.git
 * @date 2018/04/24
 */
class Color {

    public $r;
    public $g;
    public $b;
    public $a;

    public function __construct($rgba = array()) {
        if (empty($rgba)) {
            $this->setRand();
        } else {
            $this->r = $rgba[0];
            $this->g = $rgba[1];
            $this->b = $rgba[2];
            $this->a = $rgba[3];
        }
    }

    /**
     * 设置随机值
     * @return type
     */
    public function setRand() {
        $this->r = rand(0, 255);
        $this->g = rand(0, 255);
        $this->b = rand(0, 255);
        $this->a = rand(0, 127);
        return array($this->r, $this->g, $this->b, $this->a);
    }

    /**
     * 适应率公式，像素点差额的和
     * 当前像素与对比像素之间的差别
     * @param type $Pix
     * @return type
     */
    public function fitness(Color $Pix) {
        $r = abs($this->r - $Pix->r);
        $g = abs($this->g - $Pix->g);
        $b = abs($this->b - $Pix->b);
        $a = abs($this->a - $Pix->a);
        return $r + $g + $b + $a * 2;
    }

}
