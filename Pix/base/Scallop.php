<?php

namespace Ga\Pix\base;

use Ga\Pix\Config;

/**
 * 扇贝，一个扇贝包含多个颜色点，这些颜色点属于扇贝的基因
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/GeneticAlgorithmPHP.git
 * @date 2018/04/24
 */
class Scallop {

    public $pixs = array(); //扇贝上的像素点
    public $fitness; //当前扇贝的适应度
    public $generation; //扇贝第几代
    public $scpName; //扇贝的名字，每一个扇贝都有唯一的名字，用数字标识
    public $variationCnt; //基因变异个数
    public $baseImgPixs; //基础图片
    public $baseImgWidth; //基础图片宽度
    public $baseImgHeight; //基础图片高度

    public function __construct($Generation, $scpName = 0, $baseImgWidth = 0, $baseImgHeight = 0) {
        $this->generation = $Generation;
        $this->scpName = $scpName;
        $this->baseImgWidth = $baseImgWidth;
        $this->baseImgHeight = $baseImgHeight;
        $this->variationCnt = Config::variationCnt;
        $this->initScp();
    }

    /**
     * 设置当前扇贝属于第几代
     * @param type $generation
     */
    public function setGeneration($generation) {
        $this->generation = $generation;
    }

    /**
     * 设置当前扇贝名字（每个扇贝都有一个名字）
     * @param type $scpName
     */
    public function setScpName($scpName) {
        $this->scpName = $scpName;
    }

    /**
     * 设置基础图片
     * @param type $base
     */
    public function setBaseImgPixs($base) {
        $this->baseImgPixs = $base;
    }

    /**
     * 初始化当前扇贝
     * 分两步
     * 1、创建扇贝每个像素点的颜色值
     * 2、计算当前扇贝的fitness（适应度）
     */
    public function initScp() {
        for ($x = 0; $x < $this->baseImgHeight; $x++) {
            for ($y = 0; $y < $this->baseImgWidth; $y++) {
                $this->pixs[$x][$y] = new Color();
            }
        }
        $this->calFitness();
    }

    /**
     * 计算适应率
     * 像素数*通道数/像素通道差额和
     * @return int
     */
    public function calFitness() {
        if (!$this->baseImgPixs) {
            return 0;
        }
        $pixS = 0;
        for ($x = 0; $x < $this->baseImgHeight; $x++) {
            for ($y = 0; $y < $this->baseImgWidth; $y++) {
                $pixS += $this->baseImgPixs[$x][$y]->fitness($this->pixs[$x][$y]);
            }
        }
        return $this->fitness = $this->baseImgHeight * $this->baseImgWidth * 4 / $pixS;
    }

    /**
     * 杂交
     * 此处杂交父母基因各区50%概率
     * @param Scallop $scpA
     * @param Scallop $scpB
     * @return boolean
     */
    public function createScpByParent(Scallop $scpA, Scallop $scpB) {
        for ($x = 0; $x < $this->baseImgHeight; $x++) {
            for ($y = 0; $y < $this->baseImgWidth; $y++) {
                if (rand(0, 1)) {
                    $this->pixs[$x][$y] = clone $scpA->pixs[$x][$y];
                } else {
                    $this->pixs[$x][$y] = clone $scpB->pixs[$x][$y];
                }
            }
        }
        return true;
    }

    /**
     * 变异
     * 根据变异像素数，为随机点设置新的颜色
     * @return boolean
     */
    public function variation() {
        for ($i = 0; $i < $this->variationCnt; $i++) {
            $this->pixs[rand(0, $this->baseImgWidth)][rand(0, $this->baseImgHeight)] = new Color();
        }
        return true;
    }

    /**
     * 画出当前扇贝
     * 到文件
     * @return type
     */
    public function drawScp() {
        $cavas = imagecreatetruecolor($this->baseImgWidth, $this->baseImgHeight);
        imagealphablending($cavas, false); //这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;  
        imagesavealpha($cavas, true); //这里很重要,意思是不要丢了$thumb图像的透明色;
        $bg = imagecolorallocatealpha($cavas, 0, 0, 0, 127); //拾取一个完全透明的颜色
        imagefill($cavas, 0, 0, $bg); //填充
        for ($x = 0; $x < $this->baseImgHeight; $x++) {
            for ($y = 0; $y < $this->baseImgWidth; $y++) {
                imagesetpixel($cavas, $x, $y, imagecolorallocatealpha($cavas, $this->pixs[$x][$y]->r, $this->pixs[$x][$y]->g, $this->pixs[$x][$y]->b, $this->pixs[$x][$y]->a)); //画三角形
            }
        }
        $toPath = Config::FamilyImgPath . '/' . $this->generation;
//        if (!file_exists($toPath)) {
//            mkdir("$toPath", 0777, true);
//        }
        imagepng($cavas, $toPath . '_' . $this->scpName . '.png'); //生成图片
        imagedestroy($cavas);
        return $toPath . '_' . $this->scpName . '.png';
    }

}
