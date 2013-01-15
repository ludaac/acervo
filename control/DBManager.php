<?php
require_once('DBConnector.php');

// Procedimientos almacenados.
define('SELECT_ALL_USERS_INFO', 0);
define('QUERY_BOOKS', 1);
define('USER_LOGIN', 2);
define('ADD_NEW_CLASSIFICATION', 3);
define('GET_ALL_CLASSIFICATIONS', 4);
define('GET_ALL_BOOKS', 5);
define('ADD_NEW_BOOK', 6);
define('GET_ALL_BORROWS', 7);
define('GET_ALL_USERS', 8);
define('UPDATE_CLASSIFICATION', 9);
define('GET_CLASSIFICATION_BY_NUMBER', 10);
define('ADD_NEW_USER', 11);
define('GET_BOOK_BY_NUMBER', 12);
define('UPDATE_BOOK', 13);
define('GET_USER_BY_NUMBER', 14);
define('UPDATE_USER', 15);
define('SELECT_COPY_FROM_NUMBER', 16);
define('ADD_BORROW', 17);
define('ADD_USER', 18);
define('TERMINATE_BORROW', 19);

define('GET_COUNT_FROM_TABLE', 20);
define('GET_SELECT_PAGE', 21);
define('CHANGE_BOOK_CLASSIFICATION', 22);
define('ADD_COPY_TO_BOOK', 23);
define('GET_COPY_FROM_BOOK_ID', 24);
define('CHANGE_USER_PASSWORD', 25);
define('FILTER_BOOKS', 26);
define('FILTER_BOOKS_COUNT', 27);
define('CHECK_BORROWS', 28);

define('GET_CLASS_PAGES', 29);
define('GET_BOOK_PAGES', 30);
define('GET_BORROW_PAGES', 31);
define('GET_USERS_PAGES', 32);
define('GET_CLASS_COUNT', 33);
define('GET_BOOK_COUNT', 34);
define('GET_BORROW_COUNT', 35);
define('GET_USERS_COUNT', 36);
define('FILTER_BOOKS_PAGE_COUNT', 37);
define('FILTER_BOOKS_PAGE', 38);

define('DELETE_FROM_BOOK', 39);

// Constantes.
define('NO_CONNECTION_FOUND', -1);

define('STATEMENT_SUCCESS', 100);
define('PRIMARY_KEY_DUPLICATED', 150);
define('ILLEGAL_PARAMETER', 175);
define('INSERTED_NULL', 180);
define('STATEMENT_FAIL', 200);

/**
 * 
 */
