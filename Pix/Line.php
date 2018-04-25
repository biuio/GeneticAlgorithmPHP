<?php

namespace Ga\Pix;

use Ga\Pix\base\Population;

/**
 * 核心
 *
 * @author Administrator
 */
class Line {

    public $pop;
    public $Generation = 1;

    public function __construct() {
        $this->pop = new Population($this->Generation);
        $this->pop->initPop();
        $this->pop->start();
    }

}
