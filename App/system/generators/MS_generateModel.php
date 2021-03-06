<?php
namespace system\generators;

class MS_generateModel
{
	public $file;
	public $template;
	public $name;
	public $columns;
	public $keys;
	public $databaseConnectionReference;

	public function basicGenerate() {
		$this->createFile();
		$this->openTemplate();
		$this->writeFile();
	}

	public function generateFromDataSet() {
		$this->createFile();
		$this->openTemplate(TRUE);
		$this->writeFile(TRUE);
	}

	private function createFile() {
		$this->file = fopen('models/' . $this->name . 'Model.php', 'w');
	}

	private function openTemplate($database = FALSE) {
		if($database === TRUE) {
			$this->template = file_get_contents('templates/modelFromDatabase.txt', TRUE);
		}
		else {
			$this->template = file_get_contents('templates/model.txt', TRUE);
		}
	}

	private function writeFile($database = FALSE) {
		$content = str_replace('$name$', $this->name, $this->template);

		if($database === TRUE) {
			$keyString               = '';
			$questionSet             = '';
			$questionMarksForColumns = '';
			$columnString            = '';
			$columnSet               = '';
			$columnAndQuestionMark   = '';
			foreach($this->keys as $key) {
				$keyString .= '$' . $key . ',';
				$questionSet .= $key . '= ?,';
			}
			foreach($this->columns as $column) {
				$columnString .= $column . ',';
				$columnSet .= '$' . $column . ',';
				$questionMarksForColumns .= '?,';
				$columnAndQuestionMark .= $column.'=?,';
			}
			$content = str_replace('$databaseConnectionSet$', $this->databaseConnectionReference, $content);
			$content = str_replace('$questionMarksForColumns$', rtrim($questionMarksForColumns, ','), $content);
			$content = str_replace('$columnAndQuestionMark$', rtrim($columnAndQuestionMark, ','), $content);
			$content = str_replace('$questionSet$', rtrim($questionSet, ','), $content);
			$content = str_replace('$keys$', rtrim($keyString, ','), $content);
			$content = str_replace('$columns$', rtrim($columnString, ','), $content);
			$content = str_replace('$values$', rtrim($columnSet, ','), $content);

		}
		fwrite($this->file, $content);
		fclose($this->file);
	}
}