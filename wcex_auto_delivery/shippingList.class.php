<?php
class dataList
{
	var $table;			//テーブル名
	var $rows;			//データ
	var $action;		//アクション
	var $startRow;		//表示開始行番号
	var $maxRow;		//最大表示行数
	var $currentPage;	//現在のページNo
	var $firstPage;		//最初のページNo
	var $previousPage;	//前のページNo
	var $nextPage;		//次のページNo
	var $lastPage;		//最終ページNo
	var $naviMaxButton;	//ページネーション・ナビのボタンの数
	var $dataTableNavigation;	//ナヴィゲーションhtmlコード
	var $arr_period;	//表示データ期間
	var $arr_search;	//サーチ条件
	var $searchSql;		//簡易絞込みSQL
	var $searchSwitchStatus;	//サーチ表示スイッチ
	var $columns;		//データカラム
	var $sortColumn;	//現在ソート中のフィールド
	var $sortOldColumn;
	var $sortSwitchs;	//各フィールド毎の昇順降順スイッチ
	var $userHeaderNames;	//ユーザー指定のヘッダ名
	var $action_status, $action_message;
	var $pageLimit;		//ページ制限

	//Constructor
	function dataList( $tableName, $arr_column )
	{
		$this->table = $tableName;
		$this->columns = $arr_column;
		$this->rows = array();

		$this->maxRow = 30;
		$this->naviMaxButton = 11;
		$this->firstPage = 1;
		$this->action_status = 'none';
		$this->action_message = '';
		$this->pageLimit = 'on';

		$this->SetParamByQuery();

		$this->arr_period = array( __('This month', 'usces'), __('Last month', 'usces'), __('The past one week', 'usces'), __('Last 30 days', 'usces'), __('Last 90days', 'usces'), __('All', 'usces') );
	}

	function MakeTable()
	{
		$this->SetParam();

		switch( $this->action ) {

		case 'searchIn':
			$this->SearchIn();
			$res = $this->GetRows();
			break;

		case 'searchOut':
			$this->SearchOut();
			$res = $this->GetRows();
			break;

		case 'changeSort':
			$res = $this->GetRows();
			break;

		case 'changePage':
			$res = $this->GetRows();
			break;

		case 'refresh':
		default:
			$this->SetDefaultParam();
			$res = $this->GetRows();
			break;
		}

		$this->SetNavi();
		$this->SetHeaders();
		$this->SetSESSION();

		if( $res ) {
			return TRUE;

		} else {
			return FALSE;
		}
	}

	//DefaultParam
	function SetDefaultParam()
	{
		unset($_SESSION[$this->table]);
		$this->startRow = 0;
		$this->currentPage = 1;
		if( isset($_SESSION[$this->table]['arr_search']) ) {
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
		} else {
			$this->arr_search = array( 'period'=>'5', 'column'=>'', 'word'=>'' );
		}
		if( isset($_SESSION[$this->table]['searchSwitchStatus']) ) {
			$this->searchSwitchStatus = $_SESSION[$this->table]['searchSwitchStatus'];
		} else {
			$this->searchSwitchStatus = 'OFF';
		}
		$this->searchSql = '';
		$this->sortColumn = 'deco_id';
		foreach( $this->columns as $value ) {
			$this->sortSwitchs[$value] = 'DESC';
		}
		$this->SetTotalRow();
	}

	function SetParam()
	{
		$this->startRow = ( $this->currentPage - 1 ) * $this->maxRow;
	}

