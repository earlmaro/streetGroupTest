<?php

namespace App;

class Parser
{

    private $people = [];

    private $titles = ["Mr", "Mrs", "Dr", "Prof", "Mister", "Ms"];

    public function checkNameFormat($name)
    {
        if (strpos($name, 'and')) {
            // check if name has and in it, if yes, explode name and return an array
            $output = explode('and', $name);
            $count = 1;
            // start with last item in array as its most likey going to have the lastname to be used by other items
            $currentItem = end($output);
            $currentItem = trim($currentItem);
            // split current item to extract lastname
            $splitCurrentItem = explode(' ', $currentItem);
            if (count($splitCurrentItem) > 1) {
                $possibleLastname = end($splitCurrentItem);
            }
            // update record of last item 
            $this->updateJointHomeOwnersRecord($splitCurrentItem, $possibleLastname);
            // inverse loop to get other names and update record 
            while (count($output) > $count) {
                $currentItem = prev($output);
                $currentItem = trim($currentItem);
                $splitCurrentItem = explode(' ', $currentItem);
                if (!is_array(($splitCurrentItem))) {
                    $splitCurrentItem = [$splitCurrentItem];
                }
                if (count($splitCurrentItem) > 1) {
                    $possibleLastname = end($splitCurrentItem);
                }
                $this->updateJointHomeOwnersRecord($splitCurrentItem, $possibleLastname);
                $count++;
            }
        } else {
            // check if name has multiple titles
            if (strpos($name, '&')) {
                // explode to get first title
                $splitToProcessTitle = explode('&', $name);
                $firstTitle = $splitToProcessTitle[0];
                //explode agatin to get second title
                $splitToGetSecondTitle = explode(' ', trim($splitToProcessTitle[1]));
                $secondTitle = $splitToGetSecondTitle[0];
                //concatenate titles
                $title = $firstTitle . '& ' . $secondTitle;

                // array_push($this->titles, $title);
                $this->titles[] = $title;
                $splitCurrentItem = [$title, $splitToGetSecondTitle[1], $splitToGetSecondTitle[2]];
                // update record
                $this->updateLoneHomeOwnersRecord($splitCurrentItem);
            }
            $splitCurrentItem = explode(' ', $name);
            $this->updateLoneHomeOwnersRecord($splitCurrentItem);
        }
    }

    public function updateLoneHomeOwnersRecord($arr)
    {
        $result = array(
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        );

        if (isset($arr[0]) && in_array($arr[0], $this->titles)) {
            $result['title'] = $arr[0];
        }
        if (isset($arr[0]) && in_array($arr[0], $this->titles)) {
            $result['title'] = $arr[0];
        }

        if (isset($arr[1])) {
            if (isset($arr[2]) && (substr($arr[1], -1) === '.' || strlen($arr[1]) === 1)) {
                $result['initial'] = $arr[1];
            }
            if (isset($arr[2]) && (substr($arr[1], -1) !== '.' && strlen($arr[1]) !== 1)) {
                $result['first_name'] = $arr[1];
            }
            if (!isset($arr[2])) {
                $result['last_name'] = $arr[1];
            }
        }

        if (isset($arr[2])) {
            $result['last_name'] = $arr[2];
        }

        array_push($this->people, $result);
    }

    public function updateJointHomeOwnersRecord($arr, $possibleLastname = null)
    {
        $result = array(
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        );

        if (isset($arr[0]) && in_array($arr[0], $this->titles)) {
            $result['title'] = $arr[0];

            if (count($arr) === 1) {
                $result['last_name'] = $possibleLastname;
            }
        }

        if (isset($arr[1]) && $arr[1] === $possibleLastname) {
            $result['first_name'] = null;
            $result['last_name'] = $possibleLastname;
        } elseif (isset($arr[1]) && ((substr($arr[1], -1) === '.' || strlen($arr[1]) === 1))) {
            $result['initial'] = $arr[1];
        } elseif (isset($arr[1]) && $arr[1] !== $possibleLastname && substr($arr[1], -1) !== '.' && strlen($arr[1]) !== 1) {
            $result['first_name'] = $arr[1];
        }

        if (isset($arr[2]) && $arr[2] === $possibleLastname) {
            $result['last_name'] = $possibleLastname;
        }
        array_push($this->people, $result);
    }


    public function readCsv()
    {
        // csv filename
        $fileName = $_FILES["file"]["tmp_name"];
        if ($fileName === false) {
            throw new \Exception("Failed to open the file.");
        }
        if ($_FILES["file"]["size"] > 0) {

            try {
                //code...
                $file = fopen($fileName, "r");
                $importCount = 0;
                // loop through csv and parse csv data
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if ($importCount !== 0) {
                        if (!empty($column) && is_array($column)) {
                            if ($this->hasEmptyRow($column)) {
                                continue;
                            }
                        }
                        $name = $column[0];
                        // send individual names to checkNameFormat for processing
                        $processName = $this->checkNameFormat($name);
                    }
                    $importCount++;
                }
                var_dump($this->people);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    function hasEmptyRow(array $column)
    {
        $columnCount = count($column);
        $isEmpty = true;
        for ($i = 0; $i < $columnCount; $i++) {
            if (!empty($column[$i]) || $column[$i] !== '') {
                $isEmpty = false;
            }
        }
        return $isEmpty;
    }
}
