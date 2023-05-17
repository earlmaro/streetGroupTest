<?php

namespace App;

class Parser
{

    private $people = [];

    private $titles = ["Mr", "Mrs", "Dr", "Prof", "Mister", "Ms"];

    private function splitJointHomeOwnersName($name)
    {
        $output = explode('and', $name);
        $count = 1;
        $currentItem = end($output);
        $currentItem = trim($currentItem);
        $splitCurrentItem = explode(' ', $currentItem);
        if (count($splitCurrentItem) > 1) {
            $possibleLastname = end($splitCurrentItem);
        }
        $this->updateJointHomeOwnersRecord($splitCurrentItem, $possibleLastname);
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
    }

    private function splitMultipleTitlesName($name)
    {
        $splitToProcessTitle = explode('&', $name);
        $firstTitle = $splitToProcessTitle[0];
        $splitToGetSecondTitle = explode(' ', trim($splitToProcessTitle[1]));
        $secondTitle = $splitToGetSecondTitle[0];
        $title = $firstTitle . '& ' . $secondTitle;
        $this->titles[] = $title;
        $splitCurrentItem = [$title, $splitToGetSecondTitle[1], $splitToGetSecondTitle[2]];
        $this->updateLoneHomeOwnersRecord($splitCurrentItem);
    }

    private function splitLoneHomeOwnersName($name)
    {
        $splitCurrentItem = explode(' ', $name);
        $this->updateLoneHomeOwnersRecord($splitCurrentItem);
    }


    public function checkNameFormat($name)
    {
        if (strpos($name, 'and')) {
            $this->splitJointHomeOwnersName($name);
        } elseif (strpos($name, '&')) {
            $this->splitMultipleTitlesName($name);
        } else {
            $this->splitLoneHomeOwnersName($name);
        }
    }

    public function updateLoneHomeOwnersRecord($nameParts, $multipleTitle = false)
    {
        $person = array(
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        );

        if (isset($nameParts[0]) && in_array($nameParts[0], $this->titles)) {
            $person['title'] = $nameParts[0];
        }
        if (isset($nameParts[0]) && in_array($nameParts[0], $this->titles)) {
            $person['title'] = $nameParts[0];
        }

        if (isset($nameParts[1])) {
            if (isset($nameParts[2]) && (substr($nameParts[1], -1) === '.' || strlen($nameParts[1]) === 1)) {
                $person['initial'] = $nameParts[1];
            }
            if (isset($nameParts[2]) && (substr($nameParts[1], -1) !== '.' && strlen($nameParts[1]) !== 1)) {
                $person['first_name'] = $nameParts[1];
            }
            if (!isset($nameParts[2])) {
                $person['last_name'] = $nameParts[1];
            }
        }

        if (isset($nameParts[2])) {
            $person['last_name'] = $nameParts[2];
        }

        $this->people[] = $person;
    }

    public function updateJointHomeOwnersRecord($nameParts, $possibleLastname = null)
    {
        $person = array(
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        );

        if (isset($nameParts[0]) && in_array($nameParts[0], $this->titles)) {
            $person['title'] = $nameParts[0];

            if (count($nameParts) === 1) {
                $person['last_name'] = $possibleLastname;
            }
        }

        if (isset($nameParts[1]) && $nameParts[1] === $possibleLastname) {
            $person['first_name'] = null;
            $person['last_name'] = $possibleLastname;
        } elseif (isset($nameParts[1]) && ((substr($nameParts[1], -1) === '.' || strlen($nameParts[1]) === 1))) {
            $person['initial'] = $nameParts[1];
        } elseif (isset($nameParts[1]) && $nameParts[1] !== $possibleLastname && substr($nameParts[1], -1) !== '.' && strlen($nameParts[1]) !== 1) {
            $person['first_name'] = $nameParts[1];
        }

        if (isset($nameParts[2]) && $nameParts[2] === $possibleLastname) {
            $person['last_name'] = $possibleLastname;
        }
        $this->people[] = $person;
    }


    public function readCsv()
    {
        $fileName = $_FILES["file"]["tmp_name"];
        if ($_FILES["file"]["size"] > 0) {
            $file = fopen($fileName, "r");
            $importCount = 0;
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($importCount !== 0) {
                    if (!empty($column) && is_array($column)) {
                        if ($this->hasEmptyRow($column)) {
                            continue;
                        }
                    }
                    $name = $column[0];
                    $processName = $this->checkNameFormat($name);
                }
                $importCount++;
            }
            var_dump($this->people);
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
