<?php

class Matrix {
    private $array = array();
    private $rows_num;
    private $cols_num;

    /**
     * Matrix constructor
     * 
     * @param integer $rows_num Row length
     * @param integer $cols_num Column length
     */
    public function __construct($rows_number, $cols_number) {
        $this->rows_num = $rows_number;
        $this->cols_num = $cols_number;
    }

    public function getRowLength()
    {
        return $this->rows_num;
    }

    public function setRowLength($rows_num) : self
    {
        $this->rows_num = $rows_num;

        return $this;
    }

    public function getColLength()
    {
        return $this->cols_num;
    }

    public function setColLength($cols_num)
    {
        $this->cols_num = $cols_num;

        return $this;
    }

    public function getArray() {
        return $this->array;
    } 

    public function setArray(array $array) {
        if (count($array) == $this->rows_num) {
            for ($i=0; $i < $this->rows_num; $i++) { 
                if (count($array[$i]) != $this->cols_num) {
                    throw new Exception("Invalid array");
                    exit;
                }
            }

            $this->array = $array;
        } else {
            throw new Exception("Invalid array");
            exit;
        }
    }

    /**
     * Set / add new column to the matrix
     * 
     * @param array  $column New column
     * @param string $key New column's key, default is null
     * 
     * @throws Exception If row length or column length is invalid
     */
    public function set(array $column) {
        if (count($this->array) < $this->rows_num) {
            if (count($column) == $this->cols_num) {
                $this->array[] = $column;
            } else {
                throw new Exception("Valid column length must be"
                                .$this->cols_num
                                ." but "
                                .count($column)
                                ." was given.");
            }
        } else {
            throw new Exception("Row index out of bound: Index: "
                                .count($this->array)
                                .", Size: "
                                .$this->rows_num.".");
        }
    }

    /**
     * Get value from position
     * 
     * @param int $x Row position
     * @param int $y Column position
     * 
     * @return float value at (x, y)
     */
    public function getAt(int $x, int $y): float {
        return $this->array[$x][$y];
    }

    /**
     * Check if two matrix is valid for multiplication.
     * 
     * @param Matrix $a First matrix
     * @param Matrix $b Second matrix
     * 
     * @return bool <b>true</b> if valid, else return <b>false</b>
     */
    private static function isValidForMultiplication(Matrix $a, Matrix $b): bool {
        return ($a->getColLength() == $b->getRowLength());
    }

    /**
     * Multiply 2 matrixes
     * 
     * @param Matrix $a First matrix (m x n)
     * @param Matrix $b Second matrix (n x p)
     * 
     * @return Matrix result (m x p), <b>null</b> if a->n != b->n 
     */
    public static function multiply(Matrix $a, Matrix $b): Matrix {
        if (Matrix::isValidForMultiplication($a, $b)) {

            $m = $a->getRowLength();
            $n = $a->getColLength();
            $p = $b->getColLength();

            $array = new Matrix($m, $p);
            for ($i=0; $i < $m; $i++) { 
                $column = array();
                for ($j=0; $j < $p; $j++) { 
                    $value = 0;
                    for ($k=0; $k < $n; $k++) { 
                        $value += $a->getAt($i, $k) * $b->getAt($k, $j);
                    }
                    $column[] = $value;
                }

                $array->set($column);
            }

            return $array;
        } else {
            return null;
        }
    }
}

?>