class DBManager {
    // Procedimientos almacenados definidos.
    public static $proc = array(
        SELECT_ALL_USERS_INFO => 'getUserInfo()',
        QUERY_BOOKS => 'getBookCard(:keys)',
        USER_LOGIN => 'loginUser(:uname, :upswd)',
        ADD_NEW_CLASSIFICATION => 'newClass(:cmain, :csub, :cname)',
        GET_ALL_CLASSIFICATIONS => 'getAllClass()',
        GET_ALL_BOOKS => 'getAllBooks()',
        ADD_NEW_BOOK => 'newBook(:code, :isbn, :title, :author, :edit, :ppl, :pyr, :ed, :clss, :ncp)',
        GET_ALL_BORROWS => 'getAllBorrows()',
        GET_ALL_USERS => 'getAllUsers()',
        GET_CLASSIFICATION_BY_NUMBER => 'getClassById(:id)',
        UPDATE_CLASSIFICATION => 'updateClass(:cid, :cmain, :csub, :cname)',
        ADD_NEW_USER => 'addUser(:name, :uname, :upswd)',
        UPDATE_BOOK => 'updateBook(:idb, :code, :isbn, :title, :author, :edit, :ppl, :pyr, :ed)',
        GET_BOOK_BY_NUMBER => 'getBookByNum(:num)',
        GET_USER_BY_NUMBER => 'getUserByNum(:num)',
        UPDATE_USER => 'updateUser(:id, :usfname, :usuname, "", :stat)',
        SELECT_COPY_FROM_NUMBER => 'getCopyByNum(:num)',
        ADD_BORROW => 'addBorrow(:memcd, :memnm, :findt, :uid, :cpid)',
        ADD_USER => 'addUser(:name, :uname, :upswd)',
        TERMINATE_BORROW => 'terminateBorrow(:id)',
		GET_COUNT_FROM_TABLE => 'getTableCount(:tblName)',
		GET_SELECT_PAGE => 'getTablePage(:tblName, :page)',
		CHANGE_BOOK_CLASSIFICATION => 'changeBookClass(:idb, :idc)',
		ADD_COPY_TO_BOOK => 'addCopy(:idb, :ncp)',
		GET_COPY_FROM_BOOK_ID => 'getCopyBook(:idb)',
		CHANGE_USER_PASSWORD => 'changePass(:id, :npass)',
		FILTER_BOOKS => 'filterBooks(:keys, :page)',
		FILTER_BOOKS_COUNT => 'filterBooksCount(:keys)',
		CHECK_BORROWS => 'check_borrow()',
		GET_CLASS_PAGES => 'getClassPage(:page)',
		GET_BOOK_PAGES => 'getBookPage(:page)',
		GET_BORROW_PAGES => 'getBorrowPage(:page)',
		GET_USERS_PAGES => 'getUserPage(:page)',
		GET_CLASS_COUNT => 'getClassCount()',
		GET_BOOK_COUNT => 'getBooksCount()',
		GET_BORROW_COUNT => 'getBorrowCount()',
		GET_USERS_COUNT => 'getUserCount()',
		FILTER_BOOKS_PAGE => 'filterBooksPage(:keys, :page)',
		FILTER_BOOKS_PAGE_COUNT => 'filterBooksPageCount(:keys)',
		DELETE_FROM_BOOK => 'deleteFromBook(:rid)'
    );

    /**
     *
     * @param type $paramArray
     * @return type 
     */
    public static function cleanParams($paramArray) {
        if (!is_array($paramArray)) {
            return false;
        }

        $cleanParams = array();
        foreach ($paramArray as $key => $value) {
            $nKey = ':' . $key;
            $cleanParams[$nKey] = $value;
        }

        return $cleanParams;
    }

    /**
     * 
     * @param type $nProc
     * @param type $paramArray
     * @param type $select 
     * @return type
     */
    public static function dbexecute($nProc, $paramArray = false, $select = false) {
        $connector = new DBConnector();
        $conn = $connector->getConnection();
        $result = false;

        if ($conn == null) {
            $result = NO_CONNECTION_FOUND;
            return $result;
        }

        $stmt = $conn->prepare('CALL ' . self::$proc[$nProc]);
        $status = self::catchDBError($stmt->errorCode());

        if ($status != STATEMENT_SUCCESS) {
            //print_r($stmt->errorInfo());
            //$result = false;
            $result = $status;
            
            $stmt->closeCursor();
            $conn = null;
            
            return $result;
        }

        if ($paramArray) {
            $paramArray = self::cleanParams($paramArray);

            foreach ($paramArray as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
        }

        $stmt->execute();
        $status = self::catchDBError($stmt->errorCode());

        if ($status != STATEMENT_SUCCESS) {
            //print_r($stmt->errorInfo());
            //$result = false;
            $result = $status;
            
            $stmt->closeCursor();
            $conn = null;
            return $result;
        }

        $result = STATEMENT_SUCCESS;
        
        if ($select == true) {
            $result = self::getResultSet($stmt);
        }

        $stmt->closeCursor();
        $conn = $connector = null;
        return $result;
    }

    /**
     *
     * @param type $sqlstate
     * @return string 
     */
    public static function catchDBError($sqlstate) {
        $classVal = substr($sqlstate, 0, 2);
        if ($classVal == '00' || $sqlstate == '') {
            return STATEMENT_SUCCESS;
        } else if ($classVal == '23000') {
			return INSERTED_NULL;
		} else if ($classVal == '23') {
            return PRIMARY_KEY_DUPLICATED;
        } else {
            return STATEMENT_FAIL;
        }
        // 1264 - Out of range...
    }

    /**
     *
     * @param type $stmt Executed statement.
     * @return type 
     */
    private static function getResultSet($stmt) {
        $rs = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rs[] = array_map('utf8_encode', $row);
        }
        return $rs;
    }

}

if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}
?>
