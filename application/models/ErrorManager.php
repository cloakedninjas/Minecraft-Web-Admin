<?php

class Application_Model_ErrorManager {

	const DB_EMPTY = 1;
	const DB_CREATE = 2;
	const JAVA_NOT_FOUND = 3;
	const JAVA_VERSION = 4;

	public function getHelpFor($id) {
		switch ($id) {
			case self::DB_EMPTY:
				return 'The database was not found in the correct path or was empty. Either restore a backup or re-download the database here:';

			case self::DB_CREATE:
				return 'Ensure the web user has permission to read and write to the database file';

			case self::JAVA_NOT_FOUND:
				return 'JAva was not found on the system';

		}

		return false;
	}

}

