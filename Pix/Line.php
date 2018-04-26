<?php

namespace Ga\Pix;

use Ga\Pix\base\Color;
use Ga\Pix\base\Population;

/**
 * 中转点<br>
 * GA算法，这个类之前的属于PHP准备部分<br>
 * 这个类为GA算法做准备<br>
 * 这个类之后的，均属于GA算法的部分<br>
 * 依次为种群->扇贝->颜色值<br>
 * 类名Line并无太大意义，只是中间连接线的意思。。
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/GeneticAlgorithmPHP.git
 * @date 2018/04/24
 */
class Line {

    public $baseImgPixs; //基础对比图
    public $baseImgWidth; //基础图宽度
    public $baseImgHeight; //基础图高度
    public $pop; //种群

    public function __construct() {
        $this->getBaseImgPixs();
        $this->pop = new Population();
        $this->pop->setBaseImg($this->baseImgPixs, $this->baseImgWidth, $this->baseImgHeight);
        $this->pop->initPop();
        $this->pop->start();
    }

    /**
     * 获取基础图片信息
     * 每个扇贝都需要这些信息
     */
    public function getBaseImgPixs() {
        $img = getimagesize(Config::BaseImg);
        $this->baseImgWidth = $img[0];
        $this->baseImgHeight = $img[1];
        $baseImg = imagecreatefrompng(Config::BaseImg);
        for ($x = 0; $x < $this->baseImgHeight; $x++) {
            for ($y = 0; $y < $this->baseImgWidth; $y++) {
                $basePix = imagecolorsforindex($baseImg, imagecolorat($baseImg, $x, $y)); // 取得一点的颜色
                $this->baseImgPixs[$x][$y] = new Color(array($basePix['red'], $basePix['green'], $basePix['blue'], $basePix['alpha']));
            }
        }
        imagedestroy($baseImg);
    }

}
