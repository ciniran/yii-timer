<?php
/**
 * Created by PhpStorm.
 * User: 谢波
 * Date: 2018/8/13
 * Time: 10:37
 */

namespace ciniran\timer;


use yii\base\BaseObject;
use yii\log\Logger;

/**
 * Class Timer
 * 用于分析程序执行时间的小工具
 * 本工具为单例执行
 * 返回的执行时间为单位为毫秒
 * @package ciniran\timer
 */
class Timer extends BaseObject
{
    /**
     * @var float $startTime 开始时间
     */
    private $startTime;
    /**
     * @var float $endTime 结束时间
     */
    private $endTime;
    /**
     * @var float $useTime 当前执行时间
     */
    private $useTime;
    /**
     * @var float $allTime 总执行时间
     */
    private $allTime;
    /**
     * @var string $startClass 开始类
     */
    private $startClass;
    /**
     * @var string $startLine 开始调用行
     */
    private $startLine;
    /**
     * @var string $endClass 结束类
     */
    private $endClass;
    /**
     * @var string $endLine 结束行
     */
    private $endLine;
    /**
     * @var array $record 执行记录
     */
    private $records;
    /**
     * @var float $point 时间切点
     */
    private $point;
    /**
     * @var bool $saveFile 日志是否保存为文件
     */
    public $saveFile = false;
    /**
     * @var string $logFileName 日志文件名
     * 默认在web目录下
     */
    public $logFileName = 'timer.txt';

    /**
     * 开始定位时间点
     */
    public function start()
    {
        $track = debug_backtrace();
        $this->startClass = $track[0]['file'];
        $this->startLine = $track[0]['line'];
        $this->startTime = microtime(true);
        $this->point = $this->startTime;
    }

    /**
     * 执行期，监控时间点
     */
    public function point()
    {
        $track = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $this->startClass = $track[0]['file'];
        $this->startLine = $track[0]['line'];
        $this->endTime = microtime(true);
        $this->endClass = $track[1]['file'];
        $this->endLine = $track[1]['line'];
        $this->useTime = round(($this->endTime - $this->point) * 1000, 3);
        $this->allTime = round(($this->endTime - $this->startTime) * 1000, 3);
        $this->addRecord();
        $this->point = $this->endTime;
    }

    /**
     * 结束监控时间点
     */
    public function end()
    {
        $this->point();
        $this->saveToFile();
        \Yii::$app->getLog()->getLogger()->log($this->records, Logger::LEVEL_ERROR, 'timer');
    }

    private function addRecord()
    {
        $array = $this->toArray();
        $this->records[] = $array;
    }

    private function toArray()
    {
        $res = [];
        $label = $this->attributesLabel();
        foreach ($this as $key => $value) {
            if (in_array($key, array_keys($label))) {
                $res[$label[$key]] = $value;
            }
        }
        return $res;
    }

    private function attributesLabel()
    {
        return [
            'startTime' => '开始时间',
            'endTime' => '结束时间',
            'startClass' => '开始文件',
            'endClass' => '结束文件',
            'startLine' => '开始行',
            'endLine' => '结束行',
            'useTime' => '当前用时',
            'allTime' => '总用时',
        ];
    }
    /**
     * @return mixed
     */
    public function getRecords()
    {
        return $this->records;
    }

    private function saveToFile()
    {
        if (!$this->saveFile) {
            return;
        }
        $logfile = fopen($this->logFileName, 'ab');
        foreach ($this->records as $item) {
            $str = json_encode($item,JSON_UNESCAPED_UNICODE);
            fwrite($logfile, $str);
        }
        fclose($logfile);
    }

}