	function SetParamByQuery()
	{
		if( isset($_REQUEST['changePage']) ) {

			$this->action = 'changePage';
			$this->currentPage = $_REQUEST['changePage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchSwitchStatus = $_SESSION[$this->table]['searchSwitchStatus'];
			$this->searchSql = $_SESSION[$this->table]['searchSql'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif( isset($_REQUEST['changeSort']) ) {

			$this->action = 'changeSort';
			$this->sortOldColumn = $this->sortColumn;
			$this->sortColumn = $_REQUEST['changeSort'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->sortSwitchs[$this->sortColumn] = $_REQUEST['switch'];
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchSql = $_SESSION[$this->table]['searchSql'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->searchSwitchStatus = $_SESSION[$this->table]['searchSwitchStatus'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} elseif( isset($_REQUEST['searchIn']) ) {

			$this->action = 'searchIn';
			$this->arr_search['column'] = isset($_REQUEST['search']['column']) ? $_REQUEST['search']['column'] : '';
			$this->arr_search['word'] = isset($_REQUEST['search']['word']) ? $_REQUEST['search']['word'] : '';
			$this->arr_search['period'] = isset($_REQUEST['search']['period']) ? (int)$_REQUEST['search']['period'] : 0;
			$this->searchSwitchStatus = isset($_REQUEST['searchSwitchStatus']) ? $_REQUEST['searchSwitchStatus'] : '';
			$this->currentPage = 1;
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];

		} elseif( isset($_REQUEST['searchOut']) ) {

			$this->action = 'searchOut';
			$this->arr_search['column'] = '';
			$this->arr_search['word'] = '';
			$this->arr_search['period'] = $_SESSION[$this->table]['arr_search']['period'];
			$this->searchSwitchStatus = isset($_REQUEST['searchSwitchStatus']) ? $_REQUEST['searchSwitchStatus'] : '';
			$this->currentPage = 1;
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];

		} elseif( isset($_REQUEST['refresh']) ) {

			$this->action = 'refresh';
			$this->currentPage = $_SESSION[$this->table]['currentPage'];
			$this->sortColumn = $_SESSION[$this->table]['sortColumn'];
			$this->sortSwitchs = $_SESSION[$this->table]['sortSwitchs'];
			$this->userHeaderNames = $_SESSION[$this->table]['userHeaderNames'];
			$this->searchSql = $_SESSION[$this->table]['searchSql'];
			$this->arr_search = $_SESSION[$this->table]['arr_search'];
			$this->searchSwitchStatus = $_SESSION[$this->table]['searchSwitchStatus'];
			$this->totalRow = $_SESSION[$this->table]['totalRow'];
			$this->selectedRow = $_SESSION[$this->table]['selectedRow'];

		} else {

			$this->action = 'default';
		}
	}

	//GetRows
	function GetRows()
	{
		global $wpdb;
		$meta_table = $wpdb->prefix.'usces_order_meta';
		$regular_table = $wpdb->prefix.'usces_regular';
		$where = $this->GetWhere();
		$order = ' ORDER BY `'.$this->sortColumn.'` '.$this->sortSwitchs[$this->sortColumn];

		$query = $wpdb->prepare(
			"SELECT ID, meta1.meta_value AS deco_id, IFNULL(reg_id, meta2.meta_value) AS reg_id, DATE_FORMAT(order_date, %s) AS date, 
				order_delidue_date AS delidue_date, order_delivery_date AS delivery_date, mem_id, 
				CONCAT(order_name1, ' ', order_name2) AS name, order_delivery_method AS delivery_method, 
				order_payment_name AS payment_name, order_status, IFNULL(meta3.meta_value, '') AS acting_zeus_card 
			FROM {$this->table} 
			LEFT JOIN {$meta_table} AS meta1 ON ID = meta1.order_id AND meta1.meta_key = 'dec_order_id' 
			LEFT JOIN {$meta_table} AS meta2 ON ID = meta2.order_id AND meta2.meta_key = 'regular_id' 
			LEFT JOIN {$meta_table} AS meta3 ON ID = meta3.order_id AND meta3.meta_key = 'acting_zeus_card' 
			LEFT JOIN {$regular_table} ON ID = reg_order_id ",
			'%Y-%m-%d %H:%i'
		);

		$query .= $where.$order;
		$wpdb->show_errors();

		$rows = $wpdb->get_results( $query, ARRAY_A );
		$this->selectedRow = count($rows);
		if( $this->pageLimit == 'off' ) {
			$this->rows = (array)$rows;
		} else {
			$this->rows = array_slice((array)$rows, $this->startRow, $this->maxRow);
		}
		foreach( $this->rows as $index => $row ) {
			$meta_value = unserialize($row['acting_zeus_card']);
			if( is_array($meta_value) ) {
				if( array_key_exists( 'settltment_status', $meta_value) ) {
					$this->rows[$index]['settltment_status'] = $meta_value['settltment_status'];
				} else {
					$this->rows[$index]['settltment_status'] = '';
				}
			} else {
				$this->rows[$index]['settltment_status'] = '';
			}
		}
		return $this->rows;
	}

	function SetTotalRow()
	{
		global $wpdb;
		$query = "SELECT COUNT(ID) AS ct FROM {$this->table} WHERE order_status NOT LIKE '%completion%' ";
		$res = $wpdb->get_var( $query );
		$this->totalRow = $res;
	}

	function GetWhere()
	{
		$str = " WHERE order_status NOT LIKE '%completion%' ";
		if( $str == '' && $this->searchSql != '' ) {
			$str = ' HAVING '.$this->searchSql;
		} else if( $str != '' && $this->searchSql != '' ) {
			$str .= ' HAVING '.$this->searchSql;
		}
		return $str;
	}

	function SearchIn()
	{
		switch( $this->arr_search['column'] ) {
		case 'ID':
			$column = 'ID';
			$this->searchSql = $column.' = '.(int)$this->arr_search['word']['ID'];
			break;
		case 'deco_id':
			$column = 'deco_id';
			$this->searchSql = $column.' LIKE '."'%".mysql_real_escape_string($this->arr_search['word']['deco_id'])."%'";
			break;
		case 'reg_id':
			$column = 'reg_id';
			$this->searchSql = $column.' = '.(int)$this->arr_search['word']['reg_id'];
			break;
		case 'date':
			$column = 'date';
			$this->searchSql = $column.' LIKE '."'%".mysql_real_escape_string($this->arr_search['word']['date'])."%'";
			break;
		case 'delidue_date':
			$column = 'order_delidue_date';
			$this->searchSql = $column.' LIKE '."'%".mysql_real_escape_string($this->arr_search['word']['delidue_date'])."%'";
			break;
		case 'delivery_date':
			$column = 'order_delivery_date';
			$this->searchSql = $column.' LIKE '."'%".mysql_real_escape_string($this->arr_search['word']['delivery_date'])."%'";
			break;
		case 'mem_id':
			$column = 'mem_id';
			$this->searchSql = $column.' = '.(int)$this->arr_search['word']['mem_id'];
			break;
		case 'name':
			$column = 'name';
			$this->searchSql = $column.' LIKE '."'%".mysql_real_escape_string($this->arr_search['word']['name'])."%'";
			break;
		case 'delivery_method':
			$column = 'delivery_method';
			$this->searchSql = $column." = '".mysql_real_escape_string($this->arr_search['word']['delivery_method'])."'";
			break;
		case 'payment_name':
			$column = 'payment_name';
			$this->searchSql = $column." = '".mysql_real_escape_string($this->arr_search['word']['payment_name'])."'";
			break;
		}
	}

	function SearchOut()
	{
		$this->searchSql = '';
	}

	function SetNavi()
	{
		$this->lastPage = ceil($this->selectedRow / $this->maxRow);
		$this->previousPage = ( $this->currentPage - 1 == 0 ) ? 1 : $this->currentPage - 1;
		$this->nextPage = ( $this->currentPage + 1 > $this->lastPage ) ? $this->lastPage : $this->currentPage + 1;

		for( $i = 0; $i < $this->naviMaxButton; $i++ ) {
			if( $i > $this->lastPage - 1 ) break;
			if( $this->lastPage <= $this->naviMaxButton ) {
				$box[] = $i + 1;
			} else {
				if( $this->currentPage <= 6 ) {
					$label = $i + 1;
					$box[] = $label;
				} else {
					$label = $i + 1 + $this->currentPage - 6;
					$box[] = $label;
					if( $label == $this->lastPage ) break;
				}
			}
		}

		$html = '';
		$html .= '<ul class="clearfix">'."\n";
		$html .= '<li class="rowsnum">'.$this->selectedRow.' / '.$this->totalRow.' '.__('cases', 'usces').'</li>'."\n";
		if( ($this->currentPage == 1) || ($this->selectedRow == 0) ) {
			$html .= '<li class="navigationStr">first&lt;&lt;</li>'."\n";
			$html .= '<li class="navigationStr">prev&lt;</li>'."\n";
		} else {
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changePage=1">first&lt;&lt;</a></li>'."\n";
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changePage='.$this->previousPage.'">prev&lt;</a></li>'."\n";
		}
		if( $this->selectedRow > 0 ) {
			for( $i = 0; $i < count($box); $i++ ) {
				if( $box[$i] == $this->currentPage ) {
					$html .= '<li class="navigationButtonSelected">'.$box[$i].'</li>'."\n";
				} else {
					$html .= '<li class="navigationButton"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changePage='.$box[$i].'">'.$box[$i].'</a></li>'."\n";
				}
			}
		}
		if( ($this->currentPage == $this->lastPage) || ($this->selectedRow == 0) ) {
			$html .= '<li class="navigationStr">&gt;next</li>'."\n";
			$html .= '<li class="navigationStr">&gt;&gt;last</li>'."\n";
		} else {
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changePage='.$this->nextPage.'">&gt;next</a></li>'."\n";
			$html .= '<li class="navigationStr"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changePage='.$this->lastPage.'">&gt;&gt;last</a></li>'."\n";
		}
		if( $this->searchSwitchStatus == 'OFF' ) {
			$html .= '<li class="rowsnum"><a style="cursor:pointer;" id="searchVisiLink" onclick="toggleVisibility(\'searchBox\');">'.__('Show the Operation field', 'usces').'</a>'."\n";
		} else {
			$html .= '<li class="rowsnum"><a style="cursor:pointer;" id="searchVisiLink" onclick="toggleVisibility(\'searchBox\');">'.__('hide the Operation field', 'usces').'</a>'."\n";
		}

		$html .= '<li class="refresh"><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&refresh">'.__('updates it to latest information', 'usces').'</a></li>'."\n";
		$html .= '</ul>'."\n";

		$this->dataTableNavigation = $html;
	}

	function SetSESSION()
	{
		$_SESSION[$this->table]['startRow'] = $this->startRow;		//表示開始行番号
		$_SESSION[$this->table]['sortColumn'] = $this->sortColumn;	//現在ソート中のフィールド
		$_SESSION[$this->table]['totalRow'] = $this->totalRow;		//全行数
		$_SESSION[$this->table]['selectedRow'] = $this->selectedRow;	//絞り込まれた行数
		$_SESSION[$this->table]['currentPage'] = $this->currentPage;	//現在のページNo
		$_SESSION[$this->table]['previousPage'] = $this->previousPage;	//前のページNo
		$_SESSION[$this->table]['nextPage'] = $this->nextPage;		//次のページNo
		$_SESSION[$this->table]['lastPage'] = $this->lastPage;		//最終ページNo
		$_SESSION[$this->table]['userHeaderNames'] = $this->userHeaderNames;//全てのフィールド
		$_SESSION[$this->table]['headers'] = $this->headers;//表示するヘッダ文字列
		$_SESSION[$this->table]['rows'] = $this->rows;			//表示する行オブジェクト
		$_SESSION[$this->table]['sortSwitchs'] = $this->sortSwitchs;	//各フィールド毎の昇順降順スイッチ
		$_SESSION[$this->table]['dataTableNavigation'] = $this->dataTableNavigation;
		$_SESSION[$this->table]['searchSql'] = $this->searchSql;
 		$_SESSION[$this->table]['arr_search'] = $this->arr_search;
		$_SESSION[$this->table]['searchSwitchStatus'] = $this->searchSwitchStatus;
	}

	function SetHeaders()
	{
		foreach( $this->columns as $key => $value ) {
			if( $value == $this->sortColumn ) {
				if( $this->sortSwitchs[$value] == 'ASC' ) {
					$str = __('[ASC]', 'usces');
					$switch = 'DESC';
				} else {
					$str = __('[DESC]', 'usces');
					$switch = 'ASC';
				}
				if( in_array($value, array('ID')) ) {
					$this->headers[$value] = $key;
				} else {
					$this->headers[$value] = '<a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changeSort='.$value.'&switch='.$switch.'"><span class="sortcolumn">'.$key.' '.$str.'</span></a>';
				}
			} else {
				$switch = $this->sortSwitchs[$value];
				if( in_array($value, array('ID')) ) {
					$this->headers[$value] = $key;
				} else {
					$this->headers[$value] = '<a href="'.get_option('siteurl').'/wp-admin/admin.php?page=usces_shippinglist&changeSort='.$value.'&switch='.$switch.'"><span>'.$key.'</span></a>';
				}
			}
		}
	}

	function GetSearchs()
	{
		return $this->arr_search;
	}

	function GetListheaders()
	{
		return $this->headers;
	}

	function GetDataTableNavigation()
	{
		return $this->dataTableNavigation;
	}

	function set_action_status( $status, $message )
	{
		$this->action_status = $status;
		$this->action_message = $message;
	}

	function get_action_status()
	{
		return $this->action_status;
	}

	function get_action_message()
	{
		return $this->action_message;
	}
}
?>