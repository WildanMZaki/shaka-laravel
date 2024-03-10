<?php

namespace App\Helpers;

use Exception;

class MuwizaTable extends Muwiza
{
    private $rowsData = [];
    private $processedData = [];
    private $resultData = [];
    private $extractData = ['id'];
    private $withId = true;
    private $order = [];
    private static $defaultPlaceholder = '{data}';
    private $usedBtns = [];
    private $definedBtns = [];
    private $btnsKeys = [];

    private $testResult = [];


    public static $btnsDefault = [
        'edit' => [
            'selector' => 'btn-edit',
            'data' => [],
            'classColor' => 'btn-label-warning',
            'classIcon' => 'ti ti-pencil',
            'textColor' => 'text-warning',
            'tooltip' => 'Edit'
        ],
        'detail' => [
            'selector' => 'btn-detail',
            'data' => [],
            'classColor' => 'btn-label-info',
            'classIcon' => 'ti ti-list-details',
            'textColor' => 'text-info',
            'tooltip' => 'Detail'
        ],
        'delete' => [
            'selector' => 'btn-delete',
            'data' => [],
            'classColor' => 'btn-label-danger',
            'classIcon' => 'ti ti-trash',
            'textColor' => 'text-danger',
            'tooltip' => 'Hapus'
        ],
        'activate' => [
            'selector' => 'btn-active-control',
            'data' => [],
            'classColor' => 'btn-label-success',
            'textColor' => 'text-success',
            'tooltip' => 'Aktifkan'
        ],
        'inactivate' => [
            'selector' => 'btn-active-control',
            'data' => [],
            'classColor' => 'btn-label-danger',
            'textColor' => 'text-danger',
            'tooltip' => 'Nonaktifkan'
        ],
        'success' => [
            'selector' => 'is-btn-success',
            'data' => [],
            'classColor' => 'btn-label-success',
            'textColor' => 'text-success',
            'tooltip' => 'Success Action'
        ],
        'primary' => [
            'selector' => 'is-btn-primary',
            'data' => [],
            'classColor' => 'btn-label-primary',
            'textColor' => 'text-primary',
            'tooltip' => 'Primary Action'
        ],
        'secondary' => [
            'selector' => 'is-btn-secondary',
            'data' => [],
            'classColor' => 'btn-label-secondary',
            'textColor' => 'text-secondary',
            'tooltip' => 'Secondary Action'
        ],
        'warning' => [
            'selector' => 'is-btn-warning',
            'data' => [],
            'classColor' => 'btn-label-warning',
            'textColor' => 'text-warning',
            'tooltip' => 'Warning Action'
        ],
        'danger' => [
            'selector' => 'is-btn-danger',
            'data' => [],
            'classColor' => 'btn-label-danger',
            'textColor' => 'text-danger',
            'tooltip' => 'Danger Action'
        ],
        'info' => [
            'selector' => 'is-btn-info',
            'data' => [],
            'classColor' => 'btn-label-info',
            'textColor' => 'text-info',
            'tooltip' => 'Info Action'
        ],
        'dark' => [
            'selector' => 'is-btn-dark',
            'data' => [],
            'classColor' => 'btn-label-dark',
            'textColor' => 'text-dark',
            'tooltip' => 'Dark Btn'
        ],
        'light' => [
            'selector' => 'is-btn-light',
            'data' => [],
            'classColor' => 'btn-label-light',
            'textColor' => 'text-light',
            'tooltip' => 'Light Btn'
        ],
        'white' => [
            'selector' => 'is-btn-white',
            'data' => [],
            'classColor' => 'btn-label-white',
            'textColor' => 'text-white',
            'tooltip' => 'White Btn'
        ],
    ];

    public static function generate($rowsData, ?callable $basicProcess = null)
    {
        $instance = new self();
        $instance->rowsData = $rowsData;

        foreach ($instance->rowsData as $i => $row) {
            if ($basicProcess !== null) {
                $cols = (object)[];
                $basicProcessResult = $basicProcess($row, $cols);

                foreach ($basicProcessResult as $key => $value) {
                    $instance->rowsData[$i]->$key = $value;
                }
            }
            $instance->processedData[] = [];
        }

        return $instance;
    }

    public function withoutId()
    {
        $this->withId = false;
        $i = array_search('id', $this->extractData);

        // Hapus elemen dengan indeks yang ditemukan
        if ($i !== false) {
            unset($this->extractData[$i]);
        }
        // Reset kembali indeks array
        $this->extractData = array_values($this->extractData);
        return $this;
    }

    public function extract(array $items)
    {
        $this->extractData = array_merge($this->extractData, $items);
        return $this;
    }

    public function orderColumns(array $items)
    {
        $this->order = $items;
        return $this;
    }

