<?php
// This class is for setting up the paging variables.

class Paging {

    // defaults
    private $numberToDisplay = 0;
    private $start = 0;
    private $nextStart = 0;
    private $previous = 0;
    private $total = 0;

    private $pageStartText = '';
    private $pageEndText = '';

    public function __construct($records, $field, $total, $start = 0, $numberToDisplay = 10) {
        if(!empty($records)){
            $this->numberToDisplay = (int)  htmlspecialchars($numberToDisplay);
            
            $this->start = (int)  htmlspecialchars($start);
            $this->nextStart = $this->start + $this->numberToDisplay - 1;
            $this->previous = $this->start - $this->numberToDisplay + 1;

            $this->total = (int) htmlspecialchars($total);
            $this->nextStart = ($this->nextStart <= $this->total) ? $this->nextStart : $this->total;
            
            //paging first three letters of the columns
            $this->pageStartText = strtoupper(substr($records[array_key_first($records)][$field], 0,3));
            $this->pageEndText = strtoupper(substr($records[array_key_last($records)][$field], 0,3));

        }
    }
    //Getters
    public function getNumberToDisplay() {
        return $this->numberToDisplay;
    }

    public function getStart() {
        return $this->start;
    }

    public function getNextStart() {
        return $this->nextStart;
    }

    public function getPrevious() {
        return $this->previous;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getPageStartText() {
        return $this->pageStartText;
    }

    public function getPageEndText() {
        return $this->pageEndText;
    }
}