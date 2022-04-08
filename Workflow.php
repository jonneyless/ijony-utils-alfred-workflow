<?php

/**
 * Alfred 工具箱
 */
class Workflow
{
    /**
     * @param $type
     * @param $keyword
     */
    public function __construct($type, $keyword)
    {
        $this->type = $type;
        $this->keyword = $keyword;
    }

    /**
     * @return void
     */
    public function result()
    {
        $funcName = 'tool' . $this->toolMaps[$this->type]['function'];

        $this->items[] = $this->$funcName();

        echo json_encode(['items' => $this->items]);
    }

    /**
     * @param $result
     * @param string $textPrefix
     *
     * @return array
     */
    private function parseItem($result, $textPrefix = '')
    {
        $title = $result;
        if ($textPrefix) {
            $title = $textPrefix . ' ' . $result;
        }

        return array_merge($this->item, [
            'title' => $title,
            'subtitle' => '回车复制到粘贴板',
            'arg' => $result,
        ]);
    }

    /**
     * @param $notice
     *
     * @return string[]
     */
    private function noticeItem($notice)
    {
        return array_merge($this->item, [
            'title' => $notice,
        ]);
    }

    /**
     * @return array
     */
    private function toolTimestramp()
    {
        $prefix = '';
        $result = time();

        if ($this->keyword) {
            if ($this->keyword == 'now') {
                $prefix = '当前时间:';
                $result = date("Y-m-d H:i:s");
            } else if (preg_match("/^\d{10}$/", $this->keyword)) {
                $result = date("Y-m-d H:i:s", $this->keyword);
            } else if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $this->keyword)) {
                $result = strtotime($this->keyword);
            } else if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->keyword)) {
                $result = strtotime($this->keyword . ' 00:00:00');
            } else if (preg_match("/^[+-]\d+ [a-z]$/", $this->keyword)) {
                $result = strtotime($this->keyword, time());
            } else {
                return $this->noticeItem($this->toolMaps['jtime']['notice']);
            }
        } else {
            $prefix = '当前时间戳:';
        }

        return $this->parseItem($result, $prefix);
    }

    /**
     * @param $type
     * @param $keyword
     *
     * @return \workflow
     */
    public static function init($type, $keyword)
    {
        return new self($type, $keyword);
    }

    /**
     * @var array
     */
    private $items;
    /**
     * @var string[]
     */
    private $item = [
        'title' => '请输入参数',
        'icon' => 'icon.png',
    ];
    /**
     * @var string[]
     */
    private $toolMaps = [
        'jtime' => [
            'function' => 'Timestramp',
            'notice' => '请输入时间戳或者日期',
        ],
    ];
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $keyword;
}