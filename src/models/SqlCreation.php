<?php

declare(strict_types=1);

namespace Htmlacademy\Models;
use Htmlacademy\exceptions\CustomExeption;

class SqlCreation {
  private $dirСsv;
	private $dirSql = "data/sql_files/";
	private $fileName;
	private $tableName;
	private $filePath;
	private $fp;

	public function __construct(string $dirСsv, string $fileName){
    $this->dirСsv = $dirСsv;
		$this->fileName = $fileName;
	}
    public function getSqlfile():string {
		$headerData =[];
		$data = [];
		$request = "";
		$qcolmn = 0;
		$dateReg = '/^[0-9]{2,4}[\-\.][0-9]{2}[\-\.][0-9]{2,4}$/';
		$numReg = "/^[1-9]{0,4}[\.]{0,1}[0-9]+$/";

        if(!substr($this->fileName,-4,4) == '.csv'){
            throw new CustomExeption("File format ' $this->fileName ' different from .csv");
           }

           if (!file_exists($this->filename)) {
            throw new CustomExeption("Файл не существует");
           }

            $this->tableName = substr($this->fileName,0,-4);
			$this->filePath = $this->dirСsv.'/'.$this->fileName;
            $this->fp = fopen($this->filePath,'r');

            if (!$this->fp) {
	            throw new CustomExeption("U can not open a file");
	        }

            rewind($this->fp);
	        $headerData = fgetcsv($this->fp);
	        $qcolmn = sizeof($headerData);

	       /* for($i=0; $i<$qcolmn; $i++){
	        	$headerData[$i] = trim($headerData[$i]);
	        }*/

	        // Считываем данные из файла в массив $data
	        foreach($this->getNextLine() as $value){
	        	$data[] = $value;
	        };
	        $data = array_filter($data);

			$request = "insert into $this->tableName (";
	        $characterMask = " \t\n\r\0\x0B";
	        $characterMask .= '﻿'; // !important! Add some unprinted symbols.

			for($i=0; $i<$qcolmn-1; $i++) {
	        	$headerData[$i] = trim($headerData[$i], $characterMask);
	        	$request .= $headerData[$i]. ", ";
	        }
			$request .= $headerData[$qcolmn-1].") \n values ";

	        foreach($data as $row){
	        	$request .= "(";

	        	for($i=0; $i<$qcolmn-1; $i++) {

	        		if ($row[$i] == null) {
	        			$request .= "null, ";
					} elseif (preg_match($dateReg, $row[$i])) {
	        			$dt = strtotime($row[$i]);
	        			$request .= "str_to_date('".date('Y-m-d',$dt)."','%Y-%m-%d '), ";
	        		} elseif (preg_match($numReg,$row[$i])) {
	        			$request .= $row[$i].', ';
	        		} else {
	        			$request .= "'".$row[$i]."', ";
	        		}
	        	}

				if ($row[$qcolmn-1] == null) {
        			$request .= "null), \n";
				} elseif (preg_match($dateReg,$row[$qcolmn-1])) {
        			$dt = strtotime($row[$qcolmn-1]);
        			$request .= "str_to_date('".date('Y-m-d',$dt)."','%Y-%m-%d ')), \n";
        		} elseif (preg_match($numReg,$row[$qcolmn-1])) {
        			$request .= $row[$qcolmn-1]."), \n";
        		} else {
        			$request .= "'".$row[$qcolmn-1]."'), \n";
        		}
	        }
	        $request = substr($request, 0,-3);
	        $request .=";";

	        fclose($this->fp);

		    // Создаём файл .sql с инструкцией для вставки данных в БД
		    $newFile = fopen($this->dirSql.$this->tableName.".sql",'w');
		    fwrite($newFile, $request);
		    fclose($newFile);

		    return $notice = " Файл $this->dirCsv/$this->fileName успешно обработан. Создан файл $this->dirSql.$this->tableName.sql <br/> \n";

	}

	private function getNextLine():?iterable {
        $result = null;
        while (!feof($this->fp)) {
            yield fgetcsv($this->fp);
        }
        return $result;
    }
    }
