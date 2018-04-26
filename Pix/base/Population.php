<?php

namespace Ga\Pix\base;

use Ga\Pix\base\Scallop;
use Ga\Pix\Config;

/**
 * 这是一个种群<br>
 * 这是1个种群，种群内包含多个扇贝
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @link https://github.com/kk1987n/GeneticAlgorithmPHP.git
 * @date 2018/04/24
 */
class Population {

    public $baseImgPixs; //基础对比图
    public $baseImgWidth; //基础图宽度
    public $baseImgHeight; //基础图高度
    public $scallops = array(); //扇贝
    public $scpCnt; //种群内扇贝的数量
    public $chdCnt; //每次迭代产生的孩子数量
    public $generation; //当前是第几代
    public $scpName = 1; //创建的扇贝的名字，每一个扇贝都有唯一的名字，用数字标识
    public $fitness = array(); //适应度-用来画曲线

    public function __construct($Generation = 1) {
        $this->generation = $Generation;
        $this->scpCnt = Config::FamilyCnt;
        $this->chdCnt = Config::ChdCnt;
    }

    public function setBaseImg($baseImgPixs, $baseImgWidth, $baseImgHeight) {
        $this->baseImgPixs = $baseImgPixs;
        $this->baseImgWidth = $baseImgWidth;
        $this->baseImgHeight = $baseImgHeight;
        return true;
    }

    /**
     * 初始种群，最开始的随机种群，这是第一代种群
     */
    public function initPop() {
        for ($i = 0; $i < $this->scpCnt; $i++) {
            $this->scpName++;
            $this->scallops[$i] = new Scallop($this->generation, $this->scpName, $this->baseImgWidth, $this->baseImgHeight);
            $this->scallops[$i]->setBaseImgPixs($this->baseImgPixs);
            $this->scallops[$i]->initScp();
        }
    }

    /**
     * 代数迭代
     */
    public function start() {
        $startTime = microtime(1);
        for ($x = 0; $x < Config::GenerationMaX; $x++) {
            echo $this->generation . PHP_EOL;
            $this->generation++;
            $this->killScp(); //杀死原种群中适应度最差的几个，补充上孩子
            $children = $this->birthChild(); //结合生出孩子
            $this->addToScp($children);
            $this->calFitness(); //计算适应度总和
            if ($this->generation % Config::drawByCnt == 0) {
                foreach ($this->scallops as $scp) {
                    $scp->drawScp();
                }
            }
        }
        $useTime = microtime(1) - $startTime;
        $memory = memory_get_usage() / (1024 * 1024);
        $this->fitnessPng($this->fitness, $useTime, $memory);
    }

    /**
     * 生小孩
     */
    public function birthChild() {
        $child = array();
        //随机两个结合，生出孩子
        for ($i = 0; $i < $this->chdCnt; $i++) {
            $scpA = $this->scallops[rand(0, count($this->scallops) / 2 - 1)]; //种群前半段随机一个
            $scpB = $this->scallops[rand(count($this->scallops) / 2, count($this->scallops) - 1)]; //种群后半段随机一个
            $child[$i] = $this->crossScp($scpA, $scpB); //生出的孩子
            $child[$i]->setBaseImgPixs($this->baseImgPixs); //告诉孩子基础图
            $child[$i]->calFitness(); //计算孩子的适应度
        }
        return $child;
    }

    /**
     * 交叉扇贝
     */
    public function crossScp(Scallop $scpA, Scallop $scpB) {
        $this->scpName++;
        $scp = new Scallop($this->generation, $this->scpName, $this->baseImgWidth, $this->baseImgHeight); //创建扇贝
        $scp->createScpByParent($scpA, $scpB); //注入基因
        $scp->variation(); //基因变异
        return $scp;
    }

    /**
     * 杀死较差的扇贝
     */
    public function killScp() {
        $temp = null;
        //冒泡排序所有的扇贝
        for ($i = 0; $i < count($this->scallops); $i++) {
            for ($j = 0; $j < count($this->scallops) - 1; $j++) {
                if ($this->scallops[$j]->fitness < $this->scallops[$j + 1]->fitness) {
                    $temp = $this->scallops[$j];
                    $this->scallops[$j] = $this->scallops[$j + 1];
                    $this->scallops[$j + 1] = $temp;
                }
            }
        }
        $cnt = count($this->scallops);
        for ($i = 0; $i < $cnt; $i++) {
            if ($i >= $this->scpCnt - $this->chdCnt) {//删除适应度最差的几个扇贝，删除数量正好是孩子的数量，把孩子补充上
                unset($this->scallops[$i]);
            }
        }
    }

    public function calFitness() {
        $this->fitness[$this->generation] = 0; //这个数记录了每一代扇贝的适应度总和（也可以求一下平均值），用来画一条适应度曲线图
        foreach ($this->scallops as $scp) {
            $this->fitness[$this->generation] += $scp->fitness; //计算适应度总和
        }
        return true;
    }

    /**
     * 把没被杀死的父辈，和孩子合起来
     * @param type $children
     */
    public function addToScp($children) {
        foreach ($children as $child) {
            $this->scallops[] = $child;
        }
        $this->scallops = array_merge($this->scallops);
    }

    /**
     * 画一个适应度曲线图
     * @param type $fitness
     */
    public function fitnessPng($fitness, $time, $mem) {
        $fitness = array_merge($fitness);
        $width = 1200;
        $height = 500;
//创建图像画布  
        $image = imagecreatetruecolor($width, $height);
//设置颜色  
        $red = imagecolorallocate($image, 255, 0, 0);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        imagestring($image, 1, $width - 80, $height - 30, round($time, 4) . 's', $red); //画时长
        imagestring($image, 1, $width - 80, $height - 20, round($mem, 4) . 'Mb', $red); //画内存

        $oneW = $width / (count($fitness) + 1);
        $fitMax = 0;
        $fitMin = 1;
        foreach ($fitness as $fit) {
            if ($fit > $fitMax) {
                $fitMax = $fit;
            } elseif ($fit < $fitMin) {
                $fitMin = $fit;
            }
        }
        $l = $fitMax - $fitMin;
        for ($i = 0; $i < count($fitness); $i++) {
            imagefilledellipse($image, $i * $oneW, $height - ($fitness[$i] - $fitMin) / $l * $height, 1, 1, $red); //画点
//            imagestring($image, 1, $i * $oneW, $height - ($fitness[$i] - $fitMin) / $l * $height, round($fitness[$i], 4), $red); //画适应度数字
        }

        imagepng($image, '适应度曲线.png'); //生成图片
//设置header  
        header('Content-Type:image/png');
//输出图片格式  
        imagepng($image);
//清理内存  
        imagedestroy($image);
    }

    /**
     * PHP删除文件夹机器内容的方法
     * @param type $dir
     * @return boolean
     */
    function deldir($dir) {
        if (!file_exists($dir)) {
            return;
        }
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

}
