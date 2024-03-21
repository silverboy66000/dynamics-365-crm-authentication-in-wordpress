<?php
global $wpdb;
global $wp_roles;
require_once( ABSPATH . 'wp-includes/http.php' );

$noyan_crm_settings = $wpdb->prefix . 'noyan_crm_settings';
$roles = $wp_roles->get_names();
$user = wp_get_current_user();
$userid=$user->ID;
$http = _wp_http_get_object();



$productArgs = array(
	'posts_per_page' => -1,
	'post_type'      => 'product',
	'post_status'    => 'publish',
);

$categoryArgs = array(
	'taxonomy'     => 'product_cat',
	'hide_empty'     => 0,
);


$loopProducts = new WP_Query( $productArgs );
$all_categories = get_categories( $categoryArgs );

?>

<div class="wrap">
    <div id="icon-users" class="icon32"></div>
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
</div>
<?php
if (isset($_POST['client_id']))
{
	$Args = array(
		'client_id' => $_POST['client_id'],
		'resource' => $_POST['resource'],
		'username' => $_POST['username'],
		'password' => $_POST['code'],
		'client_secret' => $_POST['client_secret'],
		'grant_type' => $_POST['grant_type'],
	);
	$response= $http->post( 'https://sts.eversso.com/adfs/oauth2/token', array(
		'body'    => $Args,
		'headers' => array(
			'Content-Type' => 'application/x-www-form-urlencoded',
		),
	)
    );

	$responseData=json_decode($response['body'],true);
	$accessToken=$responseData['access_token'];
	$jwt=json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $accessToken)[1]))));

	$expToken=$jwt->exp;




	$sql = "SELECT * FROM  $noyan_crm_settings where id=1";
	$results = $wpdb->get_results($sql);
	if ($results==null)
	{
		$wpdb->insert($noyan_crm_settings, array(
				'client_id' => $_POST['client_id'],
				'resource' => $_POST['resource'],
				'username' => $_POST['username'],
				'code' => $_POST['code'],
				'client_secret' => $_POST['client_secret'],
				'grant_type' => $_POST['grant_type'],
				'exp' => $expToken,
				'access_token' => $accessToken,
			)
		);
	}
	else
	{
        $wpdb->update($noyan_crm_settings,
			array(
				'client_id' => $_POST['client_id'],
				'resource' => $_POST['resource'],
				'username' => $_POST['username'],
				'code' => $_POST['code'],
				'client_secret' => $_POST['client_secret'],
				'grant_type' => $_POST['grant_type'],
				'exp' => $expToken,
				'access_token' => $accessToken,
			),
			array(
				'id'=>1
			)
		);
	}
	?>
    <div class="postbox">
        <div class="inside">
تنظیمات با موفقیت ذخیره شد!
        </div>
    </div>
	<?php
}
$results=null;
$sqlThreshold = "SELECT * FROM  $noyan_crm_settings where id=1";
$results = $wpdb->get_row($sqlThreshold);

?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
<div class="wrap fsww">
    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab active">تنظیمات </a>
    </h2>

    <div class="postbox">
        <div class="inside">
            <form action="#" method="post">
                <div class="input-box">
                    <div class="label">
                        <span>client_id</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="client_id" type="text" value="<?php echo $results ? $results->client_id : null ?>">
                    </div>
                </div>
                <div class="input-box">
                    <div class="label">
                        <span>resource</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="resource" type="text" value="<?php echo $results ? $results->resource : null ?>">
                    </div>
                </div>
                <hr>
                <div class="input-box">
                    <div class="label">
                        <span>username</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="username" type="text" value="<?php echo $results ? $results->username : null ?>">
                    </div>
                </div>
                <div class="input-box">
                    <div class="label">
                        <span>password</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="code" type="password" value="<?php echo $results ? $results->code : null ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span>client_secret</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="client_secret" type="text" value="<?php echo $results ? $results->client_secret : null ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span>grant_type</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="grant_type" type="text" value="<?php echo $results ? $results->grant_type : null ?>">
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span>exp</span>
                    </div>
                    <div class="input">
                        <input class="input-field" name="exp" type="text" value="<?php echo $results ? $results->exp : null ?>">
                        <?php echo wp_date( 'Y/m/d - h:i:s', $results->exp, new DateTimeZone('Asia/Tehran') )?>
                    </div>
                </div>

                <div class="input-box">
                    <div class="label">
                        <span>access token</span>
                    </div>
                    <div class="input">
                        <textarea class="input-field" style="width:100%" rows="10" type="text"><?php echo $results ? $results->access_token : null ?></textarea>
                    </div>
                </div>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="ذخیرهٔ تغییرات"></p>
            </form>
        </div>
    </div>

</div>

