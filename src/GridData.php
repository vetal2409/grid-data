<?php

/**
 * @copyright Copyright &copy; Vitali Sydorenko, 2015
 * @package grid-data
 * @version 1.0.0
 */

namespace vetal2409\grid;

class GridData
{
    private $extUrl = '';
    private $extPath = '';
    private $get = array();

    public function __construct($vendorUrl, $vendorPath)
    {
        $this->extUrl = $vendorUrl . '/vetal2409/grid-data';
        $this->extPath = $vendorPath . '/vetal2409/grid-data';
        if (isset($_GET)) {
            $this->get = $_GET;
        }
    }

    /**
     * Creates a widget instance and runs it.
     * The widget rendering result is returned by this method.
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @return string the rendering result of the widget.
     */
    public function widget(array $config = array())
    {
        if (self::array_keys_exists(array('dataProvider', 'columns'), $config)) {
            return $this->render($config);
        }
        return false;
    }

    private function render($config)
    {
        $info = $this->generateData($config['columns'], $config['dataProvider']);
        $result = '';
        $result .= $this->assets();
        $result .= '<div class="table-responsive">';
        $result .= '<table class="grid-data-table table table-bordered" data-toggle="table" data-height="700">';
        $result .= '<thead>';
        $result .= '<tr>';
        foreach ($info['labels'] as $keyL => $label) {
            $result .= '<th>';
            $result .= $label;
            $result .= '</th>';
        }
        $result .= '</tr>';
        $result .= '</thead>';
        $result .= '<tbody>';
        foreach ($info['rows'] as $row) {
            $result .= '<tr>';
            foreach ($row as $v) {
                $result .= '<td>';
                $result .= $v;
                $result .= '</td>';
            }
            $result .= '</tr>';
        }
        $result .= '</tbody>';
        $result .= '</table>';
        $result .= '</div>';
        return $result;
    }

    private function assets()
    {
        //$result = '<link rel="stylesheet" href="' . $this->extUrl . '/../../bower-asset/bootstrap/dist/css/bootstrap.min.css"/>';
        $result = '<link rel="stylesheet" href="' . $this->extUrl . '/assets/css/style.css"/>';
        //$result .= '<script src="' . $this->extUrl . '/../../bower-asset/jquery/dist/jquery.min.js"></script>';
        $result .= '<script src="' . $this->extUrl . '/assets/js/main.js"></script>';
        return $result;
    }

    /**
     * @param array $columns
     * @param array $dataProvider
     * @return array
     */
    public function generateData(array $columns, array $dataProvider)
    {
        $result['attributes'] = $result['labels'] = $result['rows'] = array();
        foreach ($columns as $col_key => $col_val) {
            $attribute = $label = '';
            if (is_array($col_val)) {
                if (array_key_exists('type', $col_val)) {
                    switch ($col_val['type']) {
                        case 'SerialColumn':
                            $attribute = '';
                            $label = '<span class="serial-column-label">#</span>';
                            break;
                        case 'CheckboxColumn':
                            $attribute = '';
                            $label = '<label><input type="checkbox" class="checkbox-column-label"></label>';
                            break;
                        default:
                            continue;
                    }
                } else {
                    $attribute = array_key_exists('attribute', $col_val) ? $col_val['attribute'] : $col_key;
                    $label = array_key_exists('label', $col_val) ? $col_val['label'] : $attribute;
                }
            } else {
                $attribute = $col_val;
                $label = $col_val;
            }
            $result['attributes'][] = $attribute;
            $sortInfo = $this->getSortInfo($attribute);
            $directionSymbol = '';
            if ($sortInfo['direction'] === 'asc') {
                $directionSymbol = ' <i class="fa fa-chevron-down"></a></th>';
            } elseif ($sortInfo['direction'] === 'desc') {
                $directionSymbol = ' </i><i class="fa fa-chevron-up"></i>';
            }

            $result['labels'][] = $attribute
                ? '<a href="' . $sortInfo['http_query'] . '"  title="' . $attribute . '">' . $label . $directionSymbol . '</a>'
                : $label;
        }
        foreach ($dataProvider as $key => $data) {
            foreach ($columns as $k => $v) {
                $res = '';
                if (is_array($v)) {
                    if (array_key_exists('type', $v)) {
                        switch ($v['type']) {
                            case 'SerialColumn':
                                $res = $key + 1;
                                break;
                            case 'CheckboxColumn':
                                $res = '';
                                if (array_key_exists('attributeValue', $v)) {
                                    $res = '<label><input type="checkbox" class="checkbox-column" value="' . $data[$v['attributeValue']] . '"></label>';
                                }
                                break;
                            default:
                                continue;
                        }
                    } else {
                        $lab = array_key_exists('attribute', $v) ? $v['attribute'] : $k;
                        $res = array_key_exists('value', $v) ? $v['value']($data) : $data[$lab];
                    }
                } else {
                    $res = $data[$v];
                }
                $result['rows'][$key][] = $res;
            }
        }
        return $result;
    }

    /**
     * (PHP 4 &gt;= 4.0.7, PHP 5)<br/>
     * @param array $keys <p>
     * Value to check.
     * </p>
     * @param array $search <p>
     * An array with keys to check.
     * </p>
     * @return bool true on success or false on failure.
     */
    public static function array_keys_exists(array $keys, $search)
    {
        return count(array_intersect_key(array_flip($keys), $search)) === count($keys);
    }

    /**
     * @param $attribute
     * @return string
     */
    public function getSortInfo($attribute)
    {
        $get = $this->get;
        $sortPrefix = '';
        $resultInfo['direction'] = '';
        if (array_key_exists('sort', $get)) {
            preg_match('/([-]?)(.*)/', (string)$get['sort'], $sortInfo);
            if ($attribute === $sortInfo[2]) {
                if ($sortInfo[1]) {
                    $resultInfo['direction'] = 'desc';
                } else {
                    $sortPrefix = '-';
                    $resultInfo['direction'] = 'asc';
                }
            }
        }
        $get['sort'] = $sortPrefix . $attribute;
        $resultInfo['http_query'] = $this->getHttpQuery($get);
        return $resultInfo;
    }

    /**
     * @param $params
     * @param bool $new
     * @return string
     */
    private function getHttpQuery($params, $new = true)
    {
        return ($new ? '?' : '') . http_build_query($params);
    }
}
