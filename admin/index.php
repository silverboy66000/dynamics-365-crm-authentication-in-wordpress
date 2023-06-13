<?php
global $wpdb;
$table_name = $wpdb->prefix . 'wp_product_logs_list_settings';

$exampleListTable = new List_Table();
$exampleListTable->prepare_items();

?>

<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php
	$exampleListTable->search_box('جستجو', 'search_id');
	$exampleListTable->display();
	?>
</div>

<?php

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class List_Table extends WP_List_Table
{

	public function prepare_items()
	{
        require_once ( ABSPATH .'wp-content/plugins/product-list-management/public/asset/jdf.php');
        $columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 25;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	public function get_columns()
	{
		$columns = array(
			'id'          => 'ID',
			'productId'       => 'نام محصول',
			'price'       => 'قیمت',
			'inventory'       => 'تعداد',
			'updateDate'       => 'وضعیت در انبار',
			'userId'        => 'نام ویرایشگر',
			'date'        => 'تاریخ ویرایش',
		);

		return $columns;
	}

	public function get_hidden_columns()
	{
		return array();
	}

	public function get_sortable_columns()
	{
		return array(
			'id' => array('id', false),
			'productId' => array('productId', false),
			'price' => array('price', false),
			'inventory' => array('inventory', false),
            'updateDate' => array('updateDate', false),
            'userId' => array('userId', false),
			'date' => array('date', false)
		);
	}

	private function table_data()
	{
		$data = array();
		global $wpdb;

		$table_name = $wpdb->prefix . 'product_logs_list_settings';
		$sql = "SELECT * FROM  $table_name ORDER BY id DESC";
		$results = $wpdb->get_results($sql);
		if (count($results) > 0) {
			foreach ($results as $res) {
				$data[] = array(
					'id'          => $res->id,
					'productId'    => $this->column_product($res->productId),
					'price'        => number_format($res->price),
					'inventory'     => $res->inventory,
					'updateDate'     => $this->column_title($res->updateDate)  ,
					'userId'       => $this->get_userdata($res->userId),
					'date'       => jdate("Y/m/d H:i:s", strtotime($res->date)),
				);
			}
		}

		return $data;
	}
    function get_userdata( $user_id ) {

        return get_user_meta($user_id)['first_name'][0].' '.get_user_meta($user_id)['last_name'][0];
    }
    function column_title($item) {

        if ($item=='outofstock')
        {
            return 'ناموجود شده';
        }
        else
        {
            return 'موجود درانبار';
        }
    }
    function column_product($item)
    {
        $product = wc_get_product( $item );
        return $product->get_title();
    }
	public function column_default( $item, $column_name )
	{
		switch( $column_name ) {
			case 'id':
			case 'productId':
			case 'price':
			case 'inventory':
			case 'updateDate':
			case 'userId':
			case 'date':
				return $item[ $column_name ];

			default:
				return print_r( $item, true ) ;
		}
	}

	private function sort_data( $a, $b )
	{
		// Set defaults
		$orderby = 'id';
		$order = 'desc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}


		$result = strcmp( $a[$orderby], $b[$orderby] );

		if($order === 'asc')
		{
			return $result;
		}

		return -$result;
	}
}



?>