    // Backup col method
    // public function col(string $columnName, string|callable $formatter, ?callable $callback = null)
    // {
    //     // Note: Callback parameter: 1. column name, 2. formatter, 3. callback(formattedValue, $row) 
    //     // $formatter: kalau string maka itu saja langsung, row selector ngambil ke columnName, kalau array selector ambil yang kedua

    //     foreach ($this->rowsData as $i => $row) {
    //         $value = is_string($formatter) ? static::$formatter($row->$columnName) : $formatter($row);
    //         $this->processedData[$i][$columnName] = $callback !== null ? $callback($value, $row) : $value;
    //     }

    //     return $this;
    // }

    // public function col(string $columnName, string|array|callable $formatter, null|string|callable $closure = null)
    // {
    //     // Note: Parameter: 1. column name, 2. formatter, 3. closure(formattedValue, $row) 
    //     // 1. Column Name, Wajib: untuk saat ini terikat dengan nama kolom di database, tapi seharusnya nanti bisa dicustom
    //     // 2. Formatter Or Callback or Format
    //     //    1. string, tidak punya '{data}' maka itu formatter function yang sudah ada sebelumnya, pastikan ketersediaan, kalau tidak ada throw error
    //     //       string, punya '{data}' maka itu format, kembalikan dengan metode str_replace the data with the $row->$columnName
    //     //    2. array, ['formatter', 'rowColumn'], pastikan punya 2 elemen, pastikan kedua elemen string
    //     //       array, ['format (has {data})', 'rowColumn']
    //     //    3. callable: just get the return
    //     // 3. Closure/Final
    //     //    1. null, default: don't do anything
    //     //    2. string: must format, must have '{data}' placeholder
    //     //    3. callback: has two param (1: formatted value from previous formatter, 2: $row object), catch the return of the closure

    //     $theFormatter = null;
    //     $theFormat = null;
    //     $select = $columnName;

    //     if (is_array($formatter)) {
    //         if (count($formatter) != 2) throw new \Exception("Formatter Length must 2 elements");
    //         $f = $formatter[0];
    //         $select = $formatter[1];
    //         if (stripos($f, $this::$defaultPlaceholder) !== false) {
    //             $theFormat = $f;
    //         } else {
    //             $theFormatter = $f;
    //         }
    //     } else if (is_string($formatter)) {
    //         if (stripos($formatter, $this::$defaultPlaceholder) !== false) {
    //             $theFormat = $formatter;
    //         } else {
    //             $theFormatter = $formatter;
    //         }
    //     } else {
    //         $theFormatter = $formatter;
    //     }

    //     $final = null;
    //     $finalFormatter = null;
    //     if (is_string($closure)) {
    //         if (stripos($closure, $this::$defaultPlaceholder) === false) throw new \Exception("String Closure must be the final formatter, it must have {$this::$defaultPlaceholder} placeholder");
    //         $finalFormatter = $closure;
    //     }

    //     foreach ($this->rowsData as $i => $row) {
    //         // $value = is_string($formatter) ? static::$formatter($row->$columnName) : $formatter($row);
    //         // $this->processedData[$i][$columnName] = $callback !== null ? $callback($value, $row) : $value;

    //         $firstValue = $row->$select;
    //         if ($theFormat) {
    //             $formattedValue = static::dataReplacer($firstValue, $theFormat);
    //         } else {
    //             $formattedValue = is_callable($theFormatter) ? $theFormatter($row) : static::$theFormatter($firstValue);
    //         }

    //         if ($closure !== null) {
    //             $lastValue = $finalFormatter ? static::dataReplacer($formattedValue, $finalFormatter) : $closure($formattedValue, $row);
    //         } else {
    //             $lastValue = $formattedValue;
    //         }

    //         $this->processedData[$i][$columnName] = $lastValue;
    //     }

    //     return $this;
    // }

    public function col(string $columnName, string|array|callable $formatter, null|string|callable $closure = null)
    {
        $theFormatter = null;
        $theFormat = null;
        $select = $columnName;

        if (is_array($formatter) && count($formatter) == 2) {
            [$f, $select] = $formatter;
            if (stripos($f, $this::$defaultPlaceholder) !== false) {
                $theFormat = $f;
            } else {
                $theFormatter = $f;
            }
        } elseif (is_string($formatter)) {
            if (stripos($formatter, $this::$defaultPlaceholder) !== false) {
                $theFormat = $formatter;
            } else {
                $theFormatter = $formatter;
            }
        } else {
            $theFormatter = $formatter;
        }

        $finalFormatter = is_string($closure) && stripos($closure, $this::$defaultPlaceholder) !== false ? $closure : null;

        foreach ($this->rowsData as $i => $row) {
            $firstValue = isset($row->$select) ? $row->$select : '';
            $formattedValue = $theFormat ? static::dataReplacer($firstValue, $theFormat) : (is_callable($theFormatter) ? $theFormatter($row) : static::$theFormatter($firstValue));
            $lastValue = $closure !== null ? ($finalFormatter ? static::dataReplacer($formattedValue, $finalFormatter) : $closure($formattedValue, $row)) : $formattedValue;

            $this->processedData[$i][$columnName] = $lastValue;
        }

        return $this;
    }


