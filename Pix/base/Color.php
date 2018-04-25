<?php

namespace Ga\Pix\base;

/**
 * 三角形的坐标点
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

//    public function set($r, $g, $b, $a) {
//        $this->r = $r;
//        $this->g = $g;
//        $this->b = $b;
//        $this->a = $a;
//        return true;
//    }
//
//    public function get() {
//        return array($this->r, $this->g, $this->b, $this->a);
//    }

    public function setRand() {
        $this->r = rand(0, 255);
        $this->g = rand(0, 255);
        $this->b = rand(0, 255);
        $this->a = rand(0, 127);
        return array($this->r, $this->g, $this->b, $this->a);
    }

    /**
     * 适应率公式，像素点差额的和
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
