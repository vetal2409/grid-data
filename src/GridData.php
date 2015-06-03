<?php

/**
 * @copyright Copyright &copy; Vitali Sydorenko, 2015
 * @package grid-data
 * @version 1.0.0
 */

namespace vetal2409\grid;

class GridData
{
    public $extUrl = '';
    public $extPath = '';

    public function __construct($vendorUrl, $vendorPath) {
        $this->extUrl = $vendorUrl . '/vetal2409/grid-data';
        $this->extPath = $vendorPath . '/vetal2409/grid-data';
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
        $result .= '<link rel="stylesheet" href="' . $this->extUrl . '/assets/css/style.css"/>';
        $result .= '<script src="' . $this->extUrl . '/assets/js/jquery-1.11.1.min.js"></script>';
        $result .= '<div>';
        $result .= '<table>';
        $result .= '<thead>';
        $result .= '<tr>';
        foreach($info['labels'] as $keyL => $label) {
            $result .= '<th title="' . $info['attributes'][$keyL] . '">';
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

    /**
     * @param array $columns
     * @param array $dataProvider
     * @return mixed
     */
    private function generateData(array $columns, array $dataProvider)
    {
        $result['attributes'] = $result['labels'] = $result['rows'] = array();
        foreach ($columns as $col_key => $col_val) {
            if (is_array($col_val)) {
                $result['labels'][] = array_key_exists('label', $col_val) ? $col_val['label'] : $col_key;
                $result['attributes'][] = array_key_exists('attribute', $col_val) ? $col_val['attribute'] : $col_key;
            } else {
                $result['labels'][] = $col_val;
                $result['attributes'][] = $col_val;
            }
        }
        foreach ($dataProvider as $key => $data) {
            foreach ($columns as $k => $v) {
                if (!is_array($v)) {
                    $res = $data[$v];
                } else {
                    $lab = array_key_exists('attribute', $v) ? $v['attribute'] : $k;
                    $res = array_key_exists('value', $v) ? $v['value']($data) : $data[$lab];
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
}