    public static function formatDefaultActionBtn($config)
    {
        // Default values
        $defaultConfig = [
            'data' => [],               // Default: []
            'url' => 'javascript:void(0)',  // Default: javascript:void(0)
            'tooltip' => null,           // Default: null
            'tooltipPlacement' => 'top',           // Default: 'top'
            'classColor' => 'btn-label-primary',  // Default: btn-label-primary
            'textColor' => 'text-primary',  // Default: text-primary
            'classIcon' => 'ti ti-pencil',       // Default: ti ti-pencil
            'selector' => 'is-btn',       // Default: ti ti-pencil
        ];

        // Merge the provided config with the default config
        $config = array_merge($defaultConfig, $config);

        // Extract variables from config
        $data = $config['data'];
        $url = $config['url'];
        $tooltip = $config['tooltip'];
        $tooltipPlacement = $config['tooltipPlacement'];
        $classColor = $config['classColor'];
        $textColor = $config['textColor'];
        $classIcon = $config['classIcon'];
        $selector = $config['selector'];

        $dataSyntax = '';
        if (!empty($data) && is_array($data)) {
            $keys = array_keys($data);
            foreach ($keys as $key) {
                $dataSyntax .= "data-$key='{$data[$key]}' ";
            }
        }

        $tooltipSyntax = "";
        if ($tooltip) {
            $tooltipSyntax .= "data-bs-toggle='tooltip' data-bs-placement='{$tooltipPlacement}' title='{$tooltip}'";
        }

        return "<a class='btn btn-icon $classColor $textColor rounded-2 me-1 $selector' href='$url' $dataSyntax $tooltipSyntax><i class='ti $classIcon m-0'></i></a>";
    }

    public static function dataReplacer(string $change, string $format): string
    {
        return str_replace(self::$defaultPlaceholder, $change, $format);
    }

    /**
     * @return string BTNS in HTML Format
     */
    public static function generateActions($btns)
    {
        return implode('', array_map([self::class, 'formatDefaultActionBtn'], $btns));
    }

    public function actions(string|array $actionOrBtns = 'actions', array|callable $btnsOrCallback = null, ?callable $closure = null)
    {
        if (is_array($actionOrBtns)) {
            $this->definedBtns = $actionOrBtns;
            $colName = 'actions';
        } else {
            $colName = $actionOrBtns;
        }

        if (is_array($btnsOrCallback)) {
            $this->definedBtns = $btnsOrCallback;
        }
        $this->getBtns();
        foreach ($this->rowsData as $i => $row) {
            if ($this->withId) {
                foreach ($this->btnsKeys as $btnKey) {
                    $this->usedBtns[$btnKey]['data']['id'] = $row->id;
                }
            }
            $fix_buttons = $closure != null ? $closure($this->usedBtns, $row) : $btnsOrCallback($this->usedBtns, $row);
            $this->processedData[$i][$colName] = $this::generateActions(array_values($fix_buttons));
        }

        return $this;
    }

    private function getBtns()
    {
        foreach ($this->definedBtns as $btn) {
            if (is_array($btn)) {
                $btnKey = array_keys($btn)[0];
                $this->btnsKeys[] = $btnKey;
                $type = $btn[$btnKey];
                $this->usedBtns[$btnKey] = $this::$btnsDefault[$type];
            } else {
                $this->usedBtns[$btn] = $this::$btnsDefault[$btn];
                $this->btnsKeys[] = $btn;
            }
        }
    }

    public function result()
    {
        $this->extractResult();
        return $this->resultData;
    }

    public function resultHTML()
    {
        $this->extractResult();
        return $this->generateHTML();
    }

    public function resultTest()
    {
        return $this->testResult;
    }

    private function extractResult()
    {
        foreach ($this->rowsData as $i => $row) {
            $this->resultData[] = [];
            foreach ($this->extractData as $item) {
                if (isset($row->$item)) {
                    $this->resultData[$i][$item] = $row->$item;
                }
            }
            $this->resultData[$i] += $this->processedData[$i];
        }
    }

    private function generateHTML()
    {
        $html = '';
        foreach ($this->resultData as $i => $rowData) {
            $row = '<tr>';
            if (empty($this->order)) {
                foreach ($rowData as $key => $value) {
                    $hide = $key == 'id' ? "class='text-white'" : "";
                    $row .= "<td $hide>$value</td>";
                }
            } else {
                foreach ($this->order as $col) {
                    $hide = $col == 'id' ? "class='text-white'" : "";
                    $value = $this->resultData[$i][$col];
                    $row .= "<td $hide>$value</td>";
                }
            }
            $row .= '</tr>';
            $html .= $row;
        }
        return $html;
    }
